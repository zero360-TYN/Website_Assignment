<?php
require '../_base.php'; 

$_title = 'User Login';
include root('_header.php'); 

// 1. 准备 SQL
$stm = $_db->prepare("SELECT * FROM user WHERE email = 'member@test.com'");

// 2. 核心修复：必须执行 execute()！
$stm->execute();

$u = $stm->fetch();

// 4. 逻辑修复：先判断真假，再执行登录
if ($u) {
    login($u); 
    temp('info', 'Login successfully');
    // 测试用提示 (加了点简单的 CSS 让它在页面上更显眼)
    echo "<h3 style='color: green; text-align: center;'>测试登录成功，当前身份：" . $u['email'] . "</h3>"; 
} else {
    echo "<h3 style='color: red; text-align: center;'>测试登录失败：数据库中不存在此账号。</h3>";
}
?>

<main>
    <div class="login-container">
        <h2>Login to POP Mart</h2>
        <p>Please login to continue.</p>
    </div>
</main>

<?php
include root('_footer.php'); 
?>