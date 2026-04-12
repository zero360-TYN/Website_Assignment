<?php
require '../_base.php';

$id = req('detail');

$stm = $_db->prepare("SELECT * FROM product WHERE product_id = ?");
$stm->execute([$id]);
$p = $stm->fetch();

if (!$p) {
    redirect('shop.php');
}

if (is_post()) {
    $action = req('action');
    
    if ($action == 'add') {
        
        $qty = req('quantity');
        $total_qty = $qty + get_quantity_from($p->product_id);
        if ($total_qty > $p->stock) {
            temp('info', "Failed: Only {$p->stock} items left in stock.");
        } else {
            add_cart($id, $qty); 
            temp('info', 'Successfully added to cart!');
            redirect('/product/shoppingCart.php'); 
        }
    }
}

$_title = "Detail - " . $p->name;
$_mainCssFileName = 'detail'; 
include root('_header.php');
?>

<main>
    <div class="breadcrumb">
        <a href="/product/shop.php">Shop</a> > <span><?= $p->name ?></span>
    </div>

    <div class="product-layout">
        <div class="product-gallery">
            <img src="/img/product_img/<?= $p->image ?>" alt="<?= $p->name ?>">
        </div>

        <div class="product-info">
            <h1 class="product-title"><?= $p->name ?></h1>
            <h2 class="product-price">RM <?= number_format($p->price, 2) ?></h2>
            
            <?php if ($p->stock > 0): ?>
                <span class="badge badge-success">In Stock (<?= $p->stock ?>)</span>
            <?php else: ?>
                <span class="badge badge-danger">Out of Stock</span>
            <?php endif; ?>

            <div class="product-desc">
                <h3>Description</h3>
                <p><?= isset($p->description) ? nl2br($p->description) : 'No description available for this product.' ?></p>
            </div>

            <form method="POST" class="add-to-cart-form">
                <input type="hidden" name="action" value="add">
                
                <div class="quantity-selector">
                    <label>Quantity：</label>
                    <button id="minusBtn" type="button" class="btn-qty-control">-</button>
                    <input type="number" id="qty" name="quantity" value="1" readonly class="input-qty">
                    <button id="plusBtn" type="button" class="btn-qty-control">+</button>
                </div>

                <?php if ($p->stock > 0): ?>
                    <button type="submit" class="btn-add-cart">Add to Cart</button>
                <?php else: ?>
                    <button type="button" class="btn-add-cart disabled" disabled>Out of Stock</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</main>

<?php 
$info = temp('info'); 
if ($info): 
?>
    <div id="systemModal" class="modal-overlay" style="display: flex; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
        <div class="modal-box" style="background: white; padding: 30px; border-radius: 8px; text-align:center;">
            <h3 style="margin-top:0;">Notice</h3>
            <p><?= nl2br($info) ?></p>
            <button type="button" onclick="document.getElementById('systemModal').style.display='none'" style="background:#007bff; color:white; padding: 8px 20px; border:none; border-radius:4px; cursor:pointer;">OK</button>
        </div>
    </div>
<?php endif; ?>

<?php 
$_jsFileName = 'detail';
include root('_footer.php'); 
?>