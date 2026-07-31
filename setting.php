<?php
session_start();
$conn = new mysqli("localhost","root","","store_db");

if($conn->connect_error){
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


$admin_id = 1;
$page = $_GET['page'] ?? 'account';

/* ACCOUNT UPDATE */
if(isset($_POST['save_account'])){

    $fullname = mysqli_real_escape_string($conn,$_POST['fullname']);
    $email     = mysqli_real_escape_string($conn,$_POST['email']);
    $phone     = mysqli_real_escape_string($conn,$_POST['phone']);
    $address   = mysqli_real_escape_string($conn,$_POST['address']);

    $profile_sql = "";
    $cover_sql   = "";

    if(!is_dir("uploads/staff")){
        mkdir("uploads/staff",0777,true);
    }

    if(!empty($_FILES['image']['name'])){

        $profile =
        "uploads/staff/".
        time().
        "_".
        basename($_FILES['image']['name']);

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            $profile
        );

        $profile_sql =
        ", image='$profile'";
    }

    if(!empty($_FILES['cover_image']['name'])){

        $cover =
        "uploads/staff/".
        time().
        "_cover_".
        basename($_FILES['cover_image']['name']);

        move_uploaded_file(
            $_FILES['cover_image']['tmp_name'],
            $cover
        );

        $cover_sql =
        ", cover_image='$cover'";
    }

    mysqli_query($conn,"
        UPDATE staff
        SET
        fullname='$fullname',
        email='$email',
        phone='$phone',
        address='$address'
        $profile_sql
        $cover_sql
        WHERE id='$admin_id'
    ");

    header("Location: new_setting.php");
    exit;
}

/* RESET PASSWORD */

if(isset($_POST['reset_password'])){

    $staff_id = (int)$_POST['staff_id'];

    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if($password != $confirm_password){

        echo "Passwords do not match";
        exit;
    }

    $hash = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    mysqli_query(
        $conn,
        "UPDATE staff
         SET password='$hash',
             confirm_password='$hash'
         WHERE id='$staff_id'"
    );

    echo "Password reset successful";
}


/* ADMIN */
$admin =
mysqli_fetch_assoc(
mysqli_query(
$conn,
"SELECT * FROM staff WHERE id='$admin_id'"
));

/* STAFF */
$staff =
mysqli_query(
$conn,
"SELECT * FROM staff ORDER BY id DESC"
);

$totalStaff =
mysqli_fetch_assoc(
mysqli_query(
$conn,
"SELECT COUNT(*) total FROM staff"
))['total'];


/* =========================================================
MANAGE BANNER ADD,DELETE AND EDIT OR UPDATE 
========================================================= */
if(!is_dir("uploads/banners")){
    mkdir("uploads/banners",0777,true);
}

/* SET ACTIVE */
if(isset($_GET['active'])){
    $id = (int)$_GET['active'];

    $conn->query("UPDATE banners SET is_active=0");
    $conn->query("UPDATE banners SET is_active=1 WHERE id='$id'");

    header("Location: setting.php?page=banner_manager");
    exit;
}

/* DELETE */
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];

    $conn->query("DELETE FROM banners WHERE id='$id'");

    header("Location: setting.php?page=banner_manager");
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

    header("Location: setting.php?page=banner_manager");
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

    header("Location: setting.php?page=banner_manager");

    $editData = $result->fetch_assoc();
}

$banners =
$conn->query(
    "SELECT * FROM banners ORDER BY id DESC"
);



/* =========================================================
SET PERMISSION
========================================================= */
$current_role = $_SESSION['role'] ?? '';

if(isset($_POST['update_permission'])){

    if($current_role != "Admin" && $current_role != "Manager"){

        die("Access Denied");

    }

    $staff_id = (int)$_POST['staff_id'];
    $new_role = mysqli_real_escape_string($conn,$_POST['role']);

    mysqli_query($conn,"
        UPDATE staff
        SET role='$new_role'
        WHERE id='$staff_id'
    ");

    $success = "Permission updated successfully.";
    header("Location: setting.php?page=permission");

}


$setting=mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT login_image
         FROM login_settings
         WHERE id=1"
    )
);

$login_image=$setting['login_image'] ?? "";

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width,initial-scale=1">

<title>GoShop Settings</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
    padding:12px 16px;
    border-radius:10px;

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

.logout{
   background: #444;
   color: #fff;
}

.card{
    background:#fff;
    padding:25px;
    border-radius:16px;
    margin-top:20px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
    position: relative;
}

.settings-nav{
    display:flex;
    gap:10px;
    margin-top:20px;
    flex-wrap:wrap;
}

.settings-nav a{
    padding:12px 18px;
    background:#eef2ff;
    border-radius:10px;
    text-decoration:none;
    color:#111;
    font-weight:600;
}

.settings-nav a.active{
    background:#2563eb;
    color:#fff;
}

.cover{
    height:220px;
    border-radius:15px;
    overflow:hidden;
    position:relative;
}

.cover img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.profile-box{
    display:flex;
    align-items:center;
    gap:20px;
    margin-top:-60px;
    padding-left:25px;
    position:relative;
}

.avatar{
    width:120px;
    height:120px;
    border-radius:50%;
    border:5px solid #fff;
    object-fit:cover;
    background:#fff;
}

.grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
}

input,
textarea,
select{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    border-radius:10px;
    margin-top:5px;
}

textarea{
    height:120px;
    resize:none;
}

.btn{
    background:#2563eb;
    color:#fff;
    border:none;
    padding:12px 18px;
    border-radius:10px;
    cursor:pointer;
}

.btn:hover{
    background:#1d4ed8;
}

.message{
    padding:12px;
    background:#dcfce7;
    border-radius:10px;
    margin-bottom:15px;
}

table{
    width:100%;
    border-collapse:collapse;
}

table th,
table td{
    padding:12px;
    border-bottom:1px solid #eee;
    text-align:left;
}

.staff-img{
    width:50px;
    height:50px;
    border-radius:50%;
    object-fit:cover;
}

.stat{
    font-size:30px;
    font-weight:700;
    color:#2563eb;
}

@media(max-width:768px){

.sidebar{
width:80px;
}

.main{
margin-left:80px;
}

.grid{
grid-template-columns:1fr;
}

.profile-box{
flex-direction:column;
align-items:flex-start;
}

}

/* =========================
   Banner Manager Sub Page
========================= */

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

.grid{
display:grid;
grid-template-columns:
repeat(auto-fit,minmax(320px,1fr));
gap:20px;
}

.banner-card{
background:white;
border-radius:15px;
overflow:hidden;
box-shadow:0 5px 15px rgba(0,0,0,.08);
position:relative;
}

.banner-card img{
width:100%;
height:220px;
object-fit:cover;
}

.banner-content{
padding:15px;
}

.badge{
position:absolute;
top:15px;
right:15px;
background:#22c55e;
color:white;
padding:8px 12px;
border-radius:20px;
font-size:12px;
font-weight:bold;
}

.actions{
display:flex;
gap:10px;
margin-top:15px;
flex-wrap:wrap;
}

.active-btn{
background:#f59e0b;
color:white;
padding:10px 12px;
border-radius:6px;
text-decoration:none;
}

.edit-btn{
background:#10b981;
color:white;
padding:10px 12px;
border-radius:6px;
text-decoration:none;
}

.delete-btn{
background:#ef4444;
color:white;
padding:10px 12px;
border-radius:6px;
text-decoration:none;
}

.name{
    position: relative;
    margin-top: 50px;
}

/* button */
.dark-btn{
    border:none;
    cursor:pointer;
    background:var(--primary);
    color:white;
    padding:12px 16px;
    border-radius:10px;
    position: absolute;
    top: 30px;
    right: 30px;
}


.success{

background:#dcfce7;
color:#166534;
padding:12px;
margin-bottom:20px;
border-radius:8px;

}

input[readonly]{

background:#f3f4f6;

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

            <a class="active" href="setting.php">
                <i class="fa-solid fa-gear"></i>
                <span class="text">Settings</span>
            </a>
        <?php } ?>

    </div>
</div>

<div class="main">

    <div class="topbar">

        <h2> Setting </h2>


        <div class="admin-profile">
            
            <div>
                <a href="staff_login.php">
                    <button class="dark-btn logout"> Log out </button>
                </a>
            </div>

        </div>

        

    </div>

    <div class="settings-nav">

        <a href="?page=account"
        class="<?= $page=='account'?'active':'' ?>">
        Account Settings
        </a>

        <a href="?page=password"
        class="<?= $page=='password'?'active':'' ?>">
        Reset Password
        </a>

        <?php if($role == "Admin"){ ?>
            <a href="?page=banner_manager"
            class="<?= $page=='banner_manager'?'active':'' ?>">
            Banner Manager
            </a>
        <?php } ?>

        <?php if($role == "Admin"){ ?>
            <a href="?page=permission"
                class="<?= $page=='permission'?'active':'' ?>">
                Reset Permission
            </a>
        <?php } ?>

    </div>

<?php if(isset($message)): ?>
<div class="message">
<?= $message ?>
</div>
<?php endif; ?>

<!-- ACCOUNT -->

<?php if($page=='account'): ?>

<div class="card">

<div class="cover">

<img id="coverPreview"
src="<?= !empty($admin['cover_image'])
? $admin['cover_image']
: 'https://picsum.photos/1200/300'; ?>">

</div>

<div class="profile-box">

<img id="profilePreview" class="avatar" src="uploads/<?php echo $admin_image; ?>" onerror="this.src='uploads/default.png'" >
<div class="name">
    <h2>  <?php echo $admin_name; ?> </h2>
    <p> <?php echo $role; ?> </p>
</div>

</div>

<br>

    <form method="POST" enctype="multipart/form-data">

    <div class="grid">

        <div>
            <label>Full Name</label>
            <input type="text" name="fullname" value="<?= $admin['fullname'] ?? ''; ?>" readonly>
        </div>

        <div>
            <label>Email</label>
            <input type="email" name="email" value="<?= $admin['email'] ?? ''; ?>" readonly>
        </div>

        <div>
            <label>Phone</label>
            <input type="text" name="phone" value="<?= $admin['phone'] ?? ''; ?>" readonly>
        </div>

        <div>
            <label>Image</label>
            <input type="file" name="image" id="profileImage">
        </div>

    </div>

<br>

<label>Address</label>

<textarea name="address"><?= $admin['address'] ?? ''; ?></textarea>

<br><br>

<button type="submit" name="save_account" class="btn"> Save Changes </button>

</form>

</div>

<?php endif; ?>

<!-- PASSWORD -->

<?php if($page=='password'): ?>

<div class="card">

<h2>Reset Staff Password</h2>

<form method="POST">

<label>Select Staff</label>

<select name="staff_id" id="staffSelect" required>

<?php $list = mysqli_query( $conn, "SELECT id, fullname, email FROM staff ORDER BY fullname ASC");
while($s = mysqli_fetch_assoc($list)): ?>

<option value="<?= $s['id']; ?>" data-email="<?= htmlspecialchars($s['email']); ?>"> <?= htmlspecialchars($s['fullname']); ?> </option>

<?php endwhile; ?>

</select>

<br><br>

<label>Email Address</label>

<input
type="email"
id="staffEmail"
readonly>

<br><br>

<label>New Password</label>

<input
type="text"
name="password"
placeholder="New Password"
required>

<br><br>

<label>Confirm Password</label>

<input
type="password"
name="confirm_password"
placeholder="Confirm_password"
required>

<br><br>

<button
type="submit"
name="reset_password"
class="btn">
Reset Password
</button>


</form>

</div>

<?php endif; ?>

<!-- banner_manager -->

<?php if($page == 'banner_manager'): ?>


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
src="<?= $editData && !empty($editData['banner_image'])
? $editData['banner_image']
: 'https://via.placeholder.com/800x250?text=Banner+Preview'; ?>">

<input
type="file"
name="banner_image"
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

</form>

</div>

<div class="grid">

<?php while($row = $banners->fetch_assoc()): ?>

<div class="banner-card">

<?php if(isset($row['is_active']) && $row['is_active']==1): ?>
<div class="badge">
ACTIVE
</div>
<?php endif; ?>

<img src="<?= $row['image']; ?>">

<div class="banner-content">

<h3><?= htmlspecialchars($row['title']); ?></h3>

<p><?= htmlspecialchars($row['subtitle']); ?></p>

<div class="actions">

<a
href="?active=<?= $row['id']; ?>"
class="active-btn">

<?= isset($row['is_active']) && $row['is_active']==1
? 'ACTIVE'
: 'SET ACTIVE'; ?>

</a>

<a
href="edit_banner.php?edit=<?= $row['id']; ?>"
class="edit-btn">
EDIT
</a>

<a
href="?delete=<?= $row['id']; ?>"
class="delete-btn"
onclick="return confirm('Delete this banner?')">
DELETE
</a>

</div>

</div>

</div>

<?php endwhile; ?>

</div>


<?php endif; ?>

<?php if($page=="permission"): ?>

<div class="card">

<h2>Reset Staff Permission</h2>

<?php
if(isset($success)){
    echo "<div class='success'>$success</div>";
}
?>

<form method="POST">

<label>Select Staff</label>

<select name="staff_id" id="staffPermission">

<?php

$list=mysqli_query(
$conn,
"SELECT id,fullname,email,role
FROM staff
ORDER BY fullname ASC");

while($row=mysqli_fetch_assoc($list)){

?>

<option
value="<?= $row['id'];?>"
data-role="<?= $row['role'];?>"
data-email="<?= $row['email'];?>">

<?= $row['fullname'];?>

</option>

<?php } ?>

</select>

<br><br>

<label>Email</label>

<input
type="text"
id="permissionEmail"
readonly>

<br><br>

<label>Current Role</label>

<input
type="text"
id="currentRole"
readonly>

<br><br>

<label>New Permission</label>

<select name="role">

<option value="Admin">Admin</option>

<option value="Manager">Manager</option>

<option value="Staff">Staff</option>

</select>

<br><br>

<button
class="btn"
name="update_permission">

Update Permission

</button>

</form>

</div>

<?php endif; ?>

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

<script>

document
.getElementById("profileImage")
?.addEventListener(
"change",
function(e){

if(e.target.files[0]){

document
.getElementById("profilePreview")
.src =
URL.createObjectURL(
e.target.files[0]
);

}

});

document
.getElementById("coverImage")
?.addEventListener(
"change",
function(e){

if(e.target.files[0]){

document
.getElementById("coverPreview")
.src =
URL.createObjectURL(
e.target.files[0]
);

}

});

</script>

<script>

const staffSelect =
document.getElementById('staffSelect');

const staffEmail =
document.getElementById('staffEmail');

function loadStaffEmail(){

    let selected =
    staffSelect.options[
        staffSelect.selectedIndex
    ];

    staffEmail.value =
    selected.dataset.email;
}

loadStaffEmail();

staffSelect.addEventListener(
    'change',
    loadStaffEmail
);

</script>

<script>

const permission =
document.getElementById("staffPermission");

if(permission){

function loadPermission(){

let selected=
permission.options[
permission.selectedIndex];

document.getElementById("permissionEmail").value=
selected.dataset.email;

document.getElementById("currentRole").value=
selected.dataset.role;

}

loadPermission();

permission.addEventListener(
"change",
loadPermission);

}

</script>

<script>

const input=document.getElementById("loginImage");
const preview=document.getElementById("previewImage");

input.addEventListener("change",function(){

    if(this.files.length>0){

        preview.src=URL.createObjectURL(this.files[0]);

    }

});

</script>

</body>
</html>