<?php
    $_title = 'Shop';
    include '../_base.php';
    include root('_header.php');
    
    $product_arr = $_db->query('SELECT * FROM product');
?>

<main>
    <h2>Cematrix Shop</h2><br>
    <?php foreach($product_arr as $p): 
        $id = $p->product_id;
        $name = $p->name;
        $price = $p->price;
        $desc = $p->description;
        $stock = $p->stock;
        $release_date = $p->release_date;
        $img = '/img/product_img/'. $p-> image;
    ?>
        <div>
            <img src=<?= $img ?>>
            <div>
                <?= $name ?> | RM <?= $price?>
            </div>
        </div>
    <?php endforeach ?>
</main>

<?php
    include '../_footer.php';
?>