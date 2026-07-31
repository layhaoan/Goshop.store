<?php
$conn = new mysqli("localhost","root","","store_db");
$stmt = $conn->prepare("INSERT INTO reviews (product_id,name,comment) VALUES (?,?,?)");
$stmt->bind_param("iss", $_POST['product_id'], $_POST['name'], $_POST['comment']);
$stmt->execute();
header("Location: product_details.php?id=".$_POST['product_id']);
?>
