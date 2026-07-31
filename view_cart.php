

<?php
session_start();

// DATABASE CONNECTION
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "store_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

// ======================
// GET USER DATA
// ======================

$user = null;

if(isset($_SESSION['user_id'])){

    $user_id = $_SESSION['user_id'];

    $user_sql = "SELECT * FROM users WHERE id='$user_id'";
    $user_result = $conn->query($user_sql);

    if($user_result->num_rows > 0){
        $user = $user_result->fetch_assoc();
    }
}

// ======================
// ADD TO CART
// ======================

if(isset($_POST['add_to_cart'])){

    // CHECK LOGIN
    if(!isset($_SESSION['user_id'])){

        header("Location: login.php");
        exit();
    }

    $product_id     = $_POST['id'];
    $product_name   = $_POST['name'];
    $product_price  = $_POST['price'];
    $product_image  = $_POST['image'];
    $product_status = $_POST['status'];

    $cart_item = [

        'id'     => $product_id,
        'name'   => $product_name,
        'price'  => $product_price,
        'image'  => $product_image,
        'status' => $product_status,
        'qty'    => 1
    ];

    // CREATE CART SESSION
    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = [];
    }

    // CHECK PRODUCT EXIST
    $found = false;

    foreach($_SESSION['cart'] as &$item){

        if($item['id'] == $product_id){

            $item['qty']++;
            $found = true;
            break;
        }
    }

    // image
    $image = $_FILES['image']['name'];

        $tmp_name = $_FILES['image']['tmp_name'];

        move_uploaded_file(
            $tmp_name,
            "uploads/".$image
        );

    // ADD NEW PRODUCT
    if(!$found){
        $_SESSION['cart'][] = $cart_item;
    }

    header("Location: view_cart.php");
    exit();
}

// Delete product
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    unset($_SESSION['cart'][$delete_id]);
    header("Location: view_cart.php");
    exit();
}

// ======================
// FETCH PRODUCTS
// ======================

$sql = "SELECT * FROM products ORDER BY id DESC";

$product_result = $conn->query($sql);

if(!$product_result){
    die("Product Query Failed : " . $conn->error);
}

?>


<?php
session_start();

$conn = new mysqli("localhost","root","","store_db");

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

/* =========================================
ADD TO CART
========================================= */

if(isset($_GET['add'])){

    $id = (int)$_GET['add'];

    $sql = "
    SELECT *
    FROM products
    WHERE id = '$id'
    ";

    $result = $conn->query($sql);

    if($result && $result->num_rows > 0){

        $product = $result->fetch_assoc();

        if(isset($_SESSION['cart'][$id])){

            $_SESSION['cart'][$id]['qty']++;

        }else{

            $_SESSION['cart'][$id] = [

                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'qty' => 1

            ];

            /* SAVE COLOR ONLY IF EXIST */

            if(isset($product['color']) && !empty($product['color'])){

                $_SESSION['cart'][$id]['color']
                = $product['color'];

            }

        }

    }

    header("Location: view_cart.php");
    exit();
}

/* =========================================
INCREASE
========================================= */

if(isset($_GET['increase'])){

    $id = (int)$_GET['increase'];

    if(isset($_SESSION['cart'][$id])){

        $_SESSION['cart'][$id]['qty']++;

    }

    header("Location: view_cart.php");
    exit();
}

/* =========================================
DECREASE
========================================= */

if(isset($_GET['decrease'])){

    $id = (int)$_GET['decrease'];

    if(isset($_SESSION['cart'][$id])){

        if($_SESSION['cart'][$id]['qty'] > 1){

            $_SESSION['cart'][$id]['qty']--;

        }

    }

    header("Location: view_cart.php");
    exit();
}

/* =========================================
REMOVE
========================================= */

if(isset($_GET['remove'])){

    $id = (int)$_GET['remove'];

    unset($_SESSION['cart'][$id]);

    header("Location: view_cart.php");
    exit();
}

/* =========================================
CHECK COLOR COLUMN EXIST
========================================= */

$has_color = false;

if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])){

    foreach($_SESSION['cart'] as $item){

        if(isset($item['color']) && !empty($item['color'])){

            $has_color = true;
            break;

        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Shopping Cart</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
  <!-- NAVBAR -->
  <link rel="stylesheet" href="css/index-navbar-style.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    background:#f5f5f5;
}


/* =========================================
HEADER
========================================= */

.header{
    width:100%;
    height:220px;

    background:
    linear-gradient(rgba(0,0,0,0.5),
    rgba(0,0,0,0.5)),
    url('https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1400');

    background-size:cover;
    background-position:center;

    display:flex;
    justify-content:center;
    align-items:center;
}

.header h1{
    color:white;
    font-size:55px;
}

/* =========================================
CART
========================================= */

.cart-section{
    width: 75%;
    margin:60px auto;
}

.cart-header{
    display:grid;

    grid-template-columns:
    <?php if($has_color){ ?>
    2fr 1fr 1fr 1fr 1fr 80px
    <?php }else{ ?>
    2fr 1fr 1fr 1fr 80px
    <?php } ?>;

    padding-bottom:20px;

    border-bottom:2px solid #ccc;

    color:#666;
    font-size:22px;
}

.cart-item{
    display:grid;

    grid-template-columns:
    <?php if($has_color){ ?>
    2fr 1fr 1fr 1fr 1fr 80px
    <?php }else{ ?>
    2fr 1fr 1fr 1fr 80px
    <?php } ?>;

    align-items:center;

    padding:30px 0;

    border-bottom:1px solid #ddd;
}

.product-box{
    display:flex;
    align-items:center;
    gap:20px;
}

.product-box img{
    width:100px;
    height:100px;
    object-fit:cover;
    border-radius:12px;
}

.product-box h3{
    font-size: 1.1rem;
}

.cart-item p{
    font-size: 1rem;
}

/* =========================================
QUANTITY
========================================= */

.quantity{
    display:flex;
    align-items:center;
    gap:10px;
}

.quantity a{
    width: 30px;
    height: 30px;

    border:1px solid #ccc;
    border-radius:8px;

    display:flex;
    justify-content:center;
    align-items:center;

    text-decoration:none;
    color:black;
    background:white;
}

.quantity span{
    font-size: 1rem;
}

/* =========================================
REMOVE
========================================= */

.remove-btn{
    color:black;
    font-size:24px;
}

/* =========================================
SUMMARY
========================================= */

.summary{
    margin-top:60px;
    background:#ececec;
    padding:40px;
    border-radius:12px;
}

.summary-row{
    display:flex;
    justify-content:space-between;
    margin-bottom: 15px;
    font-size: 1.1rem;
}

.total{
    border-top:2px solid #aaa;
    padding-top:20px;
    font-weight:bold;
}

/* =========================================
BUTTONS
========================================= */

.buttons{
    margin-top:40px;
    display:flex;
    justify-content:flex-end;
    gap:20px;
}

.btn{
    padding: 10px 30px;
    border-radius:10px;
    text-decoration:none;
    font-size: 0.95rem;
}

.home-btn{
    background:white;
    border:1px solid #999;
    color:black;
}

.checkout-btn{
    background:#6675ff;
    color:white;
}

/* =========================================
EMPTY
========================================= */

.empty{
    text-align:center;
    padding:80px;
    font-size:30px;
    color:#777;
}

/* FOOTER */

footer{
background:#111;
color:#fff;
padding:70px 8% 30px;
}

.footer-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:40px;
}

.footer-col h3{
margin-bottom:20px;
}

.footer-col a{
display:block;
margin-bottom:10px;
color:#bbb;
transition:.3s;
}

.footer-col a:hover{
color:#fff;
padding-left:5px;
}

.socials{
display:flex;
gap:15px;
margin-top:20px;
}

.socials i{
width:40px;
height:40px;
border-radius:50%;
background:#222;
display:flex;
align-items:center;
justify-content:center;
transition:.4s;
cursor:pointer;
}

.socials i:hover{
background:#fff;
color:#111;
transform:translateY(-5px);
}

.footer-bottom{
text-align:center;
margin-top:50px;
color:#888;
font-size:14px;
}


</style>

</head>
<body>

<!-- ================= NAVBAR ================= -->
    <nav class="navbar">

        <div class="logo">
        Go<span>Shop</span>
        </div>

        <ul class="nav-links" id="navLinks">
            <li><a href="Homepage.php">Homepage</a></li>
            <li><a href="Shopping.php">Shopping</a></li>
            <li><a href="about_us.php">About Us</a></li>
            <li><a href="contact_us.php">Contact Us</a></li>
        </ul>

        <div class="nav-right">

        <a href="view_cart.php">
            <div class="cart">
                <i class="fa-solid fa-cart-shopping"></i>
                <span><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
            </div>
        </a>

        <div class="profile">
            <a href="profile.php"><img src="uploads/<?php echo $user['profile_image']; ?>"></a>
            <div class="profile-text">
            <h4><?php echo $user['fullname']; ?></h4>
            <p> <?php echo $user['email']; ?> </p>
            </div>
        </div>

        <div class="menu-btn" id="menuBtn">
            <i class="fa-solid fa-bars"></i>
        </div>

        </div>

  </nav>

<!-- HEADER -->

<div class="header">

    <h1>Shopping Cart</h1>

</div>

<!-- CART -->

<div class="cart-section">

<div class="cart-header">

    <div>Products</div>

    <?php if($has_color){ ?>

        <div>Color</div>

    <?php } ?>

    <div>Price</div>
    <div>Quantity</div>
    <div>Total</div>
    <div></div>

</div>

<?php

$subtotal = 0;
$item = 0;

if(isset($_SESSION['cart']) &&
!empty($_SESSION['cart'])){

foreach($_SESSION['cart'] as $id => $cart){

$name = $cart['name'];
$price = $cart['price'];
$image = $cart['image'];
$qty = $cart['qty'];

$total = $price * $qty;

$subtotal += $total;

$item += $qty;

?>

<div class="cart-item">

    <div class="product-box">

        <img
        src="uploads/<?php echo $image; ?>"
        alt="">

        <h3>

            <?php echo $name; ?>

        </h3>

    </div>

    <?php if($has_color){ ?>

        <p>

            <?php

            echo isset($cart['color'])
            ? $cart['color']
            : '-';

            ?>

        </p>

    <?php } ?>

    <p>

        $ <?php echo $price; ?>

    </p>

    <div class="quantity">

        <a href="?decrease=<?php echo $id; ?>">
            -
        </a>

        <span>

            <?php echo $qty; ?>

        </span>

        <a href="?increase=<?php echo $id; ?>">
            +
        </a>

    </div>

    <p style="color:#6675ff;font-weight:bold;">

        $ <?php echo $total; ?>

    </p>

    <a
    class="remove-btn"
    href="?remove=<?php echo $id; ?>">

        <i class="fa-regular fa-trash-can"></i>

    </a>

</div>

<?php

}

}else{

echo "
<div class='empty'>
Your Cart is Empty
</div>
";

}

$discount = 10;

$grand_total = $subtotal - $discount;

if($grand_total < 0){
    $grand_total = 0;
}

?>

<!-- SUMMARY -->

<div class="summary">

    <div class="summary-row">

        <span>Products Item</span>

        <span>

            <?php echo $item; ?> item

        </span>

    </div>

    <div class="summary-row">

        <span>Discount</span>

        <span>

             <?php echo $discount; ?> %

        </span>

    </div>

    <div class="summary-row">

        <span>Sub Total</span>

        <span>

            <?php echo $subtotal; ?> $

        </span>

    </div>

    <div class="summary-row total">

        <span>Grand Total</span>

        <span>

            $ <?php echo $grand_total; ?>

        </span>

    </div>

    <div class="buttons">

        <a href="Shopping.php" class="btn home-btn">  Back to Home </a>
        <a href="checkout.php" class="btn checkout-btn"> Proceed to Checkout </a>

    </div>

</div>

</div>

<!-- FOOTER -->
  <footer>

    <div class="footer-grid">

      <div class="footer-col">

        <h3>GOSHOP</h3>

        <p>
          Professional ecommerce website for digital product selling.
        </p>

        <div class="socials">
          <i class="fa-brands fa-facebook-f"></i>
          <i class="fa-brands fa-instagram"></i>
          <i class="fa-brands fa-x-twitter"></i>
          <i class="fa-brands fa-youtube"></i>
        </div>

      </div>

      <div class="footer-col">
        <h3>Use every</h3>

        <a href="#">UI Design</a>
        <a href="#">UX Design</a>
        <a href="#">Programming</a>
        <a href="#">Development</a>
      </div>

      <div class="footer-col">
        <h3>Explore</h3>

        <a href="#">Design</a>
        <a href="#">Development</a>
        <a href="#">Design System</a>
        <a href="#">Figma</a>
      </div>

      <div class="footer-col">
        <h3>Resources</h3>

        <a href="#">Blog</a>
        <a href="#">Support</a>
        <a href="#">Contact</a>
        <a href="#">Developer</a>
      </div>

    </div>

    <div class="footer-bottom">
      © 2026 GOSHOP. All Rights Reserved.
    </div>

  </footer>

</body>
</html>