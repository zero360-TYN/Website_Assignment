<?php
require '../_base.php';

auth();
$cart = get_cart();

if (empty($cart)) {
    redirect('/product/shoppingCart.php');
}

if (is_post()) {
    global $_user, $_db;
    $user_id = $_user->user_id;
    
    $total_amount = 0;
    $out_of_stock_items = [];
    
    $check_stm = $_db->prepare("SELECT name, price, stock FROM product WHERE product_id = ?");
    
    foreach ($cart as $id => $qty) {
        $check_stm->execute([$id]);
        $p = $check_stm->fetch();
        
        if ($p) {
            if ($qty > $p->stock) {
                $out_of_stock_items[] = "{$p->name} (Only {$p->stock} left)";
            } else {
                $total_amount += $p->price * $qty;
            }
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
        
        //insert order
        $ins_order = $_db->prepare("INSERT INTO orders (user_id, order_date, total_amount) VALUES (?, NOW(), ?)");
        $ins_order->execute([$user_id, $total_amount]);
        $order_id = $_db->lastInsertId();
        //order detail
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

        $_db->commit();

        set_cart();
        temp('info','Order Successfully'); 
        redirect('/');

    } catch (Exception $e) {
        $_db->rollBack();
    }
}

$_title = 'Checkout';
$_mainCssFileName = 'checkout';
include root('_header.php');

//grand total
$grand_total = 0;
$stm = $_db->prepare("SELECT price FROM product WHERE product_id = ?");
foreach ($cart as $id => $qty) {
    $stm->execute([$id]);
    $p = $stm->fetch();
    if ($p) $grand_total += $p->price * $qty;
}
?>

<main>
    <h2 class="checkout-title">Checkout</h2>
    
    <div class="order-summary">
        <h3>Order Summary</h3>
        <p>Total Items: <b><?= array_sum($cart) ?></b></p>
        <h2 class="pay-amount">Amount to Pay: RM <?= number_format($grand_total, 2) ?></h2>
    </div>

    <form method="POST" class="checkout-form">
        <div class="payment-section">
            <h3>Payment Details</h3>
            
            <div class="form-group">
                <label>Name on Card</label>
                <input type="text" id= "cardName"name="card_name" placeholder="e.g. John Doe" required>
            </div>
            
            <div class="form-group">
                <label>Card Number</label>
                <input type="text" id="cardNumber" name="card_number" placeholder="1234 5678 1234 5678" pattern="\d{4} \d{4} \d{4} \d{4}" maxlength="19" title="Please enter a valid 16-digit card number" required>
            </div>
            
            <div class="form-row">
                <div class="form-group half">
                    <label>Expiry Date (MM/YY)</label>
                    <input type="text" id= "expriy_date" name="expiry" placeholder="MM/YY" pattern="(0[1-9]|1[0-2])\/\d{2}" maxlength="5" title="Format: MM/YY" required>
                </div>
                <div class="form-group half">
                    <label>CVV</label>
                    <input type="text" id ="cvv" name="cvv" placeholder="123" pattern="\d{3,4}" maxlength="3" required>
                </div>
            </div>
        </div>

        <div class="checkout-actions">
            <a href="/product/shoppingCart.php" class="btn-back">Back to Cart</a>
            <button type="submit" class="btn-place-order">Pay & Place Order</button>
        </div>
    </form>
</main>

<?php
$_jsFileName = 'checkout';
include root('_footer.php');
?>