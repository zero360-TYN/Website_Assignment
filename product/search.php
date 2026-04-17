<?php
require '../_base.php'; 

$keyword = req('keyword');

if (!$keyword) {
    redirect('/product/shop.php'); 
    exit;
}
$stm = $_db->prepare("SELECT * FROM product WHERE name LIKE ? AND release_date <= NOW()");
$stm->execute(["%$keyword%"]);
$products = $stm->fetchAll();

$_title = "Search: " . htmlspecialchars($keyword);
$_mainCssFileName = 'search'; 
include root('_header.php');
?>

<main class="results-wrapper">
    <header class="results-header">
        <h2 class="results-title">Search Results</h2>
        <p class="results-summary">
            Found <?= count($products) ?> result(s) for "<b><?= htmlspecialchars($keyword) ?></b>"
        </p>
    </header>

    <?php if (empty($products)): ?>
        <div class="empty-state">
            <p>No products found matching your search.</p>
            <a href="/product/shop.php" class="btn-return">Back to Shop</a>
        </div>
    <?php else: ?>
        <div class="grid-container">
            <?php foreach ($products as $p): ?>
                <article class="item-card">
                    <div class="image-wrapper">
                        <img src="/img/product_img/<?= $p->image ?>" alt="<?= $p->name ?>" class="item-image">
                    </div>
                    <div class="item-details">
                        <h3 class="item-title"><?= $p->name ?></h3>
                        <p class="item-price">RM <?= number_format($p->price, 2) ?></p>
                        
                        <a href="/product/detail.php?detail=<?= $p->product_id ?>" class="action-link">View Detail</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php
include root('_footer.php');
?>