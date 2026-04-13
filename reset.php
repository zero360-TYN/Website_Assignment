<?php
require_once '_base.php';

$error = '';
$success = '';
$show_form = 'step1';

// 处理发送验证码
if (isset($_POST['send_code'])) {
    $email = trim($_POST['email']);
    
    $sql = "SELECT * FROM USER_RESOURCES WHERE email = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        $code = rand(100000, 999999);
        $expire = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        // 用字符串存验证码
        $sql = "UPDATE USER_RESOURCES SET reset_code = ?, reset_code_expire = ? WHERE email = ?";
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
        
        // 不管邮件成不成功，都进入next page
        $show_form = 'step2';
        $_SESSION['reset_email'] = $email;
        
    } else {
        $error = "Email not found";
        $show_form = 'step1';
    }
}

if (isset($_POST['reset_password'])) {
    $email = trim($_POST['email']);
    $code = trim($_POST['code']);
    $new_password = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    
    $show_form = 'step2'; // 出错就留在 step2
    
    if ($new_password !== $confirm) {
        $error = "Passwords do not match";
    } elseif (strlen($new_password) < 4) {
        $error = "Password must be at least 4 characters";
    } else {
        $sql = "SELECT id, email, reset_code, reset_code_expire FROM USER_RESOURCES WHERE email = ?";
        $stmt = $_db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $error = "Email not found: $email";
        } elseif ((string)$user->reset_code !== (string)$code) {
            $error = "Wrong code. DB has: [{$user->reset_code}], You entered: [$code]";
        } elseif (strtotime($user->reset_code_expire) < time()) {
            $error = "Code expired at: {$user->reset_code_expire}";
        } else {
            // 验证通过，重置密码
            $hashed = sha1($new_password);
            $sql = "UPDATE USER_RESOURCES SET password = ?, reset_code = NULL, reset_code_expire = NULL WHERE email = ?";
            $stmt = $_db->prepare($sql);
            $stmt->execute([$hashed, $email]);
            
            $success = "Password reset successful! Please login.";
            $show_form = 'login';
            unset($_SESSION['reset_email']);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="wrapper" style="justify-content: center; align-items: center; display: flex;">
        <div class="form-box" style="position: relative; width: 100%; max-width: 400px;">
            <h2>Reset Password</h2>
            
            <?php if ($error): ?>
                <div style="color: red; text-align: center; margin-bottom: 15px;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div style="color: green; text-align: center; margin-bottom: 15px;"><?= htmlspecialchars($success) ?></div>
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
                        <input type="text" name="code" required maxlength="6" 
                               style="letter-spacing: 5px; font-size: 20px;">
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
                <div style="text-align:center; margin-top:10px;">
                    <a href="reset.php" style="color:#888; font-size:13px;">← Send code again</a>
                </div>
            </div>
               
            <div class="logreg-link">
                <p><a href="login.php">Back to Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>