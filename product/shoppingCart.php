<?php
require '../_base.php';

if (is_post()) {
    $action= req('action');
    if($action == 'clear_all'){
        set_cart();
    }
    if($action == 'clear'){
        $id = req('product_id');
        update_cart($id,0);
    }
    if ($action == 'update') {
        $id = req('product_id');
        $qty = req('quantity');
        if($id){
            $stm = $_db->prepare("SELECT * FROM product WHERE product_id = ? AND release_date <= NOW()");
            $stm->execute([$id]);
            $p = $stm->fetch();
            if($qty > $p->stock){
                temp('info', "Product delete from cart! : Only {$p->stock} items left in stock.");
                update_cart($id,0);
                redirect();
            }
            update_cart($id, $qty); 
        }
    }
    if($action == 'checkout'){
        redirect('/product/checkout.php');
    }
    redirect();
}

$_title = 'My Shopping Cart';
$_mainCssFileName = 'cart';
include root('_header.php');
$cart = get_cart();
?>

<main>
    <h2 class="cart-title">Shopping Cart</h2>
    
    <?php if (empty($cart)): ?>
        <div class="empty-cart">
            <p>Your Shopping cart is empty!</p>
            <a href="/product/shop.php">Go to shop</a>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <tr>
                <th>Product</th>
                <th>Name</th>
                <th>Price(RM)</th>
                <th>Quantity</th>
                <th>Total(RM)</th>
                <th class="action-column">Action</th>
            </tr>

            <?php
            $grand_total = 0;
            $stm = $_db->prepare("SELECT * FROM product WHERE product_id = ?");

            foreach ($cart as $product_id => $quantity):
                $stm->execute([$product_id]);
                $p = $stm->fetch();

                if ($p):
                    $subtotal = $p->price * $quantity;
                    $grand_total += $subtotal;
            ?>
                <tr>
                    <td>
                        <img src="/img/product_img/<?= $p->image ?>" class="cart-item-img">
                    </td>
                    <td><?= $p->name ?></td>
                    <td><?= number_format($p->price, 2) ?></td>
                    <td>
                        <div class="qty-display">
                            <span><?= $quantity ?></span>
                        </div>
                    </td>
                    <td class="item-subtotal">
                        <?= number_format($subtotal, 2) ?>
                    </td>
                    <td class="action-column">
                        <form method="POST" class="form-delete">
                            <input type="hidden" name="action" value="clear">
                            <input type="hidden" name="product_id" value="<?= $product_id ?>">
                            <button type="button" class="btn-update-popup" data-id="<?= $product_id ?>" data-qty="<?= $quantity ?>">Update</button>
                            <button type="submit" class="btn-delete">Clear</button>
                        </form>
                    </td>
                </tr>
            <?php
                endif;
            endforeach;
            ?>
        </table>

        <div class="cart-summary">
            <h3>Grand Total: <span class="total-price">RM <?= number_format($grand_total, 2) ?></span></h3>
            
            <div class="cart-actions">
                <form method="post">
                    <input type="hidden" name="action" value="clear_all">
                    <button type="submit" class="btn-clear-all">Clear All</button>
                </form>
                <form method="post">
                    <button class="btn-checkout" name="action" value="checkout">Checkout</button>
                </form>
                
            </div>
        </div>
    <?php endif; ?>

    <div id="qtyPopup" class="popup-overlay">
        <div class="popup-content">
            <h3>Update Quantity</h3>
            <form method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="product_id" id="popup-id">
                
                <div class="quantity-selector">
                    <label>Quantity：</label>
                    <button id="minusBtn" type="button" class="btn-qty-control">-</button>
                    <input type="number" id="popup-qty" name="quantity" value="1" min="1" max="100" class="input-qty">
                    <button id="plusBtn" type="button" class="btn-qty-control">+</button>
                </div>
                
                <div class="popup-actions">
                    <button type="button" id="closePopup" class="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>
</main>
<?php 
//pop up menu when stock is not enough
if (isset($_SESSION['stock_error'])): 
    $info = $_SESSION['stock_error'];
?>
    <div id="systemModal" class="modal-overlay" style="display: flex;">
        <div class="modal-box">
            <div class="modal-icon">⚠️</div>
            <h3 class="modal-title">Notice</h3>
            <p class="modal-message"><?= nl2br($info) ?></p>
            <button type="button" class="btn-modal-ok" onclick="closeSystemModal()">OK</button>
        </div>
    </div>

    <script>
        function closeSystemModal() {
            document.getElementById('systemModal').style.display = 'none';
        }
    </script>
<?php unset($_SESSION['stock_error']); endif; ?>
<?php
$_jsFileName = 'cart';
include root('_footer.php');
?>