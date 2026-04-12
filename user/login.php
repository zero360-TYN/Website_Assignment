<?php
require '../_base.php'; 


$stm = $_db->query('SELECT * FROM user WHERE role = "Admin" LIMIT 1');
$user = $stm->fetch();

if ($user) {
    // 2. 调用全局登录函数 (写入 Session)
    login($user); 
    
    // 3. 提示并跳转
    temp('info', 'Logged in as Admin: ' . $user->name);
    redirect('/product/shop.php'); 
} else {
    redirect('/user/register.php');
}
exit;