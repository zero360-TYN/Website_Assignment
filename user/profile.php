<?php
require_once '../_base.php';
auth();

$user_id = $_user->user_id; 


$sql = "SELECT * FROM user WHERE user_id = ?";
$stmt = $_db->prepare($sql);
$stmt->execute([$user_id]);
$currentUser = $stmt->fetch();

if (isset($_POST['update_profile'])) {
    $newFullname = trim($_POST['profile_fullname']);

    if (!empty($newFullname)) {
        $sql = "UPDATE user SET name = ? WHERE user_id = ?";
        $stmt = $_db->prepare($sql);
        $stmt->execute([$newFullname, $user_id]);

        // 刷新 session
        $_user->name = $newFullname;
        $_SESSION['user'] = $_user;

        temp('success', 'Profile updated successfully!');
    } else {
        temp('error', 'Name cannot be empty!');
    }
    
    redirect('profile.php');
    exit;
}

if (isset($_POST['update_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    $hashedCurrent = sha1($currentPassword);
    if ($hashedCurrent !== $_user->password) {
        temp('error', 'Current password is incorrect!');
        redirect('profile.php');
        exit;
    }

    if (strlen($newPassword) < 6) {
        temp('error', 'New password must be at least 6 characters long!');
        redirect('profile.php');
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        temp('error', 'New password and confirmation do not match!');
        redirect('profile.php');
        exit;
    }

    $hashedNew = sha1($newPassword);
    $sql = "UPDATE user SET password = ? WHERE user_id = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$hashedNew, $user_id]);

    $_user->password = $hashedNew;
    $_SESSION['user'] = $_user;

    temp('success', 'Password updated successfully!');
    redirect('profile.php');
    exit;
}

if (isset($_POST['update_profile_pic'])) {
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        
        $uploadResult = save_profile_photo($_FILES['profile_photo'], $user_id);
        
        if ($uploadResult['success']) {
            $sql = "UPDATE user SET photo = ? WHERE user_id = ?";
            $stmt = $_db->prepare($sql);
            $stmt->execute([$uploadResult['filename'], $user_id]);

            $_user->photo = $uploadResult['filename'];
            $_SESSION['user'] = $_user;

            temp('success', 'Profile photo updated successfully!');
        } else {
            temp('error', $uploadResult['message']);
        }
    } else {
        temp('error', 'No file uploaded or upload error occurred.');
    }
    redirect('profile.php');
    exit;
}

$_jsFileName = 'profile';
$success_msg = temp('success');
$error_msg = temp('error');
$_title = 'User Dashboard';
$_mainCssFileName = 'profile';
include '../_header.php';
?>

<main style="padding-top: 30px;">
    <div class="dashboard-container">
        <div class="welcome-card">
            <h1>Welcome, <?= htmlspecialchars($currentUser->name ?? $_user->name) ?>!</h1>
            <p>You are logged in as <strong><?= htmlspecialchars($_user->email) ?></strong></p>
            <span class="role-badge <?= $_user->role === 'Admin' ? 'role-admin' : 'role-member' ?>">
                Role: <?= htmlspecialchars($_user->role) ?>
            </span>
            
            <button class="update-btn" onclick="openProfileModal()" style="margin-top: 20px; width: auto; padding: 10px 20px;">
                Edit Profile
            </button>
        </div>
    </div>

    <?php if ($success_msg): ?>
        <div class="alert-message alert-success" style="position: fixed; top: 80px; right: 20px; z-index: 9999;"><?= htmlspecialchars($success_msg) ?></div>
    <?php endif; ?>
    <?php if ($error_msg): ?>
        <div class="alert-message alert-error" style="position: fixed; top: 80px; right: 20px; z-index: 9999;"><?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <div id="profileModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>My Profile</h2>
                <span class="close-modal" id="closeProfileModal">&times;</span>
            </div>
            <div class="modal-body">
                <div class="profile-tabs">
                    <button class="tab-btn active" data-tab="info">Personal Info</button>
                    <button class="tab-btn" data-tab="password">Change Password</button>
                </div>

                <div id="infoTab" class="tab-pane active">
                    <div class="profile-pic-section">
                        <div class="profile-pic-container">
                            <img id="profileImage" src="/img/user_Icon/<?= htmlspecialchars($currentUser->photo ?: 'user.png') ?>" alt="Profile">
                            <button class="upload-btn" id="uploadBtn">
                                <i class='bx bxs-camera'></i>
                            </button>
                        </div>
                    </div>

                    <form method="POST" action="profile.php" id="profileForm" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" value="<?= htmlspecialchars($currentUser->name ?? '') ?>" disabled class="readonly-field">
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" value="<?= htmlspecialchars($currentUser->email ?? '') ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="profile_fullname" value="<?= htmlspecialchars($currentUser->name ?? '') ?>" placeholder="Enter your full name" required>
                        </div>

                        <button type="submit" name="update_profile" class="update-btn">Update Profile</button>
                    </form>
                </div>

                <div id="passwordTab" class="tab-pane">
                    <form method="POST" action="profile.php" id="passwordForm">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" placeholder="Enter current password" required>
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" placeholder="Enter new password" required>
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" placeholder="Confirm new password" required>
                        </div>

                        <div class="password-requirements">
                            Password must be at least 6 characters long.
                        </div>

                        <button type="submit" name="update_password" class="update-btn">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="profile.php" id="profilePicForm" enctype="multipart/form-data" style="display: none;">
        <input type="file" name="profile_photo" id="profilePhotoInput" accept="image/*">
        <input type="hidden" name="update_profile_pic" value="1">
    </form>
</main>

<script src="/js/profile.js"></script>
<script>
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert-message');
        alerts.forEach(function(alert) {
            alert.style.display = 'none';
        });
    }, 3000);
</script>

<?php
include '../_footer.php'; 
?>