<?php
session_start();
include 'db.php';
include 'telegram_send.php';

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

$user = mysqli_fetch_assoc(
    mysqli_query($conn,"
        SELECT *
        FROM users
        WHERE id='$user_id'
    ")
);


/* =========================================
CHECK CART
========================================= */

if(!isset($_SESSION['cart']) ||
empty($_SESSION['cart'])){

    echo "
    <h2 style='text-align:center;
    margin-top:100px;
    font-family:Arial;'>
    Cart is Empty
    </h2>
    ";

    exit();
}

/* =========================================
PLACE ORDER
========================================= */

$message = "";

if(isset($_POST['place_order'])){

    $fullname =
    trim($_POST['fullname']);

    $email =
    trim($_POST['email']);

    $phone =
    trim($_POST['phone']);

    $address =
    trim($_POST['address']);

    $payment =
    trim($_POST['payment']);

    /* =========================================
    CALCULATE TOTAL
    ========================================= */

    $subtotal = 0;
    $total_item = 0;

    foreach($_SESSION['cart'] as $id => $cart){

        $item_total =
        $cart['price'] *
        $cart['qty'];

        $subtotal += $item_total;

        $total_item += $cart['qty'];

    }

    /* =========================================
    DISCOUNT
    ========================================= */

    $discount_percent = 10;

    $discount_price =
    ($subtotal * $discount_percent) / 100;

    /* =========================================
    PRICE AFTER DISCOUNT
    ========================================= */

    $price_after_discount =
    $subtotal - $discount_price;

    /* =========================================
    SHIPPING
    ========================================= */

    $shipping = 0;

    /* =========================================
    GRAND TOTAL
    ========================================= */

    $grand_total =
    $price_after_discount + $shipping;

    /* =========================================
    INSERT ORDER
    ========================================= */
    
    session_start();
    $user_id = $_SESSION['user_id'];

    $sql = "
    INSERT INTO test_orders(
        user_id,
        fullname,
        email,
        phone,
        address,
        payment_method,
        total_item,
        subtotal,
        discount_percent,
        discount_price,
        shipping_fee,
        grand_total

    )

    VALUES(
        '$user_id',
        '$fullname',
        '$email',
        '$phone',
        '$address',
        '$payment',
        '$total_item',
        '$subtotal',
        '$discount_percent',
        '$discount_price',
        '$shipping',
        '$grand_total'

    )
    ";

    if($conn->query($sql)){

        $order_id =
        $conn->insert_id;

        /* =========================================
        INSERT ORDER ITEMS
        ========================================= */

        foreach($_SESSION['cart'] as $id => $cart){

            $product_name =
            $cart['name'];

            $product_price =
            $cart['price'];

            $product_qty =
            $cart['qty'];

            $product_image =
            $cart['image'];

            $product_total =
            $product_price *
            $product_qty;

            $item_sql = "
            INSERT INTO test_order_items(

                order_id,
                product_name,
                product_price,
                product_qty,
                product_total,
                product_image

            )

            VALUES(

                '$order_id',
                '$product_name',
                '$product_price',
                '$product_qty',
                '$product_total',
                '$product_image'

            )
            ";

            $conn->query($item_sql);

        }

        /* =========================================
        CLEAR CART
        ========================================= */

        unset($_SESSION['cart']);

        $message =
        "Order Placed Successfully";
        header("Location: Shopping.php");

    }

}

?>



<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width,
initial-scale=1.0">

<title>Checkout</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<style>

/* =========================================
GLOBAL
========================================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    background:#f4f5f7;
}

/* =========================================
NAVBAR
========================================= */

.navbar{
    width:100%;
    height:90px;
    background:white;

    display:flex;
    justify-content:space-between;
    align-items:center;

    padding:0 6%;

    box-shadow:0 2px 10px rgba(0,0,0,0.05);
}

.logo{
    font-size:40px;
    font-weight:bold;
}

.logo span{
    color:#ff3b30;
}

/* =========================================
HEADER
========================================= */

.header{
    width:100%;
    height:240px;

    background:
    linear-gradient(rgba(0,0,0,0.5),
    rgba(0,0,0,0.5)),
    url('https://images.unsplash.com/photo-1556740749-887f6717d7e4?q=80&w=1400');

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
CHECKOUT
========================================= */

.checkout-container{
    width:92%;
    margin:60px auto;

    display:grid;
    grid-template-columns:1.3fr 0.7fr;

    gap:40px;
}

/* =========================================
FORM
========================================= */

.checkout-form{
    background:white;
    padding: 30px;
    border-radius:18px;
}

.checkout-form h2{
    margin-bottom:30px;
    font-size:35px;
}

.form-group{
    margin-bottom:25px;
}

.form-group label{
    display:block;
    margin-bottom:10px;
    font-size: 0.85rem;
    color:#555;
}

.form-group input,
.form-group textarea,
.form-group select{

    width:100%;
    padding: 10px;

    border:1px solid #ccc;
    border-radius:10px;

    font-size: 0.90rem;
    outline:none;
}

.form-group textarea{
    resize:none;
    height:120px;
}

/* =========================================
ORDER SUMMARY
========================================= */

.summary{
    background:white;
    padding:35px;
    border-radius:18px;
    height:max-content;
}

.summary h2{
    margin-bottom:30px;
    font-size:35px;
}

.order-item{
    display:flex;
    align-items:center;
    gap:15px;

    margin-bottom:20px;
}

.order-item img{
    width:80px;
    height:80px;
    object-fit:cover;
    border-radius:10px;
}

.order-item h4{
    font-size:18px;
    margin-bottom:6px;
}

.order-item p{
    color:#666;
    margin-bottom:4px;
}

.summary-row{
    display:flex;
    justify-content:space-between;

    margin-top:18px;

    font-size:19px;
}

.total{
    border-top:2px solid #ddd;
    padding-top:20px;
    margin-top:30px;

    font-size:24px;
    font-weight:bold;
}

/* =========================================
BUTTON
========================================= */

.checkout-btn{
    width:100%;
    padding:18px;

    border:none;
    border-radius:12px;

    background:#6675ff;
    color:white;

    font-size:20px;
    cursor:pointer;

    margin-top:35px;
}

/* =========================================
MESSAGE
========================================= */

.message{
    width:92%;
    margin:30px auto;

    background:#d4edda;
    color:#155724;

    padding:18px;
    border-radius:10px;

    font-size:18px;
}

/* =========================================
RESPONSIVE
========================================= */

@media(max-width:900px){

    .checkout-container{
        grid-template-columns:1fr;
    }

    .header h1{
        font-size:40px;
    }

}

</style>

</head>
<body>

<!-- =========================================
NAVBAR
========================================= -->

<div class="navbar">

    <div class="logo">

        Go<span>Shop</span>

    </div>

</div>

<!-- =========================================
HEADER
========================================= -->

<div class="header">

    <h1>Checkout</h1>

</div>

<!-- =========================================
SUCCESS MESSAGE
========================================= -->

<?php if($message != ""){ ?>

<div class="message">

    <?php echo $message; ?>

</div>

<?php } ?>

<!-- =========================================
CHECKOUT
========================================= -->

<div class="checkout-container">

<!-- =========================================
FORM
========================================= -->

<div class="checkout-form">

<form method="POST">

    <h2>Billing Details</h2>

    <div class="form-group">

        <label>Full Name</label>

        <input type="text" name="fullname" required>

    </div>

    <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" required>
    </div>

    <div class="form-group">
        <label>Phone Number</label>
        <input type="number"  name="phone" required>
    </div>

    <div class="form-group">
        <label>Address</label>
        <textarea name="address" required></textarea>
    </div>

    <div class="form-group">

        <label>Payment Method</label>

        <select name="payment">

            <option>
                Cash On Delivery
            </option>

            <option>
                ABA Bank
            </option>

            <option>
                ACLEDA Bank
            </option>

            <option>
                Credit Card
            </option>

        </select>

    </div>

    <button id="checkoutBtn" type="submit" name="place_order"  class="checkout-btn"> Place Order  </button>

</form>

</div>

<!-- =========================================
ORDER SUMMARY
========================================= -->

<div class="summary">

    <h2>Order Summary</h2>

<?php

$subtotal = 0;
$total_item = 0;

foreach($_SESSION['cart'] as $id => $cart){

$item_total =
$cart['price'] *
$cart['qty'];

$subtotal += $item_total;

$total_item += $cart['qty'];

?>

<div class="order-item">

    <img
    src="uploads/<?php echo $cart['image']; ?>"
    alt="">

    <div>

        <h4>

            <?php echo $cart['name']; ?>

        </h4>

        <p>

            Quantity :
            <?php echo $cart['qty']; ?>

        </p>

        <p>

            Original Price :
            $
            <?php echo number_format($item_total,2); ?>

        </p>

    </div>

</div>

<?php } ?>

<?php

/* =========================================
DISCOUNT
========================================= */

$discount_percent = 10;

$discount_price =
($subtotal * $discount_percent) / 100;

/* =========================================
PRICE AFTER DISCOUNT
========================================= */

$price_after_discount =
$subtotal - $discount_price;

/* =========================================
SHIPPING
========================================= */

$shipping = 0;

/* =========================================
GRAND TOTAL
========================================= */

$grand_total =
$price_after_discount + $shipping;

?>

<div class="summary-row">

    <span>Total Item</span>

    <span>

        <?php echo $total_item; ?> item

    </span>

</div>

<div class="summary-row">

    <span>Original Price</span>

    <span>

        $
        <?php echo number_format($subtotal,2); ?>

    </span>

</div>

<div class="summary-row">

    <span>Discount (<?php echo $discount_percent; ?>%)</span>

    <span style="color:green;">

        - $
        <?php echo number_format($discount_price,2); ?>

    </span>

</div>

<div class="summary-row">

    <span>Price After Discount</span>

    <span style="color:#6675ff;
    font-weight:bold;">

        $
        <?php echo number_format($price_after_discount,2); ?>

    </span>

</div>

<div class="summary-row">

    <span>Shipping Fee</span>

    <span>

        
        free

    </span>

</div>

<div class="summary-row total">

    <span>Grand Total</span>

    <span>

        $
        <?php echo number_format($grand_total,2); ?>

    </span>

</div>

</div>

</div>


   <script>
document.getElementById("checkoutForm")
.addEventListener("submit", function(e){

    e.preventDefault();

    let formData = new FormData(this);

    fetch("telegram_send.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(order => {

        if(order.status === "success")
        {
            window.location.href = "Shopping.php";
        }

    })

});
</script>

</body>
</html>