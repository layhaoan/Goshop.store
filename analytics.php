<?php
session_start();
$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "store_db"
);

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

/* ==========================
   ANALYTICS DATA
========================== */

$totalRevenue = 0;
$totalOrders = 0;
$totalCustomers = 0;
$totalProducts = 0;

$r = mysqli_query(
    $conn,
    "SELECT SUM(grand_total) grand_total FROM test_orders"
);

if($r){
    $row = mysqli_fetch_assoc($r);
    $totalRevenue = $row['grand_total'] ?? 0;
}

$r = mysqli_query(
    $conn,
    "SELECT COUNT(*) grand_total FROM test_orders"
);

if($r){
    $row = mysqli_fetch_assoc($r);
    $totalOrders = $row['grand_total'];
}

$r = mysqli_query(
    $conn,
    "SELECT COUNT(*) total FROM users"
);

if($r){
    $row = mysqli_fetch_assoc($r);
    $totalCustomers = $row['total'];
}

$r = mysqli_query(
    $conn,
    "SELECT COUNT(*) total FROM products"
);

if($r){
    $row = mysqli_fetch_assoc($r);
    $totalProducts = $row['total'];
}

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width,initial-scale=1">

<title>Analytics Dashboard</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

:root{
--primary:#2563eb;
--bg:#f4f6fb;
--card:#ffffff;
--text:#111827;
--border:#e5e7eb;
}

.dark{
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


/* MAIN */

.main{
margin-left:260px;
padding:25px;
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


/* CARDS */

.cards{
    margin-top:25px;
    display:grid;
    grid-template-columns:
    repeat(auto-fit,minmax(100px,1fr));
    gap:20px;
}

.card{
    background:var(--card);
    padding:20px;
    border-radius:18px;
    box-shadow:0 5px 15px rgba(0,0,0,.05);
    transition:.3s;
}

.card:hover{
    transform:translateY(-5px);
}

.card h3{
    margin-bottom:10px;
}

/* CHART */

.header{
    display: flex;
    width: 100%;
    justify-content: space-between;
}

.chart-box{
    margin-top:25px;
    background:var(--card);
    padding: 20px;
    border-radius:18px;
    width: 68%;
}

/* TABLE */

.table-box{
    margin-top:25px;
    background:var(--card);
    border-radius:18px;
    overflow:auto;
    width: 30%;
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

/* MOBILE */

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

.search input{
width:100%;
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
            <a href="new_dashboard.php">
                <i class="fa-solid fa-house"></i>
                <span class="text">Dashboard</span>
            </a>
        <?php } ?>

        <?php if($role == "Admin" || $role == "Manager"| $role == "Staff"){ ?>
            <a href="analytics.php" class="active">
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

<div class="main">

    <div class="topbar">

        <h2>Analytics</h2>


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
            <h3>Total Customers</h3>
            <h1><?= number_format($totalCustomers) ?></h1>
        </div>

        <div class="card">
            <h3>Total Products</h3>
            <h1><?= number_format($totalProducts) ?></h1>
        </div>

    </div>

    <div class="header">
        <div class="chart-box">
            <h2>Revenue Overview</h2>
            <canvas id="salesChart"></canvas>
        </div>

        <div class="table-box">
            <table>
                <tr>
                    <th>Metric</th>
                    <th>Value</th>
                </tr>

                <tr>
                    <td>Total Revenue</td>
                    <td>$<?= number_format($totalRevenue,2) ?></td>
                </tr>

                <tr>
                    <td>Total Orders</td>
                    <td><?= $totalOrders ?></td>
                </tr>

                <tr>
                    <td>Total Customers</td>
                    <td><?= $totalCustomers ?></td>
                </tr>

                <tr>
                    <td>Total Products</td>
                    <td><?= $totalProducts ?></td>
                </tr>
            </table>
        </div>
    </div>

</div>

<script>

function toggleDark(){

document.body.classList.toggle(
'dark'
);

localStorage.setItem(
'darkMode',
document.body.classList.contains(
'dark'
)
);

}

if(
localStorage.getItem(
'darkMode'
)==='true'
){
document.body.classList.add(
'dark'
);
}

const ctx =
document.getElementById(
'salesChart'
);

new Chart(ctx,{

type:'line',

data:{
labels:[
'Jan',
'Feb',
'Mar',
'Apr',
'May',
'Jun'
],

datasets:[{

label:'Revenue',

data:[
1200,
2200,
1800,
3200,
4100,
5200
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