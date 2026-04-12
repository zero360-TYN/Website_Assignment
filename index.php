<?php
require '_base.php'; 

$_title = 'Welcome to Cematrix'; 
$_mainCssFileName = 'index'; 
include root('_header.php');

global $_user;
?>

<main>
    <div class="hero-content">
        <img src="/img/cematrix.png" alt="Logo" class="hero-logo">
        <h1 class="hero-title">Every unboxing is an unknown adventure</h1>
        <p class="hero-subtitle">Explore our latest collections and exclusive releases.</p>
        
        <div class="hero-action">
            <a href="/product/shop.php" class="btn-primary">Go Shopping →</a>
        </div>
    </div>

    <div class="auth-section">
        <?php if (!$_user): ?>
            <p>New here or returning customer?</p>
            <div class="auth-buttons">
                <a href="/user/login.php" class="btn-secondary">Login</a>
                <a href="/user/register.php" class="btn-secondary outline">Register</a>
            </div>
        <?php else: ?>
            <p>Welcome back, <b><?= htmlspecialchars($_user->name) ?></b>!</p>
            <div class="auth-buttons">
                <?php if ($_user->role === 'Admin'): ?>
                    <a href="/admin/adminDashboard.php" class="btn-secondary">Admin Dashboard</a>
                <?php else: ?>
                    <a href="/user/wishlist.php" class="btn-secondary">My Wishlist</a>
                    <a href="/user/historyOrder.php" class="btn-secondary">Order History</a>
                <?php endif; ?>
                <a href="/user/logout.php" class="btn-danger">Logout</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
include root('_footer.php');
?>