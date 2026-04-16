<?php 

    require 'php/_base.php';

    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $release_date = $_POST['release_date'] . " 00:00:00";
    $stock = $_POST['stock'];
    $category = $_POST['category'];
    $imageName = "box.png";

    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "image/" . $imageName);
    }

    $stmt = $_db->prepare("INSERT INTO product (name, price, description, release_date, stock, image, category) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdssiss", $name, $price, $description, $release_date, $stock, $imageName, $category);
    $stmt->execute();

    echo "Success";
?>