<?php
session_start();

$conn = new mysqli("localhost","root","","store_db");

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

if(isset($_POST['update_status']))
{
    $id = intval($_POST['id']);
    $order_status = $_POST['order_status'];

    $stmt = $conn->prepare(
        "UPDATE test_orders SET order_status=? WHERE id=?"
    );
    $stmt->bind_param("si",$order_status,$id);
    $stmt->execute();

    echo "<script>
    alert('Status Updated');
    window.location='new_orders.php';
    </script>";
    exit;
}

$search = $_GET['search'] ?? '';


/* ===========================
   ORDER COUNTS
=========================== */

$totalOrders = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) grand_total FROM test_orders")
)['grand_total'];

$pendingOrders = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) grand_total
        FROM test_orders
        WHERE order_status='Pending'
    ")
)['grand_total'];

$completedOrders = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) grand_total
        FROM test_orders
        WHERE order_status='Completed'
    ")
)['grand_total'];

$cancelledOrders = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) grand_total
        FROM test_orders
        WHERE order_status='Cancelled'
    ")
)['grand_total'];


$sql = "
SELECT *
FROM test_orders
WHERE id LIKE '%$search%'
OR fullname LIKE '%$search%'
ORDER BY id DESC
";

$sql = "
SELECT
    o.*,
    oi.product_name,
    oi.product_image,
    oi.product_qty
FROM test_orders o
LEFT JOIN test_order_items oi
ON o.id = oi.order_id
ORDER BY o.id DESC
";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Orders Management</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

:root{
--primary:#2563eb;
--bg:#f5f7fb;
--card:#fff;
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
background:#f5f7fb;
display:flex;
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
z-index:1000;
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
text-decoration:none;
color:var(--text);
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
.dark-btn{
    border:none;
    cursor:pointer;
    background:var(--primary);
    color:white;
    padding: 12px 16px;
    border-radius:10px;
    font-weight: bold;
}


/* MAIN */

.main{
    margin-left:260px;
    padding:25px;
    width:100%;
}

.top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.search-box{
    width:300px;
}

.search-box input{
    width:100%;
    padding:12px;
    border-radius:8px;
    border:1px solid #ddd;
}

/* CARDS */

.cards{
    margin-top:20px;
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(160px,1fr));
    gap:20px;
    padding: 10px 10px;
}

.card{
    background:var(--card);
    padding:20px;
    border-radius:16px;
}

.card h2{
    margin-top:10px;
}

/* TABLE */

table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
    position: relative;
}

table th{
    background:#2563eb;
    color:white;
    padding:12px;
    font-size: 0.85rem;
    text-align: left;
}

table td{
    padding: 8px;
    border-bottom:1px solid #eee;
    text-align:center;
    font-size: 0.85rem;
    text-align: left;
}

.pending{
    background:#fff3cd;
    color:#856404;
    padding:5px 12px;
    border-radius:20px;
    font-size: 0.85rem;
}

.success{
    background:#d1fae5;
    color:#065f46;
    padding:5px 12px;
    border-radius:20px;
    font-size: 0.85rem;
}

select{
    padding:8px;
    border-radius:6px;
}

button{
    background:#2563eb;
    color:white;
    border:none;
    padding:10px 15px;
    border-radius:8px;
    cursor:pointer;
}

button:hover{
background:#1d4ed8;
}

img{
border-radius:8px;
}

/* Responsive */

@media(max-width:900px)
{
.sidebar{
width:80px;
}

.main{
margin-left:80px;
}

.logo{
font-size:18px;
}

.menu a{
font-size:12px;
padding:12px;
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

            <a class="active" href="new_orders.php">
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

    <div class="top">

        <h2>Orders Management</h2>

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

     <!-- STATS -->

    <div class="cards">

        <div class="card">
            <p>Total Orders</p>
            <h2><?= $totalOrders ?></h2>
        </div>

        <div class="card">
            <p>Pending</p>
            <h2><?= $pendingOrders ?></h2>
        </div>

        <div class="card">
            <p>Completed</p>
            <h2><?= $completedOrders ?></h2>
        </div>

        <div class="card">
            <p>Cancelled</p>
            <h2><?= $cancelledOrders ?></h2>
        </div>

    </div>

    <div class="card">

        <table>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Product Name</th>
                <th>User Name</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Proof</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php while($row=$result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td> <img src='uploads/<?php echo $row['product_image']; ?>' width='70' height='70' style='border-radius:10px;object-fit:cover;'> </td>
                    <td><?= $row['product_name'] ?></td>
                    <td><?= $row['fullname'] ?></td>
                    <td>$<?= number_format($row['grand_total'],2) ?></td>
                    <td> <?php if($row['payment_method']=='QR') echo "QR Payment"; else echo "Cash Delivery"; ?> </td>

                    <td>
                        <?php if($row['payment_method']=='QR'): ?>
                            <a href="<?= $row['payment_proof'] ?>" target="_blank">
                                <img src="uploads/<?= $row['payment_proof'] ?>" width="60">
                            </a>
                            <?php else: ?>
                            No Proof
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if($row['order_status']=='Pending'): ?>
                            <span class="pending"> Pending </span>
                        <?php else: ?>
                            <span class="success"> Success </span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <select name="order_status">
                                <option value="Pending" <?= $row['order_status']=='Pending'?'selected':'' ?>> Pending </option>
                                <option value="Success" <?= $row['order_status']=='Success'?'selected':'' ?>> Success </option>
                            </select>
                            <button type="submit" name="update_status"> Update </button>
                        </form>

                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

    </div>

</div>

</body>
</html>