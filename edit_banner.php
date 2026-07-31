<?php
session_start();
$conn = new mysqli("localhost","root","","store_db");

if($conn->connect_error){
    die("Connection failed");
}

if(!is_dir("uploads/banners")){
    mkdir("uploads/banners",0777,true);
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

/* SET ACTIVE */
if(isset($_GET['active'])){
    $id = (int)$_GET['active'];

    $conn->query("UPDATE banners SET is_active=0");
    $conn->query("UPDATE banners SET is_active=1 WHERE id='$id'");

    header("Location: banner_manager.php");
    exit;
}

/* DELETE */
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];

    $conn->query("DELETE FROM banners WHERE id='$id'");

    header("Location: banner_manager.php");
    exit;
}

/* ADD BANNER */
if(isset($_POST['add_banner'])){

    $title = $conn->real_escape_string($_POST['title']);
    $subtitle = $conn->real_escape_string($_POST['subtitle']);

    $image = "";

    if(!empty($_FILES['image']['name'])){

        $ext = pathinfo(
            $_FILES['image']['name'],
            PATHINFO_EXTENSION
        );

        $image =
        "uploads/banners/" .
        time() .
        "_" .
        uniqid() .
        "." .
        $ext;

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            $image
        );
    }

    $conn->query("
        INSERT INTO banners(
            title,
            subtitle,
            image
        )
        VALUES(
            '$title',
            '$subtitle',
            '$image'
        )
    ");

    header("Location: banner_manager.php");
    exit;
}

/* UPDATE */
if(isset($_POST['update_banner'])){

    $id = (int)$_POST['banner_id'];

    $title = $conn->real_escape_string($_POST['title']);
    $subtitle = $conn->real_escape_string($_POST['subtitle']);

    $imageSQL = "";

    if(!empty($_FILES['image']['name'])){

        $ext = pathinfo(
            $_FILES['image']['name'],
            PATHINFO_EXTENSION
        );

        $image =
        "uploads/banners/" .
        time() .
        "_" .
        uniqid() .
        "." .
        $ext;

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            $image
        );

        $imageSQL = ", image='$image'";
    }

    $conn->query("
        UPDATE banners
        SET
        title='$title',
        subtitle='$subtitle'
        $imageSQL
        WHERE id='$id'
    ");

    header("Location: setting.php?page=banner_manager");
    exit;
}

/* EDIT DATA */
$editData = null;

if(isset($_GET['edit'])){

    $id = (int)$_GET['edit'];

    $result =
    $conn->query(
        "SELECT * FROM banners WHERE id='$id'"
    );

    $editData = $result->fetch_assoc();
}

$banners =
$conn->query(
    "SELECT * FROM banners ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Banner Manager</title>

<style>

:root{
--primary:#2563eb;
--success:#10b981;
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
background:#f4f6f9;
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

.main{
margin-left:250px;
padding:25px;
}

.card{
background:white;
padding:20px;
border-radius:15px;
box-shadow:0 5px 15px rgba(0,0,0,.08);
margin-bottom:20px;
}

.form-group{
margin-bottom:15px;
}

.form-group label{
display:block;
margin-bottom:5px;
font-weight:600;
}

input,
textarea{
width:100%;
padding:12px;
border:1px solid #ddd;
border-radius:8px;
}

textarea{
height:120px;
resize:none;
}

.preview{
width:100%;
height:250px;
object-fit:cover;
border-radius:10px;
border:2px dashed #ddd;
margin-bottom:10px;
}

.btn{
padding:12px 18px;
border:none;
border-radius:8px;
cursor:pointer;
background:#2563eb;
color:white;
}

.edit-btn{
background:#10b981;
color:white;
padding:10px 12px;
border-radius:6px;
text-decoration:none;
}


.back{
background: #474444;
color:white;
padding:10px 25px;
border-radius:6px;
text-decoration:none;
}

@media(max-width:768px){

.sidebar{
width:80px;
}

.logo{
font-size:16px;
padding:20px 10px;
}

.sidebar a{
text-align:center;
padding:15px 5px;
font-size:12px;
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

<div class="card">

<h2>
<?= $editData ? 'Edit Banner' : 'Add Banner'; ?>
</h2>

<form method="POST" enctype="multipart/form-data">

<?php if($editData): ?>
<input
type="hidden"
name="banner_id"
value="<?= $editData['id']; ?>">
<?php endif; ?>

<img
id="previewImage"
class="preview"
src="<?= $editData && !empty($editData['image'])
? $editData['image']
: 'https://via.placeholder.com/800x250?text=Banner+Preview'; ?>">

<input
type="file"
name="image"
id="banner_image"
hidden
accept="image/*">

<button
type="button"
class="btn"
onclick="document.getElementById('banner_image').click();">
Choose Banner Image
</button>

<br><br>

<div class="form-group">
<label>Title</label>
<input
type="text"
name="title"
required
value="<?= $editData['title'] ?? ''; ?>">
</div>

<div class="form-group">
<label>Subtitle</label>
<textarea
name="subtitle"><?= $editData['subtitle'] ?? ''; ?></textarea>
</div>

<button
type="submit"
name="<?= $editData ? 'update_banner' : 'add_banner'; ?>"
class="btn">

<?= $editData ? 'Update Banner' : 'Add Banner'; ?>

</button>

<a href="setting.php?page=banner_manager" class="btn back">Back</a>

</form>

</div>


</div>

</div>

<script>

document
.getElementById("banner_image")
.addEventListener(
"change",
function(e){

const file = e.target.files[0];

if(file){

const reader = new FileReader();

reader.onload = function(event){

document
.getElementById("previewImage")
.src = event.target.result;

};

reader.readAsDataURL(file);

}

});

</script>

</body>
</html>