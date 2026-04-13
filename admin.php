<?php
require_once '_base.php';
auth();

$sql = "SELECT * FROM USER_RESOURCES WHERE id = ?";
$stmt = $_db->prepare($sql);
$stmt->execute([$_user->id]);
$currentUser = $stmt->fetch();

// Handle Update Profile
if (isset($_POST['update_profile'])) {
    $newEmail = trim($_POST['profile_email']);
    $newFullname = trim($_POST['profile_fullname']);

    $sql = "UPDATE USER_RESOURCES SET email = ?, name = ? WHERE id = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$newEmail, $newFullname, $_user->id]);

    $_user->email = $newEmail;
    $_user->name = $newFullname;
    $_SESSION['user'] = $_user;

    $currentUser->email = $newEmail;
    $currentUser->name = $newFullname;

    echo "<script>alert('Profile updated successfully!'); window.location.href='admin.php';</script>";
    exit;
}

// Handle Update Password
if (isset($_POST['update_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    $hashedCurrent = sha1($currentPassword);

    if ($hashedCurrent !== $_user->password) {
        echo "<script>alert('Current password is incorrect!'); window.location.href='admin.php';</script>";
        exit;
    }

    if (strlen($newPassword) < 4) {
        echo "<script>alert('New password must be at least 4 characters long!'); window.location.href='admin.php';</script>";
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        echo "<script>alert('New password and confirmation do not match!'); window.location.href='admin.php';</script>";
        exit;
    }

    $hashedNew = sha1($newPassword);

    $sql = "UPDATE USER_RESOURCES SET password = ? WHERE id = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$hashedNew, $_user->id]);

    $_user->password = $hashedNew;
    $_SESSION['user'] = $_user;

    echo "<script>alert('Password updated successfully!'); window.location.href='admin.php';</script>";
    exit;
}

// Handle Profile Picture Upload
if (isset($_POST['update_profile_pic'])) {
    $profilePic = $_POST['profile_pic_data'] ?? '';

    $sql = "UPDATE USER_RESOURCES SET photo = ? WHERE id = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$profilePic, $_user->id]);

    $_user->photo = $profilePic;
    $_SESSION['user'] = $_user;

    $currentUser->photo = $profilePic;

    echo "<script>alert('Profile photo updated!'); window.location.href='admin.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <div class="header">
        <div class="header-left">
            <img src="images/logo.png" style="height: 40px;" alt="Logo">
        </div>
        <div class="header-center">
            <h2 style="margin: 0;">Dashboard</h2>
        </div>
        <div class="header-right">
            <div class="dropdown">
                <img src="images/user.png" class="usericon" id="userbtn" alt="User">
                <div class="usermenu" id="usermenu">
                    <div class="menu-item" onclick="openProfileModal()">
                        <i class='bx bxs-user-circle'></i>
                        <div class="addaccount">My Profile</div>
                    </div>
                    <?php if ($_user->role === 'Admin'): ?>
                    <div class="menu-item" onclick="window.location.href='admin/users.php'">
                        <i class='bx bxs-user-detail'></i>
                        <div class="addaccount">Manage Users</div>
                    </div>
                    <?php endif; ?>
                    <div class="menu-item" onclick="logout()">
                        <div class="plus-circle">+</div>
                        <div class="addaccount">Logout</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-container">
        <div class="welcome-card">
            <h1>Welcome, <?= htmlspecialchars($currentUser->name ?? $_user->name) ?>!</h1>
            <p>You are logged in as <strong><?= htmlspecialchars($_user->email) ?></strong></p>
            <span class="role-badge <?= $_user->role === 'Admin' ? 'role-admin' : 'role-member' ?>">
                Role: <?= htmlspecialchars($_user->role) ?>
            </span>
        </div>
    </div>

    <!-- Profile Modal -->
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

                <!-- Personal Info Tab -->
                <div id="infoTab" class="tab-pane active">
                    <div class="profile-pic-section">
                        <div class="profile-pic-container">
                            <img id="profileImage" src="<?php echo isset($currentUser->photo) && $currentUser->photo !== '' ? $currentUser->photo : 'images/default-avatar.png'; ?>" alt="Profile">
                            <button class="upload-btn" id="uploadBtn">
                                <i class='bx bxs-camera'></i>
                            </button>
                        </div>
                    </div>

                    <form method="POST" action="admin.php" id="profileForm">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" value="<?php echo htmlspecialchars($currentUser->name ?? ''); ?>" disabled class="readonly-field">
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="profile_email" value="<?php echo htmlspecialchars($currentUser->email ?? ''); ?>" placeholder="Enter your email">
                        </div>

                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="profile_fullname" value="<?php echo htmlspecialchars($currentUser->name ?? ''); ?>" placeholder="Enter your full name">
                        </div>

                        <button type="submit" name="update_profile" class="update-btn">Update Profile</button>
                    </form>
                </div>

                <!-- Change Password Tab -->
                <div id="passwordTab" class="tab-pane">
                    <form method="POST" action="admin.php" id="passwordForm">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" placeholder="Enter current password">
                        </div>

                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" placeholder="Enter new password">
                        </div>

                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" placeholder="Confirm new password">
                        </div>

                        <div class="password-requirements">
                            Password must be at least 4 characters long.
                        </div>

                        <button type="submit" name="update_password" class="update-btn">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden form for profile picture -->
    <form method="POST" action="admin.php" id="profilePicForm">
        <input type="hidden" name="profile_pic_data" id="profilePicData">
        <input type="hidden" name="update_profile_pic">
    </form>

    <script>
        function logout() {
            window.location.href = 'logout.php';
        }

        document.getElementById('userbtn').onclick = function(e) {
            e.stopPropagation();
            var menu = document.getElementById('usermenu');
            if (menu) menu.classList.toggle('show');
        };

        document.onclick = function() {
            var menu = document.getElementById('usermenu');
            if (menu) menu.classList.remove('show');
        };

        function openProfileModal() {
            document.getElementById('profileModal').style.display = 'flex';
        }

        function closeProfileModal() {
            document.getElementById('profileModal').style.display = 'none';
        }

        function showTab(tabName) {
            var infoTab = document.getElementById('infoTab');
            var passwordTab = document.getElementById('passwordTab');
            var infoBtn = document.querySelector('.tab-btn[data-tab="info"]');
            var passwordBtn = document.querySelector('.tab-btn[data-tab="password"]');

            if (tabName === 'info') {
                infoTab.classList.add('active');
                passwordTab.classList.remove('active');
                infoBtn.classList.add('active');
                passwordBtn.classList.remove('active');
            } else {
                infoTab.classList.remove('active');
                passwordTab.classList.add('active');
                infoBtn.classList.remove('active');
                passwordBtn.classList.add('active');
            }
        }

        document.querySelectorAll('.tab-btn').forEach(function(btn) {
            btn.onclick = function() {
                showTab(this.getAttribute('data-tab'));
            };
        });

        document.getElementById('closeProfileModal').onclick = closeProfileModal;

        window.onclick = function(e) {
            var modal = document.getElementById('profileModal');
            if (e.target === modal) closeProfileModal();
        };

        document.getElementById('uploadBtn').onclick = function() {
            var input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/jpeg,image/png,image/jpg';
            input.onchange = function(e) {
                var file = e.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        document.getElementById('profileImage').src = event.target.result;
                        document.getElementById('profilePicData').value = event.target.result;
                        document.getElementById('profilePicForm').submit();
                    };
                    reader.readAsDataURL(file);
                }
            };
            input.click();
        };
    </script>
</body>
</html>