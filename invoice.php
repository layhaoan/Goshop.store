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
    die("Invoice ID Missing");
}

$order_id = (int)$_GET['id'];

/*
|--------------------------------------------------------------------------
| ORDER
|--------------------------------------------------------------------------
*/

$orderQuery = $conn->query("
SELECT *
FROM test_orders
WHERE id='$order_id'
LIMIT 1
");

if($orderQuery->num_rows == 0){
    die("Invoice Not Found");
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

$subtotal = 0;

$itemQuery->data_seek(0);

$tax = 0;
$grandTotal = $order['subtotal'];

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="utf-8">
<meta name="viewport"
content="width=device-width,initial-scale=1">

<title>
Invoice #<?php echo $order['user_id']; ?>
</title>

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
max-width:1200px;
margin:auto;
}

.invoice-card{
background:var(--card);
padding:30px;
border-radius:20px;
box-shadow:0 5px 20px rgba(0,0,0,.08);
}

.topbar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:25px;
flex-wrap:wrap;
gap:15px;
}

.store-logo{
font-size:34px;
font-weight:700;
color:var(--primary);
}

.invoice-title{
text-align:right;
}

.invoice-title h1{
font-size:36px;
}

.info-grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:30px;
margin:25px 0;
}

.box{
padding:20px;
border:1px solid var(--border);
border-radius:15px;
}

.box h3{
margin-bottom:10px;
}

.table-wrap{
overflow:auto;
margin-top:20px;
}

table{
width:100%;
border-collapse:collapse;
}

table th{
background:var(--primary);
color:white;
padding:15px;
text-align:left;
}

table td{
padding:15px;
border-bottom:1px solid var(--border);
}

.product-img{
width:70px;
height:70px;
object-fit:cover;
border-radius:10px;
}

.summary{
width:350px;
margin-left:auto;
margin-top:25px;
}

.summary-row{
display:flex;
justify-content:space-between;
padding:10px 0;
}

.total{
font-size:26px;
font-weight:700;
color:var(--primary);
}

.actions{
margin-top:30px;
display:flex;
gap:15px;
flex-wrap:wrap;
}

.btn{
padding:12px 22px;
border:none;
border-radius:10px;
cursor:pointer;
text-decoration:none;
color:white;
font-size:15px;
}

.print-btn{
background:#10b981;
}

.back-btn{
background:#6b7280;
}

.dark-btn{
background:#111827;
}

.footer{
margin-top:40px;
text-align:center;
color:#777;
}

@media(max-width:768px){

.info-grid{
grid-template-columns:1fr;
}

.summary{
width:100%;
}

}

@media print{

.actions{
display:none;
}

body{
background:white;
padding:0;
}

.invoice-card{
box-shadow:none;
border:none;
}

}

</style>

</head>
<body>

<div class="container">

<div class="invoice-card">

<div class="topbar">

<div>

<div class="store-logo">
GoShop
</div>

<p>
Your Ecommerce Store
</p>

<p>
Phnom Penh, Cambodia
</p>

</div>

<div class="invoice-title">

<h1>INVOICE</h1>

<p>
Invoice No:
<strong>
<?php echo $order['user_id']; ?>
</strong>
</p>

<p>
Date:
<?php echo date(
'd M Y',
strtotime($order['created_at'])
);
?>
</p>

</div>

</div>

<div class="info-grid">

<div class="box">

<h3>
Bill To
</h3>

<p>
<strong>
<?php echo $order['fullname']; ?>
</strong>
</p>

<p>
<?php echo $order['email']; ?>
</p>

<p>
<?php echo $order['phone']; ?>
</p>

<p>
<?php echo $order['address']; ?>
</p>

</div>

<div class="box">

<h3>
Payment Information
</h3>

<p>
Method:
<strong>
<?php echo $order['payment_method']; ?>
</strong>
</p>

<p>
Status:
<strong>
<?php echo $order['order_status']; ?>
</strong>
</p>

</div>

</div>

<div class="table-wrap">

<table>

<thead>

<tr>
<th>Image</th>
<th>Product</th>
<th>Price</th>
<th>Qty</th>
<th>Total</th>
</tr>

</thead>

<tbody>

<?php while($item = $itemQuery->fetch_assoc()){ ?>

<tr>

<td>

<img
src="uploads/<?php echo $item['product_image']; ?>"
class="product-img">

</td>

<td>
<?php echo $item['product_name']; ?>
</td>

<td>
$<?php echo number_format($item['product_price'],2); ?>
</td>

<td>
<?php echo $item['product_qty']; ?>
</td>

<td>
$<?php echo number_format($item['product_total'],2); ?>
</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<div class="summary">

<div class="summary-row">
<span>Subtotal</span>
<span>
$<?php echo number_format($subtotal,2); ?>
</span>
</div>

<div class="summary-row">
<span>Tax</span>
<span>
$<?php echo number_format($tax,2); ?>
</span>
</div>

<hr>

<div class="summary-row total">
<span>Grand Total</span>
<span>
$<?php echo number_format($grandTotal,2); ?>
</span>
</div>

</div>

<div class="actions">

<button
onclick="window.print()"
class="btn print-btn">

<i class="fas fa-print"></i>
 Print Invoice

</button>

<a
href="customer_list.php"
class="btn back-btn">

<i class="fas fa-arrow-left"></i>
 Back

</a>

<button
id="darkBtn"
class="btn dark-btn">

🌙 Dark Mode

</button>

</div>

<div class="footer">

<p>
Thank you for shopping with GoShop.
</p>

<p>
Please keep this invoice for your records.
</p>

</div>

</div>

</div>

<script>

const darkBtn =
document.getElementById(
'darkBtn'
);

if(
localStorage.getItem(
'invoice_dark'
)==='on'
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
'invoice_dark',
'on'
);
}else{
localStorage.setItem(
'invoice_dark',
'off'
);
}

};

</script>

</body>
</html>