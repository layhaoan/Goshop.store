<?php
$conn = new mysqli("localhost","root","","store_db");

if($conn->connect_error){
    die("Connection Failed: ".$conn->connect_error);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$result = mysqli_query(
    $conn,
    "SELECT * FROM staff WHERE id='$id' LIMIT 1"
);

$staff = mysqli_fetch_assoc($result);

if(!$staff){
    die("Staff not found.");
}

if(isset($_POST['update_staff'])){

    $fullname = mysqli_real_escape_string($conn,$_POST['fullname']);
    $email     = mysqli_real_escape_string($conn,$_POST['email']);
    $phone     = mysqli_real_escape_string($conn,$_POST['phone']);
    $role      = mysqli_real_escape_string($conn,$_POST['role']);

    $image_sql = "";

    if(!is_dir("uploads/staff")){
        mkdir("uploads/staff",0777,true);
    }

    if(!empty($_FILES['image']['name'])){

        $filename = preg_replace(
            "/[^a-zA-Z0-9._-]/",
            "_",
            $_FILES['image']['name']
        );

        $image =
        "uploads/staff/" .
        time() .
        "_" .
        $filename;

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            $image
        );

        $image_sql =
        ", image='" .
        mysqli_real_escape_string(
            $conn,
            $image
        ) .
        "'";
    }

    $update = "
    UPDATE staff SET
    fullname='$fullname',
    email='$email',
    phone='$phone',
    role='$role'
    $image_sql
    WHERE id='$id'
    ";

    if(mysqli_query($conn,$update)){

        echo "<script>
        alert('Staff updated successfully');
        window.location='staff_list.php';
        </script>";
        exit;

    }else{

        echo mysqli_error($conn);

    }
}
?>

<!DOCTYPE html>

<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">

<title>Edit Staff</title>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Segoe UI,sans-serif;
}

body{
background:#f4f6fb;
padding:30px;
}

.container{
max-width:900px;
margin:auto;
background:#fff;
padding:30px;
border-radius:20px;
box-shadow:0 10px 30px rgba(0,0,0,.08);
}

h2{
margin-bottom:25px;
color:#111827;
}

.image-box{
text-align:center;
margin-bottom:20px;
}

.image-box img{
width:150px;
height:150px;
border-radius:50%;
object-fit:cover;
border:4px solid #2563eb;
}

.grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:15px;
}

input,
select{
width:100%;
padding:12px;
border:1px solid #ddd;
border-radius:10px;
}

label{
font-weight:600;
display:block;
margin-bottom:6px;
}

.btn{
background:#2563eb;
color:#fff;
border:none;
padding:14px;
border-radius:10px;
cursor:pointer;
margin-top:20px;
width:100%;
font-size:16px;
}

.btn:hover{
background:#1d4ed8;
}

.back{
display:inline-block;
margin-bottom:20px;
text-decoration:none;
color:#2563eb;
font-weight:600;
}

@media(max-width:768px){

.grid{
grid-template-columns:1fr;
}

}

</style>

</head>
<body>

<div class="container">

<a href="staff_list.php" class="back">
← Back to Staff List
</a>

<h2>Edit Staff</h2>

<form method="POST" enctype="multipart/form-data">

<div class="image-box">

<img
id="preview"
src="uploads/<?=
!empty($staff['image'])
? $staff['image']
: 'https://via.placeholder.com/150';
?>">

</div>

<div style="margin-bottom:20px;">

<label>Staff Image</label>

<input
type="file"
name="image"
id="imageInput">

</div>

<div class="grid">

<div>
<label>Full Name</label>
<input
type="text"
name="fullname"
value="<?= htmlspecialchars($staff['fullname']); ?>"
required>
</div>

<div>
<label>Email</label>
<input
type="email"
name="email"
value="<?= htmlspecialchars($staff['email']); ?>"
required>
</div>

<div>
<label>Phone</label>
<input
type="text"
name="phone"
value="<?= htmlspecialchars($staff['phone']); ?>">
</div>

<div>
<label>Role</label>

<select name="role">

<option value="Admin"
<?= $staff['role']=='Admin'?'selected':''; ?>>
Admin
</option>

<option value="Manager"
<?= $staff['role']=='Manager'?'selected':''; ?>>
Manager
</option>

<option value="Staff"
<?= $staff['role']=='Staff'?'selected':''; ?>>
Staff
</option>

</select>

</div>

</div>

<button
type="submit"
name="update_staff"
class="btn">
Update Staff </button>

</form>

</div>

<script>

document
.getElementById("imageInput")
.addEventListener("change",function(e){

if(e.target.files[0]){

document
.getElementById("preview")
.src =
URL.createObjectURL(
e.target.files[0]
);

}

});

</script>

</body>
</html>
