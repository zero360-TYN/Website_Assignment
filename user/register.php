<?php
require '../_base.php'; 

if (is_post()) {
    $name     = req('name');
    $email    = req('email');
    $password = req('password');
    $confirm  = req('confirm');

    if (!$name) {
        $_err['name'] = 'Name is required';
    }
    
    if (!$email) {
        $_err['email'] = 'Email is required';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_err['email'] = 'Invalid email format';
    } else {
        $stm = $_db->prepare('SELECT COUNT(*) FROM user WHERE email = ?');
        $stm->execute([$email]);
        if ($stm->fetchColumn() > 0) {
            $_err['email'] = 'Email already exists!';
        }
    }
    
    if (!$password) {
        $_err['password'] = 'Password is required';
    } else if (strlen($password) < 6) {
        $_err['password'] = 'Password must be at least 6 characters';
    }

    if(!$confirm){
        $_err['confirm'] = 'Please confirm password';
    }
    else if(strlen($password) < 6){
        $_err['confirm'] = 'Password must be at least 6 characters';

    }
    else if ($password !== $confirm) {
        $_err['confirm'] = 'Passwords do not match';
    }

    if (empty($_err)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stm = $_db->prepare('INSERT INTO user (email, password, name, role) VALUES (?, ?, ?, "Member")');
        $stm->execute([$email, $hash, $name]);

        temp('info', 'Registration successful! Please login.');
        redirect('/user/login.php');
        exit;
    }
}

$_title = 'Register Member';
$_mainCssFileName = 'register';
include root('_header.php');
?>

<main class="auth-page">
    <div class="auth-left">
        <a href="/index.php">
            <img src="/img/cematrix.png" class="logo" alt="Logo">
        </a>
        <h2 class="auth-title">Register as Member</h2>
        <div class="welcome-text">Welcome to Cematrix</div>
    </div>
    <div class="auth-right">
        <form method="post" action="register.php" class="auth-form">
            <div class="auth-field">
                <label>Name</label>
                <input type="text" name="name" value="<?= $name ?? '' ?>" maxlength="50" class="auth-input">
                <?php err('name'); ?>
            </div>

            <div class="auth-field">
                <label>Email</label>
                <input type="email" name="email" value="<?= $email ?? '' ?>" maxlength="100" class="auth-input">
                <?php err('email'); ?>
            </div>

            <div class="auth-field">
                <label>Password</label>
                <input type="password" name="password" maxlength="20" class="auth-input">
                <?php err('password'); ?>
            </div>

            <div class="auth-field">
                <label>Confirm Password</label>
                <input type="password" name="confirm" maxlength="20" class="auth-input">
                <?php err('confirm'); ?>
            </div>

            <div class="auth-actions">
                <button type="submit" class="btn-submit">Register Now</button>
                <button type="reset" class="btn-reset">Clear</button>
            </div>

            <p class="auth-footer">Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>
</main>
<?php
include root('_footer.php');
?>