<?php

session_start();

$conn = mysqli_connect("localhost","root","","store_db");

if(!$conn){
    die("Database connection failed");
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


/* ======================
   SEARCH
====================== */

$search = $_GET['search'] ?? '';

$where = '';

if($search != ''){

    $search = mysqli_real_escape_string(
        $conn,
        $search
    );

    $where = "
        WHERE
        product_name LIKE '%$search%'
        OR category LIKE '%$search%'
    ";
}

/* ==========================
   REPORT STATISTICS
========================== */

$totalProducts = mysqli_fetch_assoc(
    mysqli_query($conn,"
        SELECT COUNT(*) grand_total
        FROM products
    ")
)['grand_total'];

$totalCustomers = mysqli_fetch_assoc(
    mysqli_query($conn,"
        SELECT COUNT(*) grand_total
        FROM test_orders
    ")
)['grand_total'];

$totalOrders = mysqli_fetch_assoc(
    mysqli_query($conn,"
        SELECT COUNT(*) grand_total
        FROM test_orders
    ")
)['grand_total'];

$totalSales = mysqli_fetch_assoc(
    mysqli_query($conn,"
        SELECT IFNULL(SUM(product_total),0) product_total
        FROM test_order_items
    ")
)['product_total'];

/* ==========================
   EXPORT REPORT CSV
========================== */

if(isset($_POST['export_csv']))
{
    header('Content-Type:text/csv');
    header('Content-Disposition:attachment; filename=goshop_report.csv');

    $output = fopen("php://output","w");

    fputcsv($output,
    [
        'Total Sales',
        'Total Orders',
        'Total Customers',
        'Total Products'
    ]);

    fputcsv($output,
    [
        $totalSales,
        $totalOrders,
        $totalCustomers,
        $totalProducts
    ]);

    fclose($output);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>GoShop Reports</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
:root{
    --primary:#2563eb;
    --bg:#f5f7fb;
    --card:#ffffff;
    --text:#222;
    --border:#e5e7eb;
}

body.dark{
    --bg:#121826;
    --card:#1b2435;
    --text:#fff;
    --border:#2d3748;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    background:#f5f7fb;
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

.dark-btn{
    border:none;
    cursor:pointer;
    background:var(--primary);
    color:white;
    padding:12px 16px;
    border-radius:10px;
   font-weight: bold;
}

/* =======================
   CONTENT
======================= */

.main{
    margin-left: 250px;
    padding: 30px;
    position: relative;
    bottom: 35px;
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

.page-title{
    font-size: 28px;
    font-weight:700;
}

.header-box{
    display: flex;
    margin: 10px;
}

.search-box{
    position:relative;
    width: 780px;
    max-width:100%;
    display: flex;
}

.search-box input{
    width:100%;
    padding: 14px 14px 14px 35px;
    border:none;
    outline:none;
    background: #fff;
    border-radius:12px;
    transition:.3s;
    position: relative;
}

.search-box input:focus{
    transform:scale(1.03);
}

.search-box i{
    position:absolute;
    right:15px;
    top:15px;
}

.export-btn{
    background:#22c55e;
    color:white;
    border:none;
    padding: 14px 28px;
    border-radius: 8px;
    cursor:pointer;
    font-size:15px;
    position: relative;
    left: 10px;
}

/* =======================
   CARDS
======================= */

.cards{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:25px;
    margin-bottom:35px;
}

.card{
    background:white;
    border-radius:24px;
    padding:30px;
}

.card h4{
    color:#6b7280;
    margin-bottom:10px;
}

.card h2{
    font-size: 1.2rem;
    color:#111827;
}

/* =======================
   TABLE
======================= */

.table-box{
    background:white;
    border-radius:24px;
    overflow:hidden;
    margin-bottom:30px;
}

.table-title{
    padding: 25px;
    font-size: 1.2rem;
    font-weight:600;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#2563eb;
    color:white;
    padding: 18px;
    text-align: left;
    font-size: 0.78rem
}

td{
    padding: 18px;
    border-bottom:1px solid #eee;
    text-align: left;
    font-size: 0.72rem
}

/* =======================
   TOP PRODUCTS
======================= */

.products-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
}

.product-card{
    background:white;
    border-radius:20px;
    padding:25px;
}

.product-card h3{
    margin-bottom:10px;
}



@media(max-width:1200px)
{
    .cards{
        grid-template-columns:repeat(2,1fr);
    }
}

@media(max-width:768px)
{
    .sidebar{
        width:100%;
        height:auto;
        position:relative;
    }

    .main{
        margin-left:0;
    }

    .cards{
        grid-template-columns:1fr;
    }

    .topbar{
        flex-direction:column;
        gap:15px;
    }

    .search-box{
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
            <a href="new_dashboard.php" >
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

            <a class="active" href="reports.php">
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


--- main---
<div class="main">

    <div class="topbar">

        <h2> Reports Dashboard </h2>


        <div class="admin-profile">

            <img src="uploads/<?php echo $admin_image; ?>" onerror="this.src='uploads/default.png'" >

            <div>
                <h4>  <?php echo $admin_name; ?> </h4>
                <p> <?php echo $role; ?> </p>
            </div>

            <div>
                <a href="staff_login.php">
                    <button class="dark-btn logout"> Log out </button>
                </a>
            </div>

        </div>

        

    </div>

    <br>
    
    <div class="cards">

        <div class="card">
            <h4>Total Sales</h4>
            <h2>$<?php echo number_format($totalSales,2); ?></h2>
        </div>

        <div class="card">
            <h4>Total Orders</h4>
            <h2><?php echo $totalOrders; ?></h2>
        </div>

        <div class="card">
            <h4>Total Customers</h4>
            <h2><?php echo $totalCustomers; ?></h2>
        </div>

        <div class="card">
            <h4>Total Products</h4>
            <h2><?php echo $totalProducts; ?></h2>
        </div>

    </div>


    <div class="header-box">
        <div class="search-box">
            <input type="text" id="searchInput" class="search-box" placeholder="Search Orders...">
            <i class="fa fa-search"></i>
        </div>
        <form method="POST"><button type="submit" name="export_csv" class="export-btn"> Export CSV</button></form>
    </div>

    <div class="table-title">
            Recent Orders
        </div>
    <div class="table-box">

        

        <table id="ordersTable">

            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Payment Method</th>
                    <th>Total item</th>
                    <th>Discount Percent</th>
                    <th>Sub Total</th>
                    <th>Discount Price</th>
                    
                    <th>Grand Total</th>
                </tr>
            </thead>

            <tbody>

            <?php

            $orders = mysqli_query($conn,"
                SELECT *
                FROM test_orders
                ORDER BY id DESC
                LIMIT 20
            ");

            while($row = mysqli_fetch_assoc($orders))
            {
            ?>

                <tr>


                    <td> <?php echo $row['fullname'] ?? 'Customer'; ?> </td>

                    <td> <?php echo $row['email'] ?? 'Customer'; ?> </td>

                    <td> <?php echo $row['phone'] ?? 'Customer'; ?> </td>

                    <td> <?php echo $row['payment_method'] ?? 'Customer'; ?> </td>

                    <td> <?php echo $row['total_item'] ?? 'Customer'; ?> </td>

                    <td> <?php echo $row['discount_percent'] ?? 'Customer'; ?> % </td>

                    <td> $ <?php echo number_format( $row['subtotal'] ?? 0, 2 ); ?></td>

                    <td> $<?php echo $row['discount_price'] ?? 'Customer'; ?> </td>

                    

                    <td> $ <?php echo number_format( $row['grand_total'] ?? 0, 2 ); ?> </td>

                   

                </tr>

            <?php
            }
            ?>

            </tbody>

        </table>

    </div>

    <h2 style="margin-bottom:20px;">
        Top Selling Products
    </h2>

    <div class="products-grid">

    <?php

    $topProducts = mysqli_query($conn,"
        SELECT
        product_name,
        SUM(product_qty) sold
        FROM test_order_items
        GROUP BY product_name
        ORDER BY sold DESC
        LIMIT 6
    ");

    while($product = mysqli_fetch_assoc($topProducts))
    {
    ?>

        <div class="product-card">

            <h3>
                <?php echo $product['product_name']; ?>
            </h3>

            <p>
                Sold:
                <b>
                    <?php echo $product['sold']; ?>
                </b>
            </p>

        </div>

    <?php
    }
    ?>

    </div>

</div>

<script>

function toggleDarkMode()
{
    document.body.classList.toggle('dark');
}

document
.getElementById("searchInput")
.addEventListener("keyup",function(){

    let value =
    this.value.toLowerCase();

    let rows =
    document.querySelectorAll(
        "#ordersTable tbody tr"
    );

    rows.forEach(row=>{

        row.style.display =
        row.innerText
        .toLowerCase()
        .includes(value)
        ?
        ""
        :
        "none";

    });

});

</script>

</body>
</html>