<?php
// ======================================================
// SAVE FAVORITE FROM PRODUCTS TABLE
// ======================================================

session_start();

$conn = new mysqli("localhost","root","","store_db");

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

$_SESSION['user_id'] = 1;

$user_id = $_SESSION['user_id'];

/*
=========================================================
PRODUCTS TABLE EXAMPLE
=========================================================

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255),
    product_price VARCHAR(100),
    product_image TEXT
);

=========================================================

FAVORITES TABLE
=========================================================

CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

=========================================================
*/


// ==============================================
// GET PRODUCT ID
// Example: product.php?id=1
// ==============================================

$product_id = $_GET['id'];


// ==============================================
// GET PRODUCT DATA FROM PRODUCTS TABLE
// ==============================================

$productQuery = $conn->query("
    SELECT * FROM products
    WHERE id='$product_id'
");

$product = $productQuery->fetch_assoc();


// ==============================================
// SAVE FAVORITE
// ==============================================

if(isset($_POST['save_favorite'])){

    // CHECK ALREADY SAVED
    $check = $conn->query("
        SELECT * FROM favorites
        WHERE user_id='$user_id'
        AND product_id='$product_id'
    ");

    if($check->num_rows == 0){

        $stmt = $conn->prepare("
            INSERT INTO favorites(user_id, product_id)
            VALUES(?,?)
        ");

        $stmt->bind_param(
            "ii",
            $user_id,
            $product_id
        );

        $stmt->execute();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Product</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<style>

body{
    background:#f5f5f5;
    font-family:Arial;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.card{
    width:320px;
    background:#fff;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
    position:relative;
}

.card img{
    width:100%;
    height:220px;
    object-fit:cover;
}

.content{
    padding:20px;
}

.price{
    color:#ff6600;
    font-size:22px;
    font-weight:bold;
    margin-top:10px;
}

/* HEART BUTTON */

.heart-form{
    position:absolute;
    top:15px;
    right:15px;
}

.heart-btn{
    width:50px;
    height:50px;
    border:none;
    border-radius:50%;
    background:#fff;
    cursor:pointer;
    box-shadow:0 5px 15px rgba(0,0,0,0.15);
}

.heart-btn i{
    color:red;
    font-size:22px;
}

.favorite-link{
    display:inline-block;
    margin-top:15px;
    background:#111;
    color:#fff;
    padding:12px 20px;
    border-radius:10px;
    text-decoration:none;
}

</style>
</head>
<body>

<div class="card">

    <!-- SAVE FAVORITE -->
    <form method="POST" class="heart-form">

        <button type="submit" name="save_favorite" class="heart-btn">
            <i class="fa-solid fa-heart"></i>
        </button>

    </form>

    <!-- PRODUCT IMAGE -->
    <img src="<?php echo $product['product_image']; ?>">

    <div class="content">

        <h2>
            <?php echo $product['product_name']; ?>
        </h2>

        <div class="price">
            $<?php echo $product['product_price']; ?>
        </div>

        <a href="favorites.php" class="favorite-link">
            View Favorites
        </a>

    </div>

</div>

</body>
</html>