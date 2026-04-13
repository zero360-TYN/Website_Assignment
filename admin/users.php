<?php
require_once '../_base.php';
auth();

// 只有 Admin 角色可以访问
if ($_user->role !== 'Admin') {
    redirect('../admin.php');
}

// 处理封锁用户
if (isset($_POST['block_user'])) {
    $user_id = $_POST['user_id'];
    $block_reason = trim($_POST['block_reason']);
    
    if (empty($block_reason)) {
        $block_reason = 'No reason provided';
    }
    
    $sql = "UPDATE USER_RESOURCES SET is_blocked = 1, block_reason = ?, blocked_at = NOW() WHERE id = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$block_reason, $user_id]);
    
    $success = "User has been blocked.";
}

// 处理解封用户
if (isset($_POST['unblock_user'])) {
    $user_id = $_POST['user_id'];
    
    $sql = "UPDATE USER_RESOURCES SET is_blocked = 0, block_reason = NULL, blocked_at = NULL WHERE id = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$user_id]);
    
    $success = "User has been unblocked.";
}

// 获取所有用户
$sql = "SELECT * FROM USER_RESOURCES ORDER BY id ASC";
$stmt = $_db->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/table.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #1565c0;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-active {
            background: #4caf50;
            color: white;
        }
        .status-blocked {
            background: #f44336;
            color: white;
        }
        .btn-block {
            background: #f44336;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-unblock {
            background: #4caf50;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-block:hover, .btn-unblock:hover {
            opacity: 0.8;
        }
        .reason-input {
            padding: 5px;
            width: 150px;
            margin-right: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background: #f5f5f5;
        }
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            background: #2196f3;
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <a href="../admin.php" style="text-decoration: none; color: #333;">
                <img src="../images/logo.png" style="height: 40px;" alt="Logo">
            </a>
        </div>
        <div class="header-center">
            <h2 style="margin: 0;">User Management</h2>
        </div>
        <div class="header-right">
            <div class="dropdown">
                <img src="../images/user.png" class="usericon" id="userbtn" alt="User">
                <div class="usermenu" id="usermenu">
                    <div class="menu-item" onclick="window.location.href='../admin.php'">
                        <i class='bx bxs-dashboard'></i>
                        <div class="addaccount">Back to Admin</div>
                    </div>
                    <div class="menu-item" onclick="logout()">
                        <div class="plus-circle">+</div>
                        <div class="addaccount">Logout</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <a href="../admin.php" class="back-link">← Back to Admin Panel</a>
        
        <div class="page-title">Manage Users</div>
        
        <?php if (isset($success)): ?>
            <div class="alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Block Reason</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user->id ?></td>
                        <td><?= htmlspecialchars($user->name) ?></td>
                        <td><?= htmlspecialchars($user->email) ?></td>
                        <td><span class="role-badge"><?= $user->role ?></span></td>
                        <td>
                            <?php if ($user->is_blocked == 1): ?>
                                <span class="status-badge status-blocked">Blocked</span>
                            <?php else: ?>
                                <span class="status-badge status-active">Active</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($user->block_reason ?? '-') ?></td>
                        <td>
                            <?php if ($user->is_blocked == 1): ?>
                                <!-- 解封表单 -->
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?= $user->id ?>">
                                    <button type="submit" name="unblock_user" class="btn-unblock" onclick="return confirm('Unblock this user?')">Unblock</button>
                                </form>
                            <?php else: ?>
                                <!-- 封锁表单 -->
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?= $user->id ?>">
                                    <input type="text" name="block_reason" class="reason-input" placeholder="Reason (optional)">
                                    <button type="submit" name="block_user" class="btn-block" onclick="return confirm('Block this user?')">Block</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function logout() {
            window.location.href = '../logout.php';
        }

        // User dropdown menu
        document.getElementById('userbtn').onclick = function(e) {
            e.stopPropagation();
            var menu = document.getElementById('usermenu');
            if (menu) {
                menu.classList.toggle('show');
            }
        };
        
        document.onclick = function() {
            var menu = document.getElementById('usermenu');
            if (menu) {
                menu.classList.remove('show');
            }
        };
    </script>
</body>
</html>