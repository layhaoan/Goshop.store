<?php
    include "db.php";

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Get image file name
        $result = $conn->query("SELECT image FROM products WHERE id=$id");
        $product = $result->fetch_assoc();

        // Delete image file from 'uploads' folder
        if ($product['image'] && file_exists("uploads/" . $product['image'])) {
            unlink("uploads/" . $product['image']);
        }

        // Delete product from database
        $sql = "DELETE FROM products WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            header("Location: new_products.php");
            exit();
        } else {
            echo "Error deleting product: " . $conn->error;
        }
    }
?>
