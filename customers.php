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
   ADD CUSTOMER
========================== */

if(isset($_POST['add_customer'])){

    $name = mysqli_real_escape_string(
        $conn,
        $_POST['name']
    );

    $email = mysqli_real_escape_string(
        $conn,
        $_POST['email']
    );

    $phone = mysqli_real_escape_string(
        $conn,
        $_POST['phone']
    );

    $address = mysqli_real_escape_string(
        $conn,
        $_POST['address']
    );

    mysqli_query($conn,"
        INSERT INTO test_orders
        (
            fullname,
            email,
            phone,
            address,
            created_at
        )
        VALUES
        (
            '$fullname',
            '$email',
            '$phone',
            '$address',
            NOW()
        )
    ");

    header("Location: customers.php");
    exit;
}

/* ==========================
   DELETE CUSTOMER
========================== */

if(isset($_GET['delete'])){

    $id = (int)$_GET['delete'];

    mysqli_query($conn,"
        DELETE FROM test_orders
        WHERE id='$id'
    ");

    header("Location: customers.php");
    exit;
}

/* ==========================
   SEARCH
========================== */

$search = $_GET['search'] ?? '';

$where = '';

if($search != ''){

    $search = mysqli_real_escape_string(
        $conn,
        $search
    );

    $where = "
    WHERE
        fullname LIKE '%$search%'
        OR email LIKE '%$search%'
        OR phone LIKE '%$search%'
    ";
}

/* ==========================
   STATS
========================== */

$totalCustomers = mysqli_fetch_assoc(
mysqli_query($conn,"
SELECT COUNT(*) total
FROM test_orders
"))['total'];

$newCustomers = mysqli_fetch_assoc(
mysqli_query($conn,"
SELECT COUNT(*) total
FROM test_orders
WHERE DATE(created_at)=CURDATE()
"))['total'];

/* ==========================
   PAGINATION
========================== */

$limit = 10;

$page = isset($_GET['page'])
? (int)$_GET['page']
: 1;

if($page < 1){
    $page = 1;
}

$offset = ($page - 1) * $limit;

$totalRows = mysqli_fetch_assoc(
mysqli_query($conn,"
SELECT COUNT(*) total
FROM test_orders
$where
"))['total'];

$totalPages = ceil(
    $totalRows / $limit
);

$customers = mysqli_query($conn,"
SELECT *
FROM test_orders
$where
ORDER BY id DESC
LIMIT $offset,$limit
");

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="utf-8">
<meta name="viewport"
content="width=device-width,initial-scale=1">

<title>Customers Management</title>

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
    padding:12px 16px;
    border-radius:10px;
        font-weight: bold;
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
    gap:15px;
    flex-wrap:wrap;
}

.search-box{
    position:relative;
    width: 970px;
    max-width:100%;
    top: 12px;
}

.search-box input{
    width:100%;
    padding:14px 14px 14px 45px;
    border:none;
    outline:none;
    background:var(--card);
    border-radius:12px;
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

.dark-btn,
.add-btn{
border:none;
cursor:pointer;
padding:12px 18px;
border-radius:10px;
color:white;
}



/* CARDS */

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
border-radius:16px;
}

/* TABLE */

.table-card{
    margin-top:25px;
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
}

th,td{
    padding:14px;
    text-align:left;
    font-size: 0.85rem;
}

tr{
    border-bottom:1px solid var(--border);
}

/* ACTIONS */

a{
    text-decoration: none;
}

.action-btn{
    padding:8px 12px;
    border:none;
    border-radius:8px;
    color:white;
    cursor:pointer;
    text-decoration:none;
}

.edit-btn{
    background: #4864df;
    padding: 8px 20px;
}

.delete-btn{
    background: #ef4444;
}

.views-btn{
background:#0000ff;
}

/* PAGINATION */

.pagination{
    margin-top:20px;
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}

.pagination a{
    padding:10px 15px;
    background:var(--card);
    border-radius:10px;
    text-decoration:none;
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

<h2>Customers Management</h2>


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
    <h3>Total Customers</h3>
    <h2><?=number_format($totalCustomers)?></h2>
</div>

<div class="card">
    <h3>New Today</h3>
    <h2><?=number_format($newCustomers)?></h2>
</div>

</div>


<form method="GET">

<div class="search-box">

<i class="fa fa-search"></i>

<input
type="text"
name="search"
value="<?=htmlspecialchars($search)?>"
placeholder="Search customers...">

</div>

</form>

<div class="table-card">

<table>

    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Created</th>
        <th>Action</th>
    </tr>

    <?php while($row=mysqli_fetch_assoc($customers)): ?>

        <tr>

            <td>#<?=$row['id']?></td>
            <td><?=htmlspecialchars($row['fullname'])?></td>
            <td><?=htmlspecialchars($row['email'])?></td>
            <td><?=htmlspecialchars($row['phone'])?></td>
            <td><?=htmlspecialchars($row['address'])?></td>

        <td>
    <?=date(
        'd M Y',
        strtotime($row['created_at'])
    )?>
</td>

<td>

    <a onclick="return confirm('Delete customer?')" href="?delete=<?=$row['id']?>">
         <button class="action-btn delete-btn"><i class="fa-solid fa-trash"></i></button>
    </a>
    <a href="view_customers.php?id=<?= $row['id'] ?>" class="action-btn ">
         <button class="action-btn edit-btn">View</button>
    </a>

</td>

</tr>

<?php endwhile; ?>

</table>

</div>

<div class="pagination">

<?php for($i=1;$i<=$totalPages;$i++): ?>

<a
class="<?=($i==$page)?'active':''?>"
href="?page=<?=$i?>">

<?=$i?>

</a>

<?php endfor; ?>

</div>

</div>



<script>

function openModal(){
document.getElementById(
'customerModal'
).style.display='flex';
}

function closeModal(){
document.getElementById(
'customerModal'
).style.display='none';
}

window.onclick=function(e){

let modal=document.getElementById(
'customerModal'
);

if(e.target==modal){
modal.style.display='none';
}

}

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