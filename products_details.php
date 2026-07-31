<?php
// ===========================================
// COMPLETE PRODUCT DETAILS PAGE
// FILE NAME: new_products.php
// ===========================================
session_start();
// DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "", "store_db");

if ($conn->connect_error) {
    die("Connection Failed : " . $conn->connect_error);
}

/*
=========================================
GET PRODUCT DETAILS
=========================================
*/

if(isset($_GET['id'])){

    $product_id = $_GET['id'];

    $sql = "SELECT * FROM products WHERE id='$product_id'";
    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result) > 0){

        $product = mysqli_fetch_assoc($result);

    }else{
        echo "Product not found";
        exit;
    }

}else{
    echo "No product selected";
    exit;
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

// GET PRODUCT ID
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// GET PRODUCT
$product_sql = "SELECT * FROM products WHERE id='$product_id'";
$product_result = $conn->query($product_sql);

// CHECK PRODUCT
if ($product_result->num_rows > 0) {

    $product = $product_result->fetch_assoc();

} else {

    die("Product Not Found");

}

// MESSAGE
$message = "";

// ======================
// ADD TO CART
// ======================

if(isset($_POST['add_to_cart'])){

    // CHECK LOGIN
    if(!isset($_SESSION['user_id'])){

        header("Location: products_details.php");
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

    header("Location: products_details.php");
    exit();
}



// ===========================================
// RELATED PRODUCTS
// ===========================================

$related_sql = "
SELECT * FROM products
WHERE id != '$product_id'
ORDER BY id DESC
LIMIT 16
";

$related_products = $conn->query($related_sql);

// CART COUNT
$cart_count = $conn->query("
SELECT * FROM cart
")->num_rows;

?>



<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Product Details</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

 <!-- NAVBAR -->
  <link rel="stylesheet" href="css/index-navbar-style.css">

<style>

/* =========================
   GLOBAL
========================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}

body{
    background:#f3f3f3;
    color:#222;
}

a{
    text-decoration:none;
}

.container{
    width:90%;
    margin:auto;
}

/* =========================
   HEADER
========================= */

header{
    background:white;
    padding:18px 0;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
}

.navbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo{
    font-size:30px;
    font-weight:bold;
}

.logo span{
    color:red;
}

.menu{
    display:flex;
    gap:25px;
}

.menu a{
    color:#666;
    font-size:15px;
}

.right-icons{
    display:flex;
    gap:20px;
    align-items:center;
}

.cart{
    position:relative;
}

.cart-count{
    position:absolute;
    top:-8px;
    right:-10px;
    width:18px;
    height:18px;
    background:red;
    color:white;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:11px;
}

/* =========================
   PRODUCT SECTION
========================= */

.product-section{
    margin-top: 120px;
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:50px;
}

.product-image{
    background:black;
    border-radius:20px;
    overflow:hidden;
    height: 480px;
}

.product-image img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.product-info h1{
    font-size:50px;
    margin-bottom:15px;
}

.badge{
    display:inline-block;
    background:#eef0ff;
    color:#5b61ff;
    padding:7px 15px;
    border-radius:6px;
    font-size:13px;
    margin-bottom:15px;
}

.rating{
    margin-top:20px;
    color:gold;
}

.rating span{
    color:#666;
    margin-left:10px;
}

.price{
    font-size:45px;
    font-weight:bold;
    margin-top:25px;
}

.buttons{
    display:flex;
    gap:15px;
    margin-top:30px;
}

.btn{
    border:none;
    padding:15px 30px;
    border-radius:10px;
    cursor:pointer;
    font-size:16px;
    transition:0.3s;
}

.add-cart{
    background:#5b61ff;
    color:white;
}

.add-cart:hover{
    background:#3e45ff;
}

.save-btn{
    width:55px;
    background:white;
    border:1px solid #ddd;
}

.save-btn:hover{
    background:#eee;
}

.colors{
    display:flex;
    gap:12px;
    margin-top:30px;
}

.color{
    width:22px;
    height:22px;
    border-radius:50%;
    border:2px solid #ddd;
}

.white{background:white;}
.black{background:black;}
.blue{background:#8cc7ff;}

/* =========================
   ALERT
========================= */

.alert{
    margin-top:25px;
    background:#d4edda;
    color:#155724;
    padding:15px;
    border-radius:8px;
}

/* =========================
   SPECIFICATIONS
========================= */

.specs{
    margin-top:70px;
    background:white;
    padding:40px;
    border-radius:15px;
}

.specs h2{
    margin-bottom:30px;
}

.spec-table{
    width:100%;
}

.spec-table tr{
    height:50px;
}

.spec-table td:first-child{
    width:220px;
    color:#777;
}

/* =========================
   FEATURES
========================= */

.features{
    margin-top:50px;
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:25px;
}

.feature-box{
    background:white;
    padding:40px;
    border-radius:15px;
    text-align:center;
}

.feature-box i{
    font-size:35px;
    margin-bottom:15px;
    color:#666;
}

/* =========================
   RELATED PRODUCTS
========================= */

.related{
    margin-top:70px;
}

.related h2{
    margin-bottom:30px;
}

.product-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:25px;
}

.card{
    background:white;
    border-radius:15px;
    overflow:hidden;
    padding:15px;
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px);
}

.card img{
    width:100%;
    height:220px;
    object-fit:cover;
    border-radius:10px;
}

.card-title{
    margin-top:15px;
    font-size:20px;
}

.card-price{
    margin-top:10px;
    font-weight:bold;
}

.card-bottom{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-top:15px;
}

.cancel{
    padding: 5px 10px;
    bac
}

.small-btn{
    width:40px;
    height:40px;
    border:none;
    background:#444;
    color:white;
    border-radius:8px;
    cursor:pointer;
}

/* =========================
   FOOTER
========================= */

footer{
    background:#111;
    color:white;
    margin-top:80px;
    padding:60px 0;
}

.footer-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:30px;
}

.footer-grid h3{
    margin-bottom:20px;
}

.footer-grid p{
    color:#bbb;
    line-height:28px;
}

.copy{
    text-align:center;
    margin-top:50px;
    color:#888;
}

/* =========================
   RESPONSIVE
========================= */

@media(max-width:992px){

    .product-section{
        grid-template-columns:1fr;
    }

    .product-grid{
        grid-template-columns:repeat(2,1fr);
    }

    .features{
        grid-template-columns:1fr;
    }

    .footer-grid{
        grid-template-columns:repeat(2,1fr);
    }

}

@media(max-width:600px){

    .menu{
        display:none;
    }

    .product-grid{
        grid-template-columns:1fr;
    }

    .footer-grid{
        grid-template-columns:1fr;
    }

    .product-info h1{
        font-size:35px;
    }

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
          <h4>  <?php echo $user['fullname']; ?></h4>
          <p> <?php echo $user['email']; ?> </p>
        </div>
      </div>

      <div class="menu-btn" id="menuBtn">
        <i class="fa-solid fa-bars"></i>
      </div>

    </div>

  </nav>



<!-- =========================
     MAIN
========================= -->

<div class="container">

<?php if($message != ""){ ?>

<div class="alert">
    <?php echo $message; ?>
</div>

<?php } ?>

<div class="product-section">

    <!-- IMAGE -->

    <div class="product-image">

        <img src="uploads/<?php echo $product['image']; ?>" alt="">

    </div>

    <!-- INFO -->

    <div class="product-info">

        <a href="Shopping.php">
            <button class="cancel">Cancel</button>
        </a>

        <span class="badge">
            New Arrival
        </span>

        <h1>
            <?php echo $product['name']; ?>
        </h1>

        <p>
            <?php echo $product['description']; ?>
        </p>

        <div class="rating">
            ★ ★ ★ ★ ★
            <span>4.9 Reviews</span>
        </div>

        <div class="price">
            $<?php echo $product['price']; ?>
        </div>

        <form method="POST">

            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="code" value="<?php echo $product['code']; ?>">
            <input type="hidden" name="name" value="<?php echo $product['name']; ?>">
            <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
            <input type="hidden" name="status" value="<?php echo $product['status']; ?>">
            <input type="hidden" name="qaunlity" value="<?php echo $product['qaunlity']; ?>">
            <input type="hidden" name="image" value="<?php echo $product['image']; ?>">

            <div class="buttons">

                <button
                    type="submit"
                    name="add_to_cart"
                    class="btn add-cart">

                    <i class="fa-solid fa-cart-shopping"></i>
                    Add To Cart

                </button>

            </div>

        </form>

    </div>

</div>

<!-- =========================
     SPECIFICATIONS
========================= -->

<div class="specs">

    <h2>Specifications</h2>

    <table class="spec-table">

        <tr>
            <td>Status</td>
            <td>New Arrival</td>
        </tr>

        <tr>
            <td>Item Code</td>
            <td><?php echo $product['code']; ?></td>
        </tr>

        <tr>
            <td>Product Name</td>
            <td><?php echo $product['name']; ?></td>
        </tr>

        <tr>
            <td>Price</td>
            <td>$<?php echo $product['price']; ?></td>
        </tr>

        <tr>
            <td>Discount</td>
            <td>$<?php echo $product['discount']; ?></td>
        </tr>

        <tr>
            <td>Description</td>
            <td><?php echo $product['description']; ?></td>
        </tr>

    </table>

</div>

<!-- =========================
     FEATURES
========================= -->

<div class="features">

    <div class="feature-box">

        <i class="fa-solid fa-lock"></i>

        <h3>Secure Payment</h3>

    </div>

    <div class="feature-box">

        <i class="fa-solid fa-truck-fast"></i>

        <h3>Free Delivery</h3>

    </div>

    <div class="feature-box">

        <i class="fa-solid fa-rotate-left"></i>

        <h3>Free Returns</h3>

    </div>

</div>

<!-- =========================
     RELATED PRODUCTS
========================= -->

<div class="related">

    <h2>Goods For You</h2>

    <div class="product-grid">

        <?php while($row = $related_products->fetch_assoc()){ ?>

        <div class="card">

            <a href="products_details.php?id=<?php echo $row['id']; ?>"><img src="uploads/<?php echo $row['image']; ?>" alt="" ></a>

            <div class="card-title">
                <?php echo $row['name']; ?>
            </div>

            <div class="card-price">
                $<?php echo $row['price']; ?>
            </div>

        </div>

        <?php } ?>

    </div>

</div>

</div>

<!-- =========================
     FOOTER
========================= -->

<footer>

<div class="container">

    <div class="footer-grid">

        <div>

            <h3>GOSHOP</h3>

            <p>
                Professional ecommerce website
                for digital product selling.
            </p>

        </div>

        <div>

            <h3>Use Every</h3>

            <p>UI Design</p>
            <p>UX Design</p>
            <p>Programming</p>
            <p>Development</p>

        </div>

        <div>

            <h3>Explore</h3>

            <p>Design</p>
            <p>Development</p>
            <p>Design System</p>
            <p>Figma</p>

        </div>

        <div>

            <h3>Resources</h3>

            <p>Blog</p>
            <p>Support</p>
            <p>Contact</p>
            <p>Developer</p>

        </div>

    </div>

    <div class="copy">
        © 2026 GOSHOP. All Rights Reserved.
    </div>

</div>

</footer>

<!-- =========================
     JAVASCRIPT
========================= -->

<script>

// COLOR ACTIVE
const colors = document.querySelectorAll('.color');

colors.forEach(color => {

    color.addEventListener('click', () => {

        colors.forEach(c => {
            c.style.border = "2px solid #ddd";
        });

        color.style.border = "3px solid #5b61ff";

    });

});

</script>

</body>
</html>