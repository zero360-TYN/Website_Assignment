<?php
require '../_base.php';

auth();
$cart = get_cart();

if (empty($cart)) {
    redirect('/');
}

$user_id = $_user->user_id;

//calc total amount
$total_amount = 0;
$stm = $_db->prepare("SELECT price FROM product WHERE product_id = ?" . " AND release_date <= NOW() AND is_deleted = 0");
foreach ($cart as $id => $qty) {
    if (!is_exists($id, 'product', 'product_id') || is_deleted($id)) {
        temp('info', "Product delete from cart! : This product is deleted.");
        unset($cart[$id]);
        set_cart($cart);
        redirect('/');
        exit();
    }
    $stm->execute([$id]);
    $p = $stm->fetch();
    if ($p) $total_amount += $p->price * $qty;
}

if (is_post() && isset($_POST['action'])) {
    
    //Add voucher
    if ($_POST['action'] === 'apply_voucher') {
        $code = trim($_POST['promo_code']);
        
        $v_stm = $_db->prepare("
            SELECT v.discount_price 
            FROM voucher v
            JOIN voucher_handle vh ON v.promo_code = vh.promo_code
            WHERE v.promo_code = ? AND vh.user_id = ? AND vh.is_used = 0 AND v.is_active = 1
        ");
        $v_stm->execute([$code, $user_id]);
        $voucher = $v_stm->fetch();

        if ($voucher) {
            $_SESSION['applied_voucher'] = [
                'code' => $code,
                'discount' => $voucher->discount_price
            ];
            temp('info', 'Voucher applied successfully!');
        } else {
            temp('info', 'Invalid, used, or expired voucher.');
        }
        redirect('/product/checkout.php');
        exit;
    }

    //remove voucher
    if ($_POST['action'] === 'remove_voucher') {
        unset($_SESSION['applied_voucher']);
        temp('info', 'Voucher removed.');
        redirect('/product/checkout.php');
        exit;
    }

    //place order
    if ($_POST['action'] === 'place_order') {
        $promo_code = $_SESSION['applied_voucher']['code'] ?? null;
        $discount_amount = $_SESSION['applied_voucher']['discount'] ?? 0;
        $grand_total = max(0, $total_amount - $discount_amount);

        //check stock
        $out_of_stock_items = [];
        $check_stm = $_db->prepare("SELECT name, stock FROM product WHERE product_id = ? AND release_date <= NOW() AND is_deleted = 0");
        foreach ($cart as $id => $qty) {
            $check_stm->execute([$id]);
            $p = $check_stm->fetch();
            if ($p && $qty > $p->stock) {
                $out_of_stock_items[] = "{$p->name} (Only {$p->stock} left)";
            }
        }

        if (!empty($out_of_stock_items)) {
            $error_msg = "Checkout failed! Insufficient stock for:\n" . implode("\n", $out_of_stock_items);
            $_SESSION['stock_error'] = $error_msg;
            redirect('/product/shoppingCart.php');
            exit; 
        }

        try {
            $_db->beginTransaction();
            
            $ins_order = $_db->prepare("INSERT INTO orders (user_id, order_date, total_amount, promo_code, discount_amount, grand_total) VALUES (?, NOW(), ?, ?, ?, ?)");
            $ins_order->execute([$user_id, $total_amount, $promo_code, $discount_amount, $grand_total]);
            $order_id = $_db->lastInsertId();
            
            $ins_detail = $_db->prepare("INSERT INTO order_item (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
            $update_stock = $_db->prepare("UPDATE product SET stock = stock - ? WHERE product_id = ?");
            $price_stm = $_db->prepare("SELECT price FROM product WHERE product_id = ?");
            
            foreach ($cart as $id => $qty) {
                $price_stm->execute([$id]);
                $p = $price_stm->fetch();
                if ($p) {
                    $ins_detail->execute([$order_id, $id, $qty, $p->price]);
                    $update_stock->execute([$qty, $id]);
                }
            }

            //update the promo code if used
            if ($promo_code) {
                $upd_voucher = $_db->prepare("UPDATE voucher_handle SET is_used = 1, used_at = NOW() WHERE user_id = ? AND promo_code = ?");
                $upd_voucher->execute([$user_id, $promo_code]);
                unset($_SESSION['applied_voucher']); 
            }

            $_db->commit();
            set_cart(); 
            temp('info', 'Order Successfully Placed!'); 
            redirect('/');
            exit;
        } catch (Exception $e) {
            $_db->rollBack();
            temp('info', 'System error during checkout: ' . $e->getMessage());
        }
    }
}

$promo_code = null;
$discount_amount = 0;

if (isset($_SESSION['applied_voucher'])) {
    $promo_code = $_SESSION['applied_voucher']['code'];
    $discount_amount = $_SESSION['applied_voucher']['discount'];
}
$grand_total = max(0, $total_amount - $discount_amount);

//for show what voucher user can use
$available_vouchers_stm = $_db->prepare("
    SELECT v.promo_code, v.discount_price 
    FROM voucher v
    JOIN voucher_handle vh ON v.promo_code = vh.promo_code
    WHERE vh.user_id = ? AND vh.is_used = 0 AND v.is_active = 1
");
$available_vouchers_stm->execute([$user_id]);
$my_vouchers = $available_vouchers_stm->fetchAll();

$_title = 'Checkout';
$_mainCssFileName = 'checkout';
include root('_header.php');
?>

<main>
    <h2 class="checkout-title">Checkout</h2>
    
    <div class="order-summary">
        <h3>Order Summary</h3>
        <p>Total Items: <b><?= array_sum($cart) ?></b></p>
        <p>Total Amount: RM <?= number_format($total_amount, 2) ?></p>

        <div class="voucher-box">
            <?php if ($promo_code): ?>
                <div class="voucher-applied">
                    <div>
                        <p class="voucher-success">✔ Voucher Applied: <?= htmlspecialchars($promo_code) ?></p>
                        <p class="voucher-discount">Discount: -RM <?= number_format($discount_amount, 2) ?></p>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action" value="remove_voucher">
                        <button type="submit" class="btn-remove">Remove</button>
                    </form>
                </div>
            <?php else: ?>
                <button type="button" class="btn-select-voucher" id="openVoucherBtn">🎟️ Select Voucher</button>
            <?php endif; ?>
        </div>

        <h2 class="pay-amount">Grand Total: RM <?= number_format($grand_total, 2) ?></h2>
    </div>

    <form method="POST" class="checkout-form">
        <input type="hidden" name="action" value="place_order">
        
        <div class="payment-section">
            <h3>Payment Details</h3>
            <div class="form-group">
                <label>Name on Card</label>
                <input type="text" id="cardName" name="card_name" placeholder="e.g. John Doe" required>
            </div>
            <div class="form-group">
                <label>Card Number</label>
                <input type="text" id="cardNumber" name="card_number" placeholder="1234 5678 1234 5678" pattern="\d{4} \d{4} \d{4} \d{4}" maxlength="19" required>
            </div>
            <div class="form-row">
                <div class="form-group half">
                    <label>Expiry Date (MM/YY)</label>
                    <input type="text" id="expriy_date" name="expiry" placeholder="MM/YY" pattern="(0[1-9]|1[0-2])\/\d{2}" maxlength="5" required>
                </div>
                <div class="form-group half">
                    <label>CVV</label>
                    <input type="text" id="cvv" name="cvv" placeholder="123" pattern="\d{3,4}" maxlength="3" required>
                </div>
            </div>
        </div>

        <div class="checkout-actions">
            <a href="/product/shoppingCart.php" class="btn-back">Back to Cart</a>
            <button type="submit" class="btn-place-order">Pay & Place Order</button>
        </div>
    </form>
</main>
            <!-------------------------------pop up menu=================================-->
<div id="voucherModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>My Vouchers</h3>
            <span class="close-modal" id ="closeVoucherBtn">&times;</span>
        </div>
        <div class="modal-body">
            <?php if (empty($my_vouchers)): ?>
                <p style="text-align:center; color:#888; padding: 20px;">You have no available vouchers right now.</p>
            <?php else: ?>
                <div class="voucher-list">
                    <?php foreach ($my_vouchers as $v): ?>
                        <div class="voucher-item">
                            <div class="voucher-info">
                                <h4><?= htmlspecialchars($v->promo_code) ?></h4>
                                <p>Discount: RM <?= number_format($v->discount_price, 2) ?></p>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="action" value="apply_voucher">
                                <input type="hidden" name="promo_code" value="<?= htmlspecialchars($v->promo_code) ?>">
                                <button type="submit" class="btn-apply-voucher">Apply</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$_jsFileName = 'checkout';
include root('_footer.php');
?>