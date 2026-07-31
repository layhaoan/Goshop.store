<?php
session_start();
$conn = mysqli_connect("localhost","root","","store_db");

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


/* DELETE STAFF */
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];

    mysqli_query(
        $conn,
        "DELETE FROM staff WHERE id='$id'"
    );

    header("Location: staff_list.php");
    exit;
}

/* UPDATE STAFF */
if(isset($_POST['update_staff'])){

    $id = (int)$_POST['staff_id'];

    $full_name = mysqli_real_escape_string(
        $conn,
        $_POST['fullname']
    );

    $email = mysqli_real_escape_string(
        $conn,
        $_POST['email']
    );

    $phone = mysqli_real_escape_string(
        $conn,
        $_POST['phone']
    );

    $role = mysqli_real_escape_string(
        $conn,
        $_POST['role']
    );

    $imageSQL = '';

    if(!empty($_FILES['image']['name'])){

        $folder = 'uploads/staff/';

        if(!is_dir($folder)){
            mkdir($folder,0777,true);
        }

        $image =
        $folder .
        time() .
        '_' .
        basename(
            $_FILES['image']['name']
        );

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            $image
        );

        $imageSQL =
        ", image='$image'";
    }

    mysqli_query(
        $conn,
        "
        UPDATE staff
        SET
        fullname='$fullname',
        email='$email',
        phone='$phone',
        role='$role',
        image='$image'
        WHERE id='$id'
        "
    );

    header("Location: staff_list.php");
    exit;
}

/* SEARCH */

$search = $_GET['search'] ?? '';

$where = '';

if($search != ''){

    $search =
    mysqli_real_escape_string(
        $conn,
        $search
    );

    $where =
    "
    WHERE
    fullname LIKE '%$search%'
    OR email LIKE '%$search%'
    ";
}

$staffQuery = mysqli_query(
    $conn,
    "
    SELECT *
    FROM staff
    $where
    ORDER BY id DESC
    "
);
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="utf-8">
<meta name="viewport"
content="width=device-width,initial-scale=1">

<title>Staff List</title>

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

/* button */

a{
    text-decoration: none;
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
}

.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    flex-wrap:wrap;
    margin-bottom:20px;
}

.search-box{
    position:relative;
    width:350px;
    max-width:100%;
}

.search-box input{
    width:100%;
    padding:12px 15px 12px 45px;
    border:none;
    border-radius:30px;
    background:white;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
    outline:none;
}

.search-box i{
    position:absolute;
    left:15px;
    top:13px;
    color:#666;
}

/* TABLE */

.table-box{
    background:white;
    border-radius:18px;
    overflow:auto;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
     margin-top:20px;
}

.staff-img{
    width:55px;
    height:55px;
    border-radius:50%;
    object-fit:cover;
    border:3px solid #2563eb;
}

form{
    display: flex;
    width: 100%;
    position: relative;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top: 0;
}

th{
    background:#2563eb;
    color:white;
}

th,td{
    padding:15px;
    text-align:left;
}

tr{
    border-bottom:1px solid #eee;
    font-size: 0.9rem;
}

tr:hover{
background:#f8fafc;
}

.role{
    padding:6px 12px;
    border-radius:20px;
    background:#dbeafe;
    color:#1d4ed8;
    font-size:12px;
}

.edit-btn{
    background:#10b981;
    color:white;
    border:none;
    padding: 6px 14px;
    border-radius:8px;
    cursor:pointer;
}

.delete-btn{
    background:#ef4444;
    color:white;
    padding: 6px 14px;
    border-radius:8px;
    text-decoration:none;
}

/* MODAL */

.modal{
position:fixed;
inset:0;
background:rgba(0,0,0,.5);
display:none;
justify-content:center;
align-items:center;
z-index:9999;
}

.modal-content{
width:500px;
max-width:95%;
background:white;
padding:25px;
border-radius:20px;
}

.form-group{
margin-bottom:15px;
}

.form-group label{
display:block;
margin-bottom:6px;
font-weight:600;
}

.form-group input,
.form-group select{
width:100%;
padding:12px;
border:1px solid #ddd;
border-radius:10px;
}

.save-btn{
width:100%;
padding:12px;
background:#2563eb;
color:white;
border:none;
border-radius:10px;
cursor:pointer;
}

/* IMAGE */

.image-upload{
    text-align:center;
    margin-bottom:25px;
}

.image-upload img{
    width:120px;
    height:120px;
    border-radius:50%;
    object-fit:cover;
    border:4px solid white;
    cursor:pointer;
    transition:.3s;
}

.image-upload img:hover{
    transform:scale(1.05);
}

.image-upload input{
    display:none;
}

.image-upload label{
    display:block;
    color:white;
    margin-top:10px;
    cursor:pointer;
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

            <a class="active" href="staff_list.php">
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

            <?php if($role == "Admin" || $role == "Manager"){ ?>
                <a href="add_staff.php">
                    <button class="dark-btn"> Add Staff </button>
                </a>
            <?php } ?>

            <a href="staff_login.php">
                <button class="dark-btn "> Log out </button>
            </a>
            

        </div>

    </div>

    
</div>

<div class="topbar">

    

</div>

<form>

    <div class="search-box">

        <i class="fa-solid fa-search"></i>

        <input
        type="text"
        name="search"
        placeholder="Search Staff..."
        value="<?= htmlspecialchars($search) ?>">

    </div>

</form>

<div class="table-box">

<table>

    <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Role</th>
         <?php if($role == "Admin" || $role == "Manager"){ ?>
        <th>Action</th>
        <?php } ?>
    </tr>

    <?php while($row=mysqli_fetch_assoc($staffQuery)): ?>

        <tr>

            <td><?= $row['id'] ?></td>

                    <td><img src="uploads/<?= !empty($row['image']) ? $row['image'] : 'uploads/default-user.png' ?>"
                    class="staff-img"></td>

                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td>
                        <span class="role"> <?= htmlspecialchars($row['role']) ?> </span>
                    </td>
                <?php if($role == "Admin" || $role == "Manager"){ ?>
                    <td>
                        <a href="edit_staff.php?id=<?=$row['id']; ?>"><button class="edit-btn" > Edit</button></a>
                        <a class="delete-btn" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this staff?')"> Delete</a>
                    </td>
                <?php } ?>

        </tr>

    <?php endwhile; ?>

</table>

</div>

</div>


<script>


document
.getElementById(
    "profileImage"
)
.addEventListener(
    "change",
    function(e){

        const file =
        e.target.files[0];

        if(file){

            document
            .getElementById(
                "preview"
            )
            .src =
            URL.createObjectURL(file);

        }

    }
);

</script>

</body>
</html>