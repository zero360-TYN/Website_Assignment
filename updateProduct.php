<?php
    require 'php/_base.php';

    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $release_date = $_POST['release_date'] . " 00:00:00";
    $stock = $_POST['stock'];
    $category = $_POST['category'];

    $stmt = $_db->prepare("UPDATE product SET name=?, price=?, description=?, release_date=?, stock=?, category=? WHERE product_id=?");
    $stmt->bind_param("sdssisi", $name, $price, $description, $release_date, $stock, $category, $id);
    $stmt->execute();

    echo "Success";
?>