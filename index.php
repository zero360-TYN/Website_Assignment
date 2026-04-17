<?php
require '_base.php'; 

$_title = 'Welcome to Cematrix'; 
$_mainCssFileName = 'index'; 
include root('_header.php');

global $_user;

$stmt = $_db->prepare("SELECT * FROM product WHERE release_date > NOW() ORDER BY release_date ASC LIMIT 8");
$stmt->execute();
$upcomingProducts = $stmt->fetchAll();
?>

<main>
    <div class="hero-content">
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
                <a href="/user/login.php" class="btn-secondary">Login/Register</a>
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

    <section class="upcoming-section">
        <h2 class="section-title">🚀 Coming Soon</h2>
        
        <?php if (count($upcomingProducts) > 0): ?>
            <div class="upcoming-grid">
                <?php foreach ($upcomingProducts as $p): ?>
                    <div class="upcoming-card">
                        <div class="img-wrapper">
                            <img src="/img/product_Img/<?= $p->image ?>" alt="<?= htmlspecialchars($p->name) ?>">
                            
                            <div class="date-badge">
                                Drops on <?= date('M d, Y', strtotime($p->release_date)) ?>
                            </div>
                        </div>
                        <div class="card-info">
                            <span class="category-tag"><?= htmlspecialchars($p->category) ?></span>
                            <h3 class="product-name"><?= htmlspecialchars($p->name) ?></h3>
                            <p class="product-price">RM <?= number_format($p->price, 2) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align:center; color:#777; margin-top:20px;">Stay tuned for our next exciting drop!</p>
        <?php endif; ?>
    </section>

</main>

<?php
include root('_footer.php');
?>