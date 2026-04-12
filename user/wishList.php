<?php
require '../_base.php';

$wish_id = req('add_wish');
if ($wish_id) {
    toggle_wishlist($wish_id);
    redirect('wishlist.php'); 
    exit;
}

$products = get_wishlist_items();

$_title = 'My Wishlist';
$_mainCssFileName = 'wishlist';
include root('_header.php');
?>

<main>
    <div class="search-section">
        <h2 class="page-title">My Wishlist ❤️</h2>
        <p class="search-result-text">
            You have <?= count($products) ?> item(s) in your wishlist.
        </p>
    </div>

    <?php if (empty($products)): ?>
        <div class="no-result">
            <p>Your wishlist is currently empty.</p>
            <br>
            <a href="/product/shop.php" class="btn-goto-shop">Go Shopping</a>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $p): ?>
                <div class="product-card">
                    <img src="/img/product_img/<?= $p->image ?>" alt="<?= $p->name ?>" class="product-img">
                    <h3 class="product-name"><?= $p->name ?></h3>
                    <p class="product-price">RM <?= number_format($p->price, 2) ?></p>
                    
                    <a href="?add_wish=<?= $p->product_id ?>" class="card-btn">
                        Remove from Wishlist
                    </a>
                    
                    <a href="/product/detail.php?detail=<?= $p->product_id ?>" class="card-btn">
                        View Detail
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php
include root('_footer.php');
?>