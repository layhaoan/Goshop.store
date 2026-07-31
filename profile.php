<!-- save as: profile.php -->

<?php
    session_start();
    include "db.php";

    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
    }

    $id = $_SESSION['user_id'];

    $result = $conn->query("SELECT * FROM users WHERE id='$id'");
    $user = $result->fetch_assoc();
?>

<?php

session_start();

$conn = new mysqli("localhost","root","","store_db");



if(!isset($_SESSION['user_id'])){

    die("Please Login");

}

$user_id = $_SESSION['user_id'];

$order_sql = "

SELECT *

FROM test_orders

WHERE user_id = '$user_id'

ORDER BY id DESC

";

$order_result = $conn->query($order_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Responsive My Account Page</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
<link rel="stylesheet" href="css/profile-page-style-re.css">
<!-- NAVBAR -->
<link rel="stylesheet" href="css/index-navbar-style.css">
<style>

.container{
    max-width:1200px;
    margin:auto;
}

.title{
    font-size: 0.8rem;
    margin-bottom: 20px;
}

.order-box{
    background:white;
    padding:20px;
    border-radius:12px;
    margin-bottom:25px;
    border:1px solid #969696;
}

.order-box h2{
    font-size: 1rem;
}

.order-box p{
    font-size: 1rem;
}

.item{
    display:flex;
    gap:15px;
    align-items:center;
    border-top:1px solid #eee;
    padding:15px 0;
}

.item img{
    width:90px;
    height:90px;
    object-fit:cover;
}

.total{
    font-size: 1.1rem;
    font-weight:bold;
    margin-top:20px;
}

.empty{
    background:white;
    padding:40px;
    text-align:center;
    border-radius:12px;
    font-size:22px;
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
        <img src="uploads/<?php echo $user['profile_image']; ?>" alt="">
        
        <div class="profile-text">
          <h4><?php echo isset($user['fullname']) ? $user['fullname'] : ''; ?></h4>
          <p><?php echo isset($user['email']) ? $user['email'] : ''; ?></p>
        </div>
      </div>

      <div class="menu-btn" id="menuBtn">
        <i class="fa-solid fa-bars"></i>
      </div>

    </div>

  </nav>

<!-- ================= BANNER ================= -->

<section class="banner">
    <h1>My Account</h1>
    <p>Home / My Account</p>
</section>

<!-- ================= MAIN ================= -->

<div class="container">

    <!-- CONTENT -->

    <div class="content">

        <!-- TABS HEADER -->

        <div class="tabs-header">

            <button class="tab-btn active"
            onclick="showTab('personal', this)">
                Personal Information
            </button>

            <button class="tab-btn"
            onclick="showTab('orders', this)">
                My Orders
            </button>

            <button class="tab-btn"
            onclick="showTab('address', this)">
                Manage Address
            </button>

            <button class="tab-btn"
            onclick="showTab('payment', this)">
                Payment Method
            </button>

            <button class="tab-btn"
            onclick="showTab('saved', this)">
                Item Saved
            </button>

            <button class="tab-btn"
            onclick="showTab('profile', this)">
                Manage Profile
            </button>

        </div>

        <!-- ================= PERSONAL TAB ================= -->

        <div class="tab-content active-tab" id="personal">

            <div class="profile-card">

               <a href="update_profile.php">
                 <button class="edit-btn">
                    Edit Profile
                </button>
               </a>

                <div class="profile-header">

                    <img src="uploads/<?php echo $user['profile_image']; ?>" alt="">

                    <div class="profile-name">
                        <h1><?php echo isset($user['fullname']) ? $user['fullname'] : ''; ?></h1>
                        <p><?php echo isset($user['email']) ? $user['email'] : ''; ?></p>
                    </div>

                </div>

                <div class="profile-details">

                    <div class="detail">
                        <strong>Phone</strong>
                        <span>: <?php echo $user['phone']; ?></span>
                    </div>

                    <div class="detail">
                        <strong>Gender</strong>
                        <span>: <?php echo $user['gender']; ?></span>
                    </div>

                    <div class="detail">
                        <strong>Country</strong>
                        <span>: <?php echo $user['country']; ?></span>
                    </div>

                    <div class="detail">
                        <strong>City</strong>
                        <span>: <?php echo $user['city']; ?></span>
                    </div>

                    <div class="detail">
                        <strong>Bio</strong>
                    </div>

                    <p class="bio"><?php echo $user['bio']; ?></p>

                </div>

            </div>

        </div>

        <!-- ================= ORDERS TAB ================= -->

        <div class="tab-content" id="orders">

            <div class="simple-box">
                <h2>My Orders</h2>
                <p>Your order history will show here.</p>

                    <?php
                        if($order_result->num_rows > 0){
                            while($order = $order_result->fetch_assoc()){
                        ?>

                        <div class="order-box">
                            <h2> Order #<?php echo $order['id']; ?></h2>
                            <p>Status :<?php echo $order['order_status']; ?></p>
                            <p>Date :<?php echo $order['created_at']; ?></p>

                            <?php $order_id = $order['id']; $item_sql = " SELECT * FROM test_order_items WHERE order_id = '$order_id' ";
                            $item_result = $conn->query($item_sql); while($item = $item_result->fetch_assoc()){
                            ?>

                            <div class="item">
                                <img src='uploads/<?php echo $item['product_image']; ?>' >
                                <div>
                                    <p class="products_code"> Products Code : <?php echo $item['code']; ?> </p>
                                    <h3> <?php echo $item['product_name']; ?> </h3>
                                    <p> Qty : <?php echo $item['product_qty']; ?> </p>
                                    <p> Price : $<?php echo $item['product_price']; ?> </p>

                                </div>
                            </div>
                            <?php } ?>
                            <div class="total"> Grand Total : $<?php echo $order['grand_total']; ?> </div>
                        </div>

                        <?php

                            }

                        }else{

                            echo " <div class='empty'> No Orders Found </div> ";

                        }

                    ?>
            </div>


        </div>

        <!-- ================= ADDRESS TAB ================= -->

        <div class="tab-content" id="address">

            <div class="simple-box">
                <h2>Manage Address</h2>
                <p>Your address information will show here.</p>
            </div>

        </div>

        <!-- ================= PAYMENT TAB ================= -->

        <div class="tab-content" id="payment">

            <div class="simple-box">
                <h2>Payment Method</h2>
                <p>Your payment methods will show here.</p>
            </div>

        </div>

        <!-- ================= SAVED TAB ================= -->

        <div class="tab-content" id="saved">

            <div class="simple-box">
                <h2>Saved Items</h2>
                <p>Your saved products will show here.</p>
            </div>

        </div>

        <!-- ================= PROFILE TAB ================= -->

        <div class="tab-content" id="profile">

            <div class="simple-box">
                <h2>Manage Profile</h2>
                <p>Your profile settings will show here.</p>
            </div>

        </div>

    </div>

</div>

<!-- FEATURES -->

<div class="features">

    <div class="feature">
        <i class="fa-solid fa-truck"></i>
        <div>
            <h3>Free Shipping</h3>
            <p>Free shipping for order above $180</p>
        </div>
    </div>

    <div class="feature">
        <i class="fa-solid fa-wallet"></i>
        <div>
            <h3>Flexible Payment</h3>
            <p>Secure payment options</p>
        </div>
    </div>

    <div class="feature">
        <i class="fa-solid fa-headset"></i>
        <div>
            <h3>24/7 Support</h3>
            <p>Online support all days</p>
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

<!-- ================= JAVASCRIPT ================= -->

<script>

const tabs = document.querySelectorAll(".tab-btn");
const contents = document.querySelectorAll(".tab-content");

function showTab(tabId, element){

    contents.forEach(content=>{
        content.classList.remove("active-tab");
    });

    tabs.forEach(tab=>{
        tab.classList.remove("active");
    });

    document.getElementById(tabId)
    .classList.add("active-tab");

    element.classList.add("active");
}

</script>

</body>
</html>