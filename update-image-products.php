<?php
$conn = new mysqli("localhost", "root", "", "store_db");

$id = $_POST['id'];

// Get old image
$result = $conn->query("SELECT image FROM products WHERE id=$id");
$product = $result->fetch_assoc();

$image = $product['image']; // default old image

// If user selects new image
if (!empty($_FILES['image']['name'])) {

    $newImage = time() . "_" . $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];
    $path = "uploads/" . $newImage;

    if (move_uploaded_file($tmp, $path)) {

        // delete old image
        if (!empty($image) && file_exists("uploads/" . $image)) {
            unlink("uploads/" . $image);
        }

        $image = $newImage;
    }
}

// Update database
$conn->query("UPDATE products SET image='$image' WHERE id=$id");

header("Location: stock-products.php");
exit;
?>