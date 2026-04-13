<?php
session_start();

$host = 'localhost';
$dbname = 'cematrixdb';
$username = 'root';
$password = '';

try {
    $_db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $_db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Remember Me 自动登录
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    
    $sql = "SELECT * FROM USER_RESOURCES WHERE remember_token = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user'] = $user;
    }
}
// Remember Me 自动结束 
$_user = $_SESSION['user'] ?? null;

function redirect($url = null) {
    if (!$url) $url = $_SERVER['REQUEST_URI'];
    header('Location: ' . $url);
    exit;
}

function auth() {
    global $_user;
    if (!$_user) {
        redirect('login.php');
    }
}

function logout($url = null) {
    unset($_SESSION['user']);
    if (!$url) $url = 'login.php';
    redirect($url);
}

function is_post() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function req($key, $default = null) {
    return $_REQUEST[$key] ?? $default;
}

function base($path = '') {
    return "http://localhost/php/$path";
}

function temp($key, $value = null) {
    if ($value !== null) {
        $_SESSION['temp_' . $key] = $value;
    } else {
        $val = $_SESSION['temp_' . $key] ?? null;
        unset($_SESSION['temp_' . $key]);
        return $val;
    }
}

function get_mail() {
    require_once 'lib/PHPMailer.php';
    require_once 'lib/SMTP.php';
    require_once 'lib/Exception.php';
    
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'testwebbased2025@gmail.com'; 
    $mail->Password = 'tbdy mfkr onxy xhdn';
    $mail->SMTPSecure =PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->setFrom('testwebbased2025@gmail.com', 'Your Website');
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    
    return $mail;
}
?>