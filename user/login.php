<?php
require_once '../_base.php';

if (is_post() && isset($_POST['login_submit'])) {
    $email = $_POST['login_email'];
    $password = sha1($_POST['login_password']);
    $remember = isset($_POST['remember_me']) ? true : false;

    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && $user->is_blocked == 1) {
        $block_reason = $user->block_reason ?? 'No reason provided';
        $error = "Your account has been blocked. Reason: " . $block_reason;
    }
    elseif ($user && $user->locked_until && strtotime($user->locked_until) > time()) {
        $remaining = strtotime($user->locked_until) - time();
        $error = "Too many failed attempts. Please try again in " . $remaining . " seconds.";
    }
    elseif ($user && $user->password === $password) {

        $sql = "UPDATE user SET login_attempts = 0, locked_until = NULL WHERE user_id = ?";
        $stmt = $_db->prepare($sql);
        $stmt->execute([$user->user_id]);

        $_SESSION['user'] = $user;

        if ($remember) {
            $token = sha1(uniqid() . $user->user_id . rand() . microtime());
            $sql = "UPDATE user SET remember_token = ? WHERE user_id = ?";
            $stmt = $_db->prepare($sql);
            $stmt->execute([$token, $user->user_id]);
            setcookie('remember_token', $token, time() + 86400 * 7, '/');
        }

        redirect('/');
    }
    elseif ($user) {
        $attempts = ($user->login_attempts ?? 0) + 1;

        if ($attempts >= 3) {
            $locked_until = date('Y-m-d H:i:s', strtotime('+30 seconds'));
            $sql = "UPDATE user SET login_attempts = ?, locked_until = ? WHERE user_id = ?";
            $stmt = $_db->prepare($sql);
            $stmt->execute([$attempts, $locked_until, $user->user_id]);
            $error = "Too many failed attempts. Account locked for 30 seconds.";
        } else {
            $sql = "UPDATE user SET login_attempts = ? WHERE user_id = ?";
            $stmt = $_db->prepare($sql);
            $stmt->execute([$attempts, $user->user_id]);
            $remaining = 3 - $attempts;
            $error = "Invalid email or password. You have " . $remaining . " attempt(s) remaining.";
        }
    }
    else {
        $error = "Invalid email or password";
    }
}

// Register
if (is_post() && isset($_POST['register_submit'])) {
    $email = $_POST['reg_email'];
    $name = $_POST['reg_name'];
    $password = sha1($_POST['reg_password']);

    $sql = "SELECT COUNT(*) FROM user WHERE email = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$email]);
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        $sql = "INSERT INTO user (email, password, name, role) VALUES (?, ?, ?, 'Member')";
        $stmt = $_db->prepare($sql);
        $stmt->execute([$email, $password, $name]);
        $success = "Registration successful! Please login.";
    } else {
        $error = "Email already exists";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login & Registration</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="wrapper">
        <!-- Login Form -->
        <div class="form-box login">
            <h2>Login</h2>
            <?php if (isset($error)): ?>
                <div style="color: red; text-align: center; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="input-box">
                    <input type="email" name="login_email" required>
                    <label>Email</label>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="login_password" required>
                    <label>Password</label>
                    <i class='bx bxs-lock-alt'></i>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin: 15px 0 20px 0;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="remember_me" value="1" style="width: 16px; height: 16px; margin: 0;">
                        <span style="font-size: 14px; color: #555;">Remember Me</span>
                    </label>
                </div>

                <div class="forgot-link">
                    <a href="reset.php">Forgot Password?</a>
                </div>
                <button type="submit" name="login_submit" class="btn">Login</button>
                <div class="logreg-link">
                    <p>Don't have an account? <a href="javascript:void(0)" class="register-link">Sign Up</a></p>
                </div>
            </form>
        </div>

        <!-- Register Form -->
        <div class="form-box register">
            <h2>Sign Up</h2>
            <?php if (isset($success)): ?>
                <div style="color: green; text-align: center; margin-bottom: 15px;"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="input-box">
                    <input type="text" name="reg_name" required>
                    <label>Full Name</label>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="email" name="reg_email" required>
                    <label>Email</label>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="reg_password" required>
                    <label>Password</label>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" name="register_submit" class="btn">Sign Up</button>
                <div class="logreg-link">
                    <p>Already have an account? <a href="javascript:void(0)" class="login-link">Login</a></p>
                </div>
            </form>
        </div>

        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h2>Welcome Back!</h2>
            </div>
            <div class="toggle-panel toggle-right">
                <h2>Welcome!</h2>
                <p>Enter your personal details to get started</p>
            </div>
        </div>
    </div>

    <script src="/js/login.js"></script>
</body>
</html>