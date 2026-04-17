<?php 
require 'php/_base.php';

$id = $_POST['id'];

$stmt = $_db->prepare("DELETE FROM product WHERE product_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo "Success";
?>