<?php
require_once '../_base.php';
auth('Admin');

// 处理封锁用户
if (isset($_POST['block_user'])) {
    $user_id = $_POST['user_id'];
    $block_reason = trim($_POST['block_reason']);
    
    if (empty($block_reason)) {
        $block_reason = 'No reason provided';
    }
    
    $sql = "UPDATE user SET is_blocked = 1, block_reason = ?, blocked_at = NOW() WHERE user_id = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$block_reason, $user_id]);
    
    $success = "User has been blocked.";
}

if (isset($_POST['unblock_user'])) {
    $user_id = $_POST['user_id'];
    
    $sql = "UPDATE user SET is_blocked = 0, block_reason = NULL, blocked_at = NULL WHERE user_id = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$user_id]);
    
    $success = "User has been unblocked.";
}

// 处理角色变更
if (isset($_POST['change_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['new_role'];
    
    $sql = "UPDATE user SET role = ? WHERE user_id = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$new_role, $user_id]);
    
    $success = "User role updated to " . $new_role;

    if ($user_id == $_user->user_id) {
        $_user->role = $new_role;
        $_SESSION['user'] = $_user;

        if ($new_role !== 'Admin') {
            temp('info', 'Your admin privileges have been revoked. You are now a Member.');
            redirect('/'); 
            exit;
        }
    }
    redirect(); 
    exit;
}
$users = $_db->query("SELECT * FROM user ORDER BY user_id ASC")->fetchAll();

$_title = 'Member Maintenance';
$_mainCssFileName = 'memberMaintenance';
include '../_header.php';
?>

<main>
    <div class="admin-header">
        <div>
            <h2 class="admin-title">Member Maintenance</h2>
            <p class="admin-subtitle">Manage user accounts, block/unblock users, and assign roles.</p>
        </div>
        <a href="adminDashboard.php" class="btn-back">← Back to Dashboard</a>
    </div>

    <?php if (isset($success)): ?>
        <div class="alert-success"><?= $success ?></div>
    <?php endif; ?>

    <div class="member-table-container">
        <table class="member-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Block Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user->user_id ?></td>
                    <td><?= htmlspecialchars($user->name) ?></td>
                    <td><?= htmlspecialchars($user->email) ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="user_id" value="<?= $user->user_id ?>">
                            <select name="new_role" onchange="this.form.submit()" class="role-select">
                                <option value="Member" <?= $user->role == 'Member' ? 'selected' : '' ?>>Member</option>
                                <option value="Admin" <?= $user->role == 'Admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                            <input type="hidden" name="change_role">
                        </form>
                    </td>
                    <td>
                        <?php if ($user->is_blocked == 1): ?>
                            <span class="status-badge blocked">Blocked</span>
                        <?php else: ?>
                            <span class="status-badge active">Active</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($user->block_reason ?? '-') ?></td>
                    <td>
                        <?php if ($user->user_id != $_user->user_id): ?>
                            <?php if ($user->is_blocked == 1): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?= $user->user_id ?>">
                                    <button type="submit" name="unblock_user" class="btn-unblock" onclick="return confirm('Unblock this user?')">Unblock</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?= $user->user_id ?>">
                                    <input type="text" name="block_reason" class="reason-input" placeholder="Reason">
                                    <button type="submit" name="block_user" class="btn-block" onclick="return confirm('Block this user?')">Block</button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">(You cannot block yourself)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php
include '../_footer.php';
?>