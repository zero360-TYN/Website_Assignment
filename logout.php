<?php
require_once '_base.php';

if (isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];

    $sql = "UPDATE USER_RESOURCES SET remember_token = NULL WHERE remember_token = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$token]);
    
    setcookie('remember_token', '', time() - 3600, '/');
}

logout();
?>