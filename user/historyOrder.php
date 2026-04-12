<?php
require '../_base.php';


auth();
$user_id = $_user->user_id;

$order_stm = $_db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$order_stm->execute([$user_id]);
$orders = $order_stm->fetchAll();

$item_stm = $_db->prepare("
    SELECT oi.quantity, oi.unit_price, p.name, p.image 
    FROM order_item oi 
    JOIN product p ON oi.product_id = p.product_id 
    WHERE oi.order_id = ?
");

$_title = 'Order History';
$_mainCssFileName = 'history'; 
include root('_header.php');
?>

<main>
    <h2 class="page-title">My Order History</h2>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <p>You haven't placed any orders yet.</p>
            <a href="/product/shop.php" class="btn-return">Go Shopping</a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $o): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="header-info">
                            <span class="order-id">Order #<?= $o->order_id ?></span>
                            <span class="order-date"><?= date('d M Y, H:i', strtotime($o->order_date)) ?></span>
                        </div>
                        <div class="header-total">
                            Total: RM <?= number_format($o->total_amount, 2) ?>
                        </div>
                    </div>

                    <div class="order-body">
                        <?php 
                        // 执行刚才准备好的 SQL，抓取当前订单的商品详情
                        $item_stm->execute([$o->order_id]);
                        $items = $item_stm->fetchAll();
                        
                        foreach ($items as $item): 
                            $subtotal = $item->quantity * $item->unit_price;
                        ?>
                            <div class="item-row">
                                <img src="/img/product_img/<?= $item->image ?>" class="item-img" alt="Product Image">
                                <div class="item-details">
                                    <h4 class="item-name"><?= $item->name ?></h4>
                                    <p class="item-calc">RM <?= number_format($item->unit_price, 2) ?> × <?= $item->quantity ?></p>
                                </div>
                                <div class="item-subtotal">
                                    RM <?= number_format($subtotal, 2) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php
include root('_footer.php');
?>