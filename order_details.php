<?php

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "store_db"
);

if($conn->connect_error){
    die("Connection Failed");
}

if(!isset($_GET['id'])){
    die("Order ID Missing");
}

$order_id = (int)$_GET['id'];

/*
|--------------------------------------------------------------------------
| ORDER INFORMATION
|--------------------------------------------------------------------------
*/

$orderQuery = $conn->query("
SELECT *
FROM test_orders
WHERE id='$order_id'
LIMIT 1
");

if($orderQuery->num_rows == 0){
    die("Order Not Found");
}

$order = $orderQuery->fetch_assoc();

/*
|--------------------------------------------------------------------------
| ORDER ITEMS
|--------------------------------------------------------------------------
*/

$itemQuery = $conn->query("
SELECT *
FROM test_order_items
WHERE order_id='$order_id'
ORDER BY id ASC
");




$itemQuery->data_seek(0);

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="utf-8">
<meta name="viewport"
content="width=device-width,initial-scale=1">

<title>Order Details</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Segoe UI',sans-serif;
}

:root{
--primary:#6c4cf5;
--bg:#f5f7fb;
--card:#ffffff;
--text:#222;
--border:#e5e7eb;
}

body.dark{
--bg:#111827;
--card:#1f2937;
--text:#ffffff;
--border:#374151;
}

body{
background:var(--bg);
color:var(--text);
padding:25px;
transition:.3s;
}

.container{
max-width:1400px;
margin:auto;
}

.topbar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;
flex-wrap:wrap;
gap:15px;
}

.title{
font-size:34px;
font-weight:700;
}

.dark-btn{
width:50px;
height:50px;
border:none;
background:var(--primary);
color:white;
border-radius:12px;
cursor:pointer;
}

.card{
background:var(--card);
border-radius:20px;
padding:25px;
box-shadow:0 5px 15px rgba(0,0,0,.08);
margin-bottom:20px;
}

.grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:20px;
}

.info-row{
margin-bottom:15px;
}

.label{
font-weight:700;
margin-bottom:5px;
}

.value{
opacity:.9;
}

.status{
display:inline-block;
padding:8px 14px;
border-radius:30px;
font-size:13px;
font-weight:600;
}

.pending{
background:#fff3cd;
color:#856404;
}

.completed{
background:#d4edda;
color:#155724;
}

.cancelled{
background:#f8d7da;
color:#721c24;
}

.products-title{
font-size:26px;
margin-bottom:20px;
}

.product{
display:flex;
gap:20px;
padding:20px 0;
border-bottom:1px solid var(--border);
}

.product:last-child{
border-bottom:none;
}

.product img{
width:120px;
height:120px;
border-radius:15px;
object-fit:cover;
}

.product-info{
flex:1;
}

.product-name{
font-size:20px;
font-weight:600;
margin-bottom:8px;
}

.price{
color:var(--primary);
font-weight:700;
font-size:18px;
}

.summary{
margin-top:25px;
padding-top:20px;
border-top:2px solid var(--border);
}

.summary-row{
display:flex;
justify-content:space-between;
margin-bottom:12px;
font-size:17px;
}

.total{
font-size:24px;
font-weight:700;
color:var(--primary);
}

.actions{
margin-top:25px;
display:flex;
gap:10px;
flex-wrap:wrap;
}

.btn{
padding:12px 20px;
border:none;
border-radius:10px;
cursor:pointer;
text-decoration:none;
color:white;
}

.back-btn{
background:#6b7280;
}

.print-btn{
background:#10b981;
}

.invoice-btn{
background:var(--primary);
}

@media(max-width:768px){

.grid{
grid-template-columns:1fr;
}

.product{
flex-direction:column;
}

.product img{
width:100%;
height:250px;
}

}

@media print{

.dark-btn,
.actions{
display:none;
}

body{
background:white;
}

.card{
box-shadow:none;
}

}

</style>

</head>
<body>

<div class="container">

<div class="topbar">

<h1 class="title">
Order #<?php echo $order['user_id']; ?>
</h1>

<button
id="darkBtn"
class="dark-btn">
🌙
</button>

</div>

<div class="card">

<div class="grid">

<div>

<h2>Customer Information</h2>

<br>

<div class="info-row">
<div class="label">Customer Name</div>
<div class="value">
<?php echo $order['fullname']; ?>
</div>
</div>

<div class="info-row">
<div class="label">Email</div>
<div class="value">
<?php echo $order['email']; ?>
</div>
</div>

<div class="info-row">
<div class="label">Phone</div>
<div class="value">
<?php echo $order['phone']; ?>
</div>
</div>

<div class="info-row">
<div class="label">Address</div>
<div class="value">
<?php echo $order['address']; ?>
</div>
</div>

</div>

<div>

<h2>Order Information</h2>

<br>

<div class="info-row">
<div class="label">Payment Method</div>
<div class="value">
<?php echo $order['payment_method']; ?>
</div>
</div>

<div class="info-row">
<div class="label">Order Date</div>
<div class="value">
<?php echo $order['created_at']; ?>
</div>
</div>

<div class="info-row">
<div class="label">Order Status</div>

<div class="value">

<?php
$status = strtolower($order['order_status']);
?>

<span class="status <?php echo $status; ?>">
<?php echo $order['order_status']; ?>
</span>

</div>

</div>

</div>

</div>

</div>

<div class="card">

<h2 class="products-title">
Products Purchased
</h2>

<?php while($item = $itemQuery->fetch_assoc()){ ?>

<div class="product">

<img
src="uploads/<?php echo $item['product_image']; ?>"
>

<div class="product-info">

<div class="product-name">
<?php echo $item['product_name']; ?>
</div>

<p>
Quantity:
<strong>
<?php echo $item['product_qty']; ?>
</strong>
</p>

<p>
Unit Price:
<strong>
$<?php echo number_format($item['product_price'],2); ?>
</strong>
</p>

<p class="price">
Total:
$<?php echo number_format($item['product_total'],2); ?>
</p>

</div>

</div>

<?php } ?>

<div class="summary">

    <div class="summary-row">
        <span>Total item</span>
        <span class="total">
        <?php echo number_format($order['total_item']); ?>
        </span>
    </div>

    <div class="summary-row">
        <span>Discount Percent</span>
        <span class="total">
        $<?php echo number_format($order['discount_percent'],2); ?>
        </span>
    </div>

    <div class="summary-row">
        <span>Discount Price</span>
        <span class="total">
        $<?php echo number_format($order['discount_price'],2); ?>
        </span>
    </div>

    <div class="summary-row">
        <span>Shipping</span>
        <span class="total">
        $<?php echo number_format($order['shipping_fee'],2); ?>
        </span>
    </div>

    <div class="summary-row">
        <span>Total</span>
        <span class="total">
        $<?php echo number_format($order['subtotal'],2); ?>
        </span>
    </div>

    <div class="summary-row">
        <span>Grand Total</span>
        <span class="total">
        $<?php echo number_format($order['grand_total'],2); ?>
        </span>
    </div>

</div>

<div class="actions">

<a
href="customer_list.php"
class="btn back-btn">
Back
</a>

<button
onclick="window.print()"
class="btn print-btn">
Print Order
</button>

<a
href="invoice.php?id=<?php echo $order['id']; ?>"
class="btn invoice-btn">
Invoice
</a>

</div>

</div>

</div>

<script>

const darkBtn =
document.getElementById(
'darkBtn'
);

if(
localStorage.getItem('darkmode')
==='on'
){
document.body.classList.add(
'dark'
);
}

darkBtn.onclick = function(){

document.body.classList.toggle(
'dark'
);

if(
document.body.classList.contains(
'dark'
)
){
localStorage.setItem(
'darkmode',
'on'
);
}else{
localStorage.setItem(
'darkmode',
'off'
);
}

};

</script>

</body>
</html>