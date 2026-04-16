<?php 
    require 'php/_base.php';

    $action = $_POST['action'] ?? '';
    $promo_code = $_POST['promo_code'];
    $discount_price = $_POST['discount_price'];

    if ($action == 'create') {
        $stmt = $_db->prepare("INSERT INTO voucher (promo_code, discount_price) VALUES (?, ?)");
        $stmt->bind_param("sd", $promo_code, $discount_price);
        $stmt->execute();
        echo "Success";
        
    } else if ($action == "delete") {
        $stmt = $_db->prepare("DELETE FROM voucher WHERE promo_code = ?");
        $stmt->bind_param("s", $promo_code);
        $stmt->execute();
        echo "Success";
    }
    


?>