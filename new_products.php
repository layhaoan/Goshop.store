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

/* ======================
   PRODUCT STATS
====================== */

$statsQuery = mysqli_query($conn, "
    SELECT
        COUNT(*) AS total_products,
        COALESCE(SUM(stock), 0) AS total_stock,
        COUNT(DISTINCT product_category) AS total_categories,
        COUNT(CASE WHEN stock < 4 THEN 1 END) AS low_stock
    FROM products
");

$stats = mysqli_fetch_assoc($statsQuery);

$totalProducts   = $stats['total_products'];
$totalStock      = $stats['total_stock'];
$totalCategories = $stats['total_categories'];
$lowStock        = $stats['low_stock'];

/* ======================
   PAGINATION
====================== */

$limit = 7;

$page = isset($_GET['page'])
? (int)$_GET['page']
: 1;

if($page < 1){
    $page = 1;
}

$offset = ($page - 1) * $limit;

$search = $_GET['search'] ?? '';

$where = '';

if($search != ''){

    $search = mysqli_real_escape_string(
        $conn,
        $search
    );

    $where = "
        WHERE name LIKE '%$search%'
    ";
}

$totalRows = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "
        SELECT COUNT(*) AS total
        FROM products
        $where
        "
    )
)['total'];

$totalPages = ceil(
    $totalRows / $limit
);

/* ======================
   PRODUCTS
====================== */

$products = mysqli_query($conn,"
SELECT *
FROM products
$where
ORDER BY id DESC
LIMIT $offset,$limit
");


if(isset($_POST['delete_by_stock']))
{
    $stockType = $_POST['stock_type'];

    switch($stockType)
    {
        case 'instock':

            mysqli_query($conn,"
                DELETE FROM products
                WHERE stock > 0
            ");

        break;

        case 'outstock':

            mysqli_query($conn,"
                DELETE FROM products
                WHERE stock <= 0
            ");

        break;

        case 'lowstock':

            mysqli_query($conn,"
                DELETE FROM products
                WHERE stock > 0
                AND stock < 5
            ");

        break;
    }

    header("Location:new_products.php");
    exit;
}
?>


<!DOCTYPE html>
<html>
<head>

<meta charset="utf-8">
<meta name="viewport"
content="width=device-width,initial-scale=1">

<title>Products Management</title>

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

.search-box{
    position:relative;
    width:350px;
    max-width:100%;
}

.search-box input{
    width:100%;
    padding:14px 14px 14px 45px;
    border:none;
    outline:none;
    border-radius:12px;
    background:var(--card);
    transition:.3s;
}

.search-box input:focus{
    transform:scale(1.03);
}

.search-box i{
    position:absolute;
    left:15px;
    top:15px;
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


.products{
    margin: 20px;
    position: relative;
    margin-right: 0;
    right: 20px;
}

/* CARDS */

.cards{
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

.card h2{
    margin-top:10px;
}

/* TABLE */

.table-card{
    margin-top: 0;
    background:var(--card);
    border-radius:16px;
    overflow:auto;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:var(--primary);
    color:white;
    font-size: 0.78rem;
}

th,td{
    padding:14px;
    text-align:left;
    font-size: 0.85rem;
}

tr{
    border-bottom:1px solid var(--border);
}

a{
    text-decoration: none;
}

.btn{
    width: 35px;
    height: 35px;
    background: none;
    border: none;
    border-radius: 10px; 
    cursor: pointer;
    margin: 5px;
    text-decoration: none;
}

.btn-edit{
    background: #4e55d0;
    color: #fff;
}

.btn-delete{
    background: #d82e5b;
    color: #fff;
}

.btn i{
    font-size: 1rem;
}


.product-img{
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:10px;
}

.badge{
    padding: 8px 22px;
    border-radius:20px;
    color:white;
    font-size: 0.85rem;
    font-weight: bold;
}

.instock{
    background: #10b981;
    border: 1px solid #10b981;
    color: #fff;
}

.lowstock{
    background:#f59e0b;
}

.outstock{
    background:#ef4444;
}

/* PAGINATION */

.pagination{
    margin-top:20px;
    display:flex;
    flex-wrap:wrap;
    gap:8px;
}

.pagination a{
    text-decoration:none;
    padding:10px 15px;
    border-radius:10px;
    background:var(--card);
    color:var(--text);
}

.pagination .active{
    background:var(--primary);
    color:white;
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
}

.main{
    margin-left:80px;
}

}

@media(max-width:600px){

.main{
    padding:15px;
}

.topbar{
    flex-direction:column;
    align-items:stretch;
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

            <a href="new_products.php" class="active">
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

    <h2>Products Management</h2>

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
        <p>Total Products</p>
        <h2><?php echo number_format($totalProducts); ?></h2>
    </div>
    
    <div class="card">
        <p>Categories</p>
        <h2><?php echo number_format($totalCategories); ?></h2>
    </div>

    <div class="card">
        <p>Total Stock</p>
        <h2><?php echo number_format($totalStock); ?></h2>
    </div>

    <div class="card">
        <p>Low Stock</p>
        <h2><?php echo number_format($lowStock); ?></h2>
    </div>

</div>

<a href="add_product.php">
    <button class="dark-btn products"> Add Products </button>
</a>
<!-- TABLE -->
<div class="table-card">

<table>

    <tr>
        <th>Code</th>
        <th>Image</th>
        <th>Product</th>
        <th>Price</th>
        
        <th>Discount</th>
        <th>Category</th>
        <th>Stock</th>
        
        <th>OPtion</th>
    </tr>

    <?php while($row=mysqli_fetch_assoc($products)): ?>

        <tr>

            <td>#<?=$row['code']?></td>
            <td> <img src="uploads/<?=$row['image']?>" class="product-img"> </td>
            <td> <?=htmlspecialchars( $row['name'] )?>  </td>
            <td> $<?=number_format($row['price'], 2 )?> </td>
            
            
            <td> <?=htmlspecialchars( $row['discount'] )?>% </td>
            <td> <?=htmlspecialchars( $row['product_category'] )?> </td>
            <td>
                <?php
                    $stock = (int)$row['stock'];

                    if($stock <= 0){
                        echo '<span class="badge outstock"> Out of Stock </span>';
                    }elseif($stock <= 2){
                        echo '<span class="badge critical"> Critical  </span>';
                    }elseif($stock <= 5){
                        echo '<span class="badge lowstock"> Low Stock </span>';
                    }else{
                        echo '<span class="badge instock"> In Stock </span>';

                    }
                ?>
            </td>
            
            <td>
                <a href="edit_product.php?id=<?php echo $row['id']; ?>">
                    <button class="btn btn-edit"><i class="fa-solid fa-pen-to-square"></i></button>
                </a>
                <a href="delete_product.php?id=<?php echo $row['id']; ?>" 
                onclick="return confirm('Are you sure you want to delete this product?');">
                    <button class="btn btn-delete"><i class="fa-solid fa-trash"></i></button>
                </a>
            </td>

        </tr>

    <?php endwhile; ?>

</table>

</div>

<!-- PAGINATION -->

<div class="pagination">

<?php
for($i=1;
$i<=$totalPages;
$i++):
?>

<a
class="<?=
($i==$page)
?'active'
:''
?>"

href="?page=<?=$i?>
&search=<?=
urlencode($search)
?>">

<?=$i?>

</a>

<?php endfor; ?>

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

<script>

const searchBox =
document.getElementById(
'searchBox'
);

const searchInput =
document.getElementById(
'searchInput'
);

searchInput.addEventListener(
'focus',
function(){

    searchBox.classList.add(
        'active'
    );

});

searchInput.addEventListener(
'blur',
function(){

    if(this.value === ''){

        searchBox.classList.remove(
            'active'
        );

    }

});

/* Auto-expand if search exists */

if(
searchInput.value.trim() !== ''
){
    searchBox.classList.add(
        'active'
    );
}

</script>

</body>
</html>