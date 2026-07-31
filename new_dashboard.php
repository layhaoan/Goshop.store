<?php
session_start();
$conn = mysqli_connect("localhost","root","","store_db");

if(!$conn){
    die("Database Connection Failed");
}

/* =========================================================
CHECK LOGIN
========================================================= */

if(!isset($_SESSION['admin_id'])){

    header("Location: admin_auth.php");
    exit();

}

/* =========================================================
GET STAFFS
========================================================= */

$get_staff =
"
SELECT *
FROM staff
ORDER BY id DESC
";

$staffs_result =
$conn->query($get_staff);

/* =========================================================
ADMIN INFO
========================================================= */

$admin_name = "Administrator";
$admin_image = "default.png";

if(isset($_SESSION['admin_name'])){
    $admin_name =
    $_SESSION['admin_name'];
}

if(isset($_SESSION['admin_image'])){
    $admin_image =
    $_SESSION['admin_image'];
}

/* =========================================================
FIX ROLE WARNING
========================================================= */

/*
This fixes:

Warning: Undefined array key "role"

*/

$role = "Staff";

if(isset($_SESSION['role'])){

    $role =
    $_SESSION['role'];

}


/* =========================
   DASHBOARD STATISTICS
========================= */

$totalRevenue = 0;
$totalOrders = 0;
$totalProducts = 0;
$totalCustomers = 0;

$q = mysqli_query($conn,"SELECT SUM(grand_total) AS grand_total FROM test_orders");
if($q){
    $row = mysqli_fetch_assoc($q);
    $totalRevenue = $row['grand_total'] ?? 0;
}

$q = mysqli_query($conn,"SELECT COUNT(*) AS total FROM test_orders");
if($q){
    $row = mysqli_fetch_assoc($q);
    $totalOrders = $row['total'];
}

$q = mysqli_query($conn,"SELECT COUNT(*) AS total FROM products");
if($q){
    $row = mysqli_fetch_assoc($q);
    $totalProducts = $row['total'];
}

$q = mysqli_query($conn,"SELECT COUNT(*) AS total FROM users");
if($q){
    $row = mysqli_fetch_assoc($q);
    $totalCustomers = $row['total'];
}

/* =========================
   RECENT ORDERS
========================= */

$recentOrders = mysqli_query(
$conn,
"SELECT * FROM test_orders ORDER BY id DESC LIMIT 10"
);

/* =========================
   PRODUCTS
========================= */

$products = mysqli_query(
$conn,
"SELECT * FROM products ORDER BY id DESC LIMIT 10"
);

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">

<title>GoShop Dashboard</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

:root{
    --primary:#2563eb;
    --success:#10b981;
    --warning:#f59e0b;
    --danger:#ef4444;

    --bg:#f4f6fb;
    --card:#ffffff;
    --text:#111827;
    --border:#e5e7eb;
}

body.dark{
    --bg:#0f172a;
    --card:#1e293b;
    --text:#f8fafc;
    --border:#334155;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Segoe UI,sans-serif;
}

body{
    background:var(--bg);
    color:var(--text);
}

/* ==========================
SIDEBAR
========================== */

.sidebar{
    position:fixed;
    left:0;
    top:0;
    width:260px;
    height:100%;
    background:var(--card);
    border-right:1px solid var(--border);
    z-index:999;
    transition:.3s;
}

.logo{
    padding:25px;
    font-size:26px;
    font-weight:bold;
}

.menu{
    padding:10px;
}

.menu a{
    display:flex;
    align-items:center;
    gap:15px;
    text-decoration:none;
    color:var(--text);
    padding:14px;
    border-radius:12px;
    margin-bottom:8px;
    transition:.3s;
}

.menu a:hover,
.menu a.active{
    background:var(--primary);
    color:#fff;
}

/* ADMIN PROFILE */

.admin-profile{
    display:flex;
    align-items: right;
    gap:15px;
}

.admin-profile img{
    width:60px;
    height:60px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid #2563ff;
}

/* Toggle */

.dark-btn{
    border:none;
    cursor:pointer;
    background:var(--primary);
    color:white;
    padding: 12px 16px;
    border-radius:10px;
    font-weight: bold;
}


/* ==========================
MAIN CONTENT
========================== */

.main{
    margin-left:260px;
    padding:25px;
    transition:.3s;
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:15px;
}

.search-box{
    width:350px;
    max-width:100%;
    position:relative;
}

.search-box input{
    width:100%;
    border:none;
    outline:none;
    padding:14px 18px 14px 50px;
    border-radius:14px;
    background:var(--card);
    transition:.4s;
}

.search-box input:focus{
    transform:scale(1.03);
}

.search-box i{
    position:absolute;
    left:18px;
    top:15px;
}

/* =========================
   CARDS
========================= */

.cards{
    display:grid;
    grid-template-columns:
    repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin-top:25px;
}

.card{
    background:var(--card);
    padding:20px;
    border-radius: 18px;
    box-shadow:0 5px 15px rgba(0,0,0,.05);
    transition:.3s;
}

.card:hover{
    transform:translateY(-5px);
}

.card h3{
    font-size: 1rem;
    color: #777;
}

/* =========================
   CHART
========================= */

.chart-card{
    margin-top:25px;
    background:var(--card);
    padding:20px;
    border-radius:18px;
}

/* =========================
   TABLES
========================= */

.section{
    margin-top:25px;
    background:var(--card);
    border-radius:18px;
    overflow:auto;
}

.section-header{
    padding:20px;
    font-size:20px;
    font-weight:600;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:var(--primary);
    color:white;
}

th,td{
    padding:14px;
    text-align:left;
}

tr{
    border-bottom:1px solid var(--border);
}

/* =========================
   STATUS
========================= */

.status{
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    color: #fff;
    background: #4a56b3;
}

.pending{
    background:var(--warning);
}

.success{
    background:var(--success);
}

.cancelled{
    background:var(--danger);
}

/* =========================
   MOBILE
========================= */

@media(max-width:992px){

.sidebar{
width:80px;
}

.logo{
font-size:18px;
text-align:center;
}

.menu span{
display:none;
}

.main{
margin-left:80px;
}

}

</style>

</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="logo">
        GOSHOP
    </div>

    <div class="menu">
        <?php if($role == "Admin" || $role == "Manager"){ ?>
            <a href="#" class="active">
                <i class="fa-solid fa-house"></i>
                <span class="text">Dashboard</span>
            </a>
        <?php } ?>

        <?php if($role == "Admin" || $role == "Manager"| $role == "Staff"){ ?>
            <a href="analytics.php">
                <i class="fa-solid fa-chart-line"></i>
                <span class="text">Analytics</span>
            </a>

            <a href="new_products.php">
                <i class="fa-solid fa-box"></i>
                <span class="text">Products</span>
            </a>

            <a href="new_orders.php">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="text">Orders</span>
            </a>

            <a href="customers.php">
                <i class="fa-solid fa-users"></i>
                <span class="text">Customers</span>
            </a>

            <a href="staff_list.php">
                <i class="fa-solid fa-user-tie"></i>
                <span class="text">Staff List</span>
            </a>

            <a href="reports.php">
                <i class="fa-solid fa-file-lines"></i>
                <span class="text">Reports</span>
            </a>
        <?php } ?>

        <?php if($role == "Admin" || $role == "Manager"){ ?>

            <a href="setting.php">
                <i class="fa-solid fa-gear"></i>
                <span class="text">Settings</span>
            </a>
        <?php } ?>

    </div>
</div>

<!-- MAIN -->

<div class="main">

    <div class="topbar">

        <h2>Dashboard</h2>


        <div class="admin-profile">

            <img src="uploads/<?php echo $admin_image; ?>" onerror="this.src='uploads/default.png'" >

            <div>
                <h4>  <?php echo $admin_name; ?> </h4>
                <p> <?php echo $role; ?> </p>
            </div>

            <div>
                <a href="staff_login.php">
                    <button class="dark-btn"> Log out </button>
                </a>
            </div>

        </div>


    </div>


    <!-- CARDS -->

    <div class="cards">

        <div class="card">
            <h3>Total Revenue</h3>
            <h1>$<?= number_format($totalRevenue,2) ?></h1>
        </div>

        <div class="card">
            <h3>Total Orders</h3>
            <h1><?= number_format($totalOrders) ?></h1>
        </div>

        <div class="card">
            <h3>Total Products</h3>
            <h1><?= number_format($totalProducts) ?></h1>
        </div>

        <div class="card">
            <h3>Total Customers</h3>
            <h1><?= number_format($totalCustomers) ?></h1>
        </div>

    </div>


<!-- RECENT ORDERS -->

<div class="section">

    <div class="section-header"> Recent Orders </div>
        <table>

            <tr>
                <th>ID</th>
                <th>Usr Name</th>
                <th>Total</th>
                <th>Status</th>
            </tr>

            <?php
                if($recentOrders){
                while($row=mysqli_fetch_assoc($recentOrders)){
                ?>

                    <tr>

                        <td>#<?= $row['id'] ?></td>
                        <td> <?= htmlspecialchars( $row['fullname'] ?? 'Product' ) ?> </td>
                        <td> $<?= number_format($row['grand_total'] ?? 0,2) ?> </td>
                        <td>
                            <?php $status = strtolower( $row['order_status'] ?? 'pending');?>
                            <span class="status <?= $status ?>"> <?= ucfirst($status) ?> </span>
                        </td>

                    </tr>

                <?php
                }}
            ?>

        </table>

    </div>

    <!-- PRODUCTS -->

    <div class="section">

        <div class="section-header"> Latest Products </div>
        <table>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Product</th>
                <th>Price</th>
                <th>Discount</th>
                <th>Stock</th>
                <th>Product category</th>
            </tr>

            <?php
                if($products){
                while($row=mysqli_fetch_assoc($products)){
                ?>

                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td> <img src='uploads/<?php echo $row['image']; ?>' width='70' height='70' style='border-radius:10px;object-fit:cover;'> </td>
                        <td> <?= htmlspecialchars( $row['name'] ?? 'Product' ) ?> </td>
                        <td> $<?= number_format( $row['price'] ?? 0, 2 ) ?> </td>
                        <td> $<?= number_format( $row['discount'] ?? 0, 2 ) ?> </td>
                        <td> <?= number_format( $row['stock'] ?? 'Product' ) ?> </td>
                        <td> <?= htmlspecialchars( $row['product_category'] ?? 'Product' ) ?> </td>
                    </tr>

                <?php
                }}
            ?>

        </table>

    </div>

</div>

<script>



const ctx =
document.getElementById(
'salesChart'
);

new Chart(ctx,{
type:'line',
data:{
labels:[
'Jan','Feb','Mar','Apr','May','Jun'
],
datasets:[{
label:'Sales',
data:[
1200,2200,1800,3500,4200,5800
],
borderWidth:3,
tension:.4
}]
},
options:{
responsive:true
}
});

</script>

</body>
</html>