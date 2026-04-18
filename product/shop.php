<?php
$_title = 'Shop';
$_mainCssFileName = 'shop';
include '../_base.php';
include root('_header.php');

if (is_get()) {
    $wish_id = req('add_wish');
    if ($wish_id) {
        if(is_deleted($wish_id)){
            temp('info', "Failed to add to wishlist! : This product is deleted.");
            redirect('shop.php');
            exit();
        }
        $info = toggle_wishlist($wish_id);
        $stm = $_db->prepare('SELECT * FROM product WHERE product_id =?');
        $stm->execute([$wish_id]);
        $p = $stm->fetch();
        temp('info',$p->name.' '.$info.' from wishlist');
        redirect('shop.php');
        exit();
    }
}
$product_arr = $_db->query('SELECT * FROM product WHERE release_date <= NOW() AND is_deleted = 0');
if(is_post()){

    $id = req('product_id');
    $quantity =req('quantity');
    $quantity = (int)$quantity;
    if($quantity < 1){
        temp('info', "invalid quantity.");
        redirect();
        exit();
    }
    $cart = get_cart();
    if($id){
        $stm = $_db->prepare("SELECT * FROM product WHERE product_id = ?");
        $stm->execute([$id]);
        $p = $stm->fetch();
        
        $total_qty = get_quantity_from($p->product_id) + $quantity;
        if($total_qty > $p->stock){
            temp('info', "Failed: Only {$p->stock} items left in stock.");
            redirect();
            exit();
        }
        add_cart($id,$quantity);
    }
    redirect();
}
?>

<main>
    <img src="/img/cematrix.png" class="logo" alt="Logo">
    <div class="item">
        <?php foreach ($product_arr as $p):
            $id = $p->product_id;
            $name = $p->name;
            $price = $p->price;
            $desc = $p->description;
            $stock = $p->stock;
            $release_date = $p->release_date;
            $img = '/img/product_img/' . $p->image;
        ?>
            <div class="product">
                <div class="img-box">
                    <img src="<?= $img ?>" alt="<?= $name ?>">
                    <form method="get" action="/product/detail.php">
                        <div class="item-btn">
                            <button type="button" class="btn-buy" data-product-id="<?= $id ?>" data-product-name="<?= $name ?>" data-product-price="<?= $price ?>">
                                Buy
                            </button>
                            <button type="submit" name="detail" value="<?= $id ?>">
                                Detail
                            </button>
                        </div>
                    </form>
                </div>
                <?php $is_wished = is_in_wishlist($p->product_id);?>
                <a href="?add_wish=<?= $p->product_id ?>" 
                   class="btn-wishlist" 
                   style="color: <?= $is_wished ? 'red' : 'gray' ?>; text-decoration: none; font-size: 1.5rem;">
                   <?= $is_wished ? '❤️' : '🤍' ?>
                </a>
                <div class="info">
                    <?= $name ?><br>
                    RM <?= $price ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div id="buyPopup" class="popup-overlay">
        <div class="popup-content">
            <h3 id="popup-name"></h3>
            <p>Price：RM <span id="popup-price">0.00</span></p>

            <form method="POST">
                <input type="hidden" name="product_id" id="popup-product-id" value="">
                <div class="quantity-selector">
                    <label>Quantity：</label>
                    <button id="minusBtn" type="button">-</button>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" max="100">
                    <button id="plusBtn" type="button">+</button>
                </div>
                <div class="popup-actions">
                    <button type="button" class="btn-cancel" id="btn-cancel">Cancel</button>
                    <button type="submit" class="btn-confirm">add to cart</button>
                </div>
            </form>
        </div>
    </div>
</main>
<?php
$_jsFileName = 'shop';
include '../_footer.php';
?>