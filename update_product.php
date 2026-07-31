<?php
include "db.php";

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $types = $_POST['types'];
    $qaunlity = $_POST['qaunlity'];
    $discount = $_POST['discount'];
    $description = $_POST['description'];

    // Check if a new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target = "uploads/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);

        // Update with new image
        $sql = "UPDATE products SET name='$name', price='$price', types='$types', qaunlity='$qaunlity', discount='$discount', description='$description', image='$image' WHERE id=$id";
    } else {
        // Update without changing image
        $sql = "UPDATE products SET name='$name', price='$price', types='$types', qaunlity='$qaunlity', discount='$discount', description='$description' WHERE id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: stock-products.php");
        exit();
    } else {
        echo "Error updating product: " . $conn->error;
    }
}
?>
