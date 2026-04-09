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
    }
    
    if (!$password) {
        $_err['password'] = 'Password is required';
    } else if (strlen($password) < 6) {
        $_err['password'] = 'Password must be at least 6 characters';
    }
    
    if ($password !== $confirm) {
        $_err['confirm'] = 'Passwords do not match';
    }

    if (!isset($_err['email'])) {
        $stm = $_db->prepare('SELECT 1 FROM user WHERE email = ?');
        $stm->execute([$email]);
        if ($stm->fetch()) {
            $_err['email'] = 'Email already exists';
        }
    }
    if (empty($_err)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stm = $_db->prepare('INSERT INTO user (email, password, name, role) VALUES (?, ?, ?, "Member")');
        // $stm->execute([$email, $hash, $name]);

        temp('info', 'Registration successful! Please login.');
        redirect('/user/login.php');
    }
}

$_title = 'Register Member';
include root('_header.php');
?>

<h2>Register as Member</h2>

<form method="post" action="register.php">
    <div class="field">
        <label>Name</label>
        <input type="text" name="name" value="<?= $name ?? '' ?>" maxlength="50">
        <?php err('name'); ?>
    </div>

    <div class="field">
        <label>Email</label>
        <input type="email" name="email" value="<?= $email ?? '' ?>" maxlength="100">
        <?php err('email'); ?>
    </div>

    <div class="field">
        <label>Password</label>
        <input type="password" name="password" maxlength="20">
        <?php err('password'); ?>
    </div>

    <div class="field">
        <label>Confirm Password</label>
        <input type="password" name="confirm" maxlength="20">
        <?php err('confirm'); ?>
    </div>

    <div class="actions">
        <button type="submit">Register Now</button>
        <button type="reset">Clear</button>
    </div>
    
    <p>Already have an account? <a href="login.php">Login here</a></p>
</form>

<?php
include root('_footer.php');
?>