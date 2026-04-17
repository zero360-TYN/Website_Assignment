<?php
require_once '../_base.php';

$error = '';
$success = '';
$show_form = 'step1';

// 处理发送验证码
if (isset($_POST['send_code'])) {
    $email = trim($_POST['email']);
    
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        $code = rand(100000, 999999);
        $expire = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        $sql = "UPDATE user SET reset_code = ?, reset_code_expire = ? WHERE email = ?";
        $stmt = $_db->prepare($sql);
        $stmt->execute([(string)$code, $expire, $email]);
        
        try {
            $mail = get_mail();
            $mail->addAddress($user->email, $user->name);
            $mail->Subject = 'Your Password Reset Code';
            $mail->Body = "
                <h2>Password Reset Request</h2>
                <p>Your 6-digit verification code is:</p>
                <h1 style='color: #1565c0; font-size: 32px; letter-spacing: 5px;'>$code</h1>
                <p>This code will expire in 10 minutes.</p>
            ";
            $mail->send();
            $success = "Verification code sent to your email!";
        } catch (Exception $e) {
            $error = "Email failed. Your code is: $code";
        }
        
        $show_form = 'step2';
        $_SESSION['reset_email'] = $email;
        
    } else {
        $error = "Email not found";
        $show_form = 'step1';
    }
}

// 处理重置密码
if (isset($_POST['reset_password'])) {
    $email = trim($_POST['email']);
    $code = trim($_POST['code']);
    $new_password = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    
    $show_form = 'step2';
    
    if ($new_password !== $confirm) {
        $error = "Passwords do not match";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        $sql = "SELECT user_id, email, reset_code, reset_code_expire FROM user WHERE email = ?";
        $stmt = $_db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $error = "Email not found: $email";
        } elseif ((string)$user->reset_code !== (string)$code) {
            $error = "Wrong code.";
        } elseif (strtotime($user->reset_code_expire) < time()) {
            $error = "Code expired.";
        } else {
            $hashed = sha1($new_password);
            $sql = "UPDATE user SET password = ?, reset_code = NULL, reset_code_expire = NULL WHERE email = ?";
            $stmt = $_db->prepare($sql);
            $stmt->execute([$hashed, $email]);
            
            $success = "Password reset successful! Please login.";
            $show_form = 'login';
            unset($_SESSION['reset_email']);
            redirect('/user/login.php');
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="wrapper reset-wrapper">
        <div class="form-box reset-form">
            <h2>Reset Password</h2>
            
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success-message"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <!-- Step 1 -->
            <div id="step1" <?= ($show_form == 'step2') ? 'style="display:none;"' : '' ?>>
                <form method="POST">
                    <div class="input-box">
                        <input type="email" name="email" required>
                        <label>Email</label>
                        <i class='bx bxs-envelope'></i>
                    </div>
                    <button type="submit" name="send_code" class="btn">Send Verification Code</button>
                </form>
            </div>
            
            <!-- Step 2 -->
            <div id="step2" <?= ($show_form == 'step2') ? '' : 'style="display:none;"' ?>>
                <form method="POST">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['reset_email'] ?? '') ?>">
                    <div class="input-box">
                        <input type="text" name="code" required maxlength="6" class="code-input">
                        <label>6-Digit Code</label>
                        <i class='bx bxs-key'></i>
                    </div>
                    <div class="input-box">
                        <input type="password" name="new_password" required>
                        <label>New Password</label>
                        <i class='bx bxs-lock-alt'></i>
                    </div>
                    <div class="input-box">
                        <input type="password" name="confirm_password" required>
                        <label>Confirm Password</label>
                        <i class='bx bxs-lock-alt'></i>
                    </div>
                    <button type="submit" name="reset_password" class="btn">Reset Password</button>
                </form>
                <div class="back-link-container">
                    <a href="reset.php" class="resend-link">← Send code again</a>
                </div>
            </div>
               
            <div class="logreg-link">
                <p><a href="login.php">Back to Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>