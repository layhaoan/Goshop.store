<?php
session_start();

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "store_db"
);

if(!$conn){
    die("Connection Failed");
}

if(!isset($_GET['id'])){
    die("Customer ID Missing");
}

$id = (int)$_GET['id'];

/* =========================================================
GET AN IMAGE OF USER TO SHOW OR DIPLAY ON VIEW CUSTOMER
========================================================= */
$id = (int)$_GET['id'];

$userQuery = mysqli_query($conn,"
SELECT *
FROM users
WHERE id='$id'
");

$user = mysqli_fetch_assoc($userQuery);

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
   CUSTOMER INFO
========================= */

$customerQuery = mysqli_query($conn,"
SELECT *
FROM test_orders
WHERE id='$id'
");

if(mysqli_num_rows($customerQuery) == 0){
    die("Customer Not Found");
}

$customer = mysqli_fetch_assoc(
$customerQuery
);

/* =========================
   CUSTOMER STATS
========================= */

$stats = mysqli_fetch_assoc(
mysqli_query(
$conn,
"
SELECT

COUNT(*) test_orders,

COALESCE(
SUM(grand_total),
0
) total_spent

FROM test_orders

WHERE id='$id'
"
)
);

$totalOrders = $stats['test_orders'];
$totalSpent = $stats['total_spent'];

/* =========================
   CUSTOMER ORDERS
========================= */

$orders = mysqli_query(
$conn,
"
SELECT *
FROM test_orders
WHERE id='$id'
ORDER BY id DESC
"
);

$purchasedProducts = mysqli_query($conn,"
    SELECT
        o.*,
        oi.order_id,
        oi.code,
        oi.product_name,
        oi.product_image,
        oi.product_qty,
        oi.product_price,
        oi.product_total
    FROM test_orders o
    LEFT JOIN test_order_items oi ON o.id = oi.order_id
    WHERE o.id = '$id' ORDER BY o.id DESC
");



?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width,initial-scale=1">

<title>Customer Profile</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

:root{
    --primary:#2563eb;
    --success:#10b981;
    --danger:#ef4444;
    --warning:#f59e0b;

    --bg:#f5f7fb;
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

/* SIDEBAR */

.sidebar{
    position:fixed;
    left:0;
    top:0;
    width:260px;
    height:100%;
    background:var(--card);
    border-right:1px solid var(--border);
    z-index:999;
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
    padding:14px;
    border-radius:12px;
    text-decoration:none;
    color:var(--text);
    margin-bottom:8px;
    transition:.3s;
}

.menu a:hover,
.menu a.active{
    background:var(--primary);
    color:white;
}

/* MAIN */

.main{
    margin-left:260px;
    padding:25px;
}

/* TOPBAR */

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:15px;
}

.back-btn{
    background:var(--primary);
    color:white;
    padding:12px 18px;
    border-radius:10px;
    text-decoration:none;
}


/* PROFILE */

.profile-card{
    background:var(--card);
    border-radius:20px;
    padding:25px;
    display:flex;
    align-items:center;
    gap:25px;
    margin-top:25px;
    flex-wrap:wrap;
}

.avatar{
    width:120px;
    height:120px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid var(--primary);
}

.profile-info h2{
    margin-bottom:10px;
}

.profile-info p{
    margin:8px 0;
}

/* STATS */

.stats{
    margin-top:25px;
    display:grid;
    grid-template-columns:
    repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
}

.card{
    background:var(--card);
    padding:20px;
    border-radius:16px;
}

/* TABLE */

.table-card{
    margin-top:25px;
    background:var(--card);
    border-radius:16px;
    overflow: hidden;
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
    padding: 24px;
    text-align:left;
}

tr{
    border-bottom:1px solid var(--border);
}

/* STATUS */

.status{
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    color:white;
}

.completed{
    background:var(--success);
}

.pending{
    background:var(--warning);
}

.cancelled{
    background:var(--danger);
}

/* RESPONSIVE */

@media(max-width:992px){

.sidebar{
    width:80px;
}

.sidebar .text{
    display:none;
}

.logo{
    text-align:center;
    font-size:18px;
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
            <a href="new_dashboard.php">
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

            <a href="customers.php" class="active">
                <i class="fa-solid fa-users"></i>
                <span class="text">Customers</span>
            </a>

            <a href="reports.php">
                <i class="fa-solid fa-file-lines"></i>
                <span class="text">Reports</span>
            </a>
        <?php } ?>

        <?php if($role == "Admin" || $role == "Manager"){ ?>

            <a href="#">
                <i class="fa-solid fa-gear"></i>
                <span class="text">Settings</span>
            </a>
        <?php } ?>

    </div>
</div>

<!-- MAIN -->

<div class="main">

<div class="topbar">

<a
href="customers.php"
class="back-btn">

<i class="fa-solid fa-arrow-left"></i>
Back

</a>


</div>

<!-- PROFILE -->

<div class="profile-card">


    <img src="uploads/<?= !empty($user['profile_image'])? $user['profile_image']: 'assets/default-user.png' ?>"class="avatar">

    <div class="profile-info">

        <h2><?= htmlspecialchars($customer['fullname']) ?> </h2>
        <p> <b>Email:</b> <?= htmlspecialchars($customer['email']) ?></p>
        <p><b>Phone:</b> <?= htmlspecialchars($customer['phone']) ?></p>
        <p><b>Address:</b> <?= htmlspecialchars($customer['address']) ?></p>
        <p><b>Payment:</b> <?= htmlspecialchars($customer['payment_method']) ?></p>
        <p><b>Joined:</b> <?= date('d M Y',strtotime($customer['created_at'])) ?></p>

    </div>

</div>

<!-- STATS -->

<div class="stats">

<div class="card">

<h4>Total Orders</h4>

<h2>
<?= number_format(
$totalOrders
) ?>
</h2>

</div>

<div class="card">

<h4>Total Spent</h4>

<h2>

$
<?= number_format(
$totalSpent,
2
) ?>

</h2>

</div>

</div>

<!-- ORDER HISTORY -->

<div class="table-card">

<h2 style="padding:20px;">
Purchased Products
</h2>

<table>

<tr>
    <th>Image</th>
    <th>Product</th>
    <th>Price</th>
    <th>Qty</th>
    <th>Subtotal</th>
    <th>Status</th>
</tr>

<?php while($item=mysqli_fetch_assoc($purchasedProducts)): ?>

<tr>
    <td> <img src="uploads/<?= htmlspecialchars($item['product_image']) ?>" class="product-image" style="width: 100px;"></td>
    <td><?= htmlspecialchars($item['product_name']) ?></td>
    <td>$<?= number_format($item['product_price'],2) ?></td>
    <td><?= htmlspecialchars($item['product_qty']) ?></td>
    <td>$<?= number_format($item['product_total'],2) ?></td>

<td>

<span class="status <?= strtolower($item['order_status']) ?>">

<?= htmlspecialchars(
$item['order_status']
) ?>

</span>

</td>

</tr>

<?php endwhile; ?>

</table>

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

</script>

</body>
</html>