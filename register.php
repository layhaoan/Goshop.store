<!-- save as: register.php -->

<?php
include "db.php";

$message = "";

if(isset($_POST['register'])){

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    /* IMAGE */

    $imageName = "";

    if($_FILES['profile_image']['name']){

        if(!file_exists("uploads")){
            mkdir("uploads");
        }

        $imageName =
        time() . "_" .
        $_FILES['profile_image']['name'];

        move_uploaded_file(
            $_FILES['profile_image']['tmp_name'],
            "uploads/" . $imageName
        );
    }

    /* CHECK EMAIL */

    $check = $conn->query(
    "SELECT * FROM users WHERE email='$email'"
    );

    if($check->num_rows > 0){

        $message = "Email already exists!";

    }else{

        /* INSERT USER */

        $sql = "
        INSERT INTO users
        (fullname,email,password,profile_image)

        VALUES

        ('$fullname','$email','$password','$imageName')
        ";

        if($conn->query($sql)){

            header("Location: index.php");
            exit();

        }else{
            $message = "Register Failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>GoShop Register</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Segoe UI',sans-serif;
}

body{
min-height:100vh;
display:flex;
justify-content:center;
align-items:center;
background:#e8e8f5;
padding:20px;
}

.container{
width: 900px;
max-width:100%;
height: 540px;
background:white;
border-radius:20px;
overflow:hidden;
display:flex;
box-shadow:0 15px 40px rgba(0,0,0,.15);
animation:fadeIn .8s ease;
}

@keyframes fadeIn{
from{
opacity:0;
transform:translateY(30px);
}
to{
opacity:1;
transform:translateY(0);
}
}

.left{
flex:1;
padding:50px;
display:flex;
flex-direction:column;
justify-content:center;
}

.logo{
font-size: 1.2rem;
font-weight:700;
color:#7b2cff;
margin-bottom: 10px;
position: relative;
bottom: 30px;
}

h1{
font-size: 1.4rem;
position: relative;
bottom: 10px;
margin-bottom:10px;
color:#333;
}

.subtitle{
color:#777;
margin-bottom: 5px;
font-size: 0.89rem;
position: relative;
bottom: 20px;
}

.profile-upload{
display:flex;
justify-content:center;
margin-bottom:10px;
}

.avatar{
width:100px;
height:100px;
border-radius:50%;
border:4px solid #7b2cff;
overflow:hidden;
cursor:pointer;
position:relative;
transition:.3s;
}

.avatar:hover{
transform:scale(1.05);
}

.avatar img{
width:100%;
height:100%;
object-fit:cover;
}

.avatar::after{
content:"📷";
position:absolute;
bottom:5px;
right:5px;
background:#7b2cff;
width:30px;
height:30px;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
color:white;
font-size:14px;
}

input[type=file]{
display:none;
}

.form-group{
margin-bottom:15px;
position:relative;
}

.form-group i{
position:absolute;
left:15px;
top:10px;
color:#888;
}

.form-control{
width:100%;
padding: 8px 45px;
border:none;
outline:none;
background:#f3f3fc;
border-radius:30px;
font-size: 0.92rem;
}

.password-toggle{
position:absolute;
right:15px;
top: 15px;
cursor:pointer;
color:#666;
}

.btn{
width:100%;
padding: 8px;
border:none;
border-radius:30px;
background:linear-gradient(
135deg,
#8a2be2,
#5a00ff
);
color:white;
font-size: 0.92rem;
cursor:pointer;
font-weight:600;
transition:.3s;
}

.btn:hover{
transform:translateY(-3px);
box-shadow:0 10px 20px rgba(123,44,255,.3);
}

.login-link{
text-align:center;
margin-top:15px;
}

.login-link a{
text-decoration:none;
color:#7b2cff;
font-weight:600;
}

.success{
background:#d4edda;
color:#155724;
padding:12px;
border-radius:10px;
margin-bottom:15px;
}

.error{
background:#f8d7da;
color:#721c24;
padding:12px;
border-radius:10px;
margin-bottom:15px;
}

.right{
flex:1;
background:linear-gradient(
135deg,
#a855f7,
#6d28d9
);
display:flex;
justify-content:center;
align-items:center;
position:relative;
overflow:hidden;
}

.right::before{
content:'';
position:absolute;
width: 50%;
height:500px;
border-radius:50%;
background:rgba(255,255,255,.1);
top:-150px;
right:-150px;
}

.right::after{
content:'';
position:absolute;
width:350px;
height:350px;
border-radius:50%;
background:rgba(255,255,255,.08);
bottom:-100px;
left:-100px;
}

.laptop{
width: 100%;
height: 100%;
z-index:2;
}

.laptop img{
width:100%;
height: 100%;
object-fit:cover;
}

@media(max-width:900px){

.container{
flex-direction:column;
}

.right{
display:none;
}

.left{
padding:30px;
}

}

</style>
</head>
<body>

<div class="container">

<div class="left">

<a href="Homepage.php" style="text-decoration: none;">
    <div class="logo">
    <i class="fas fa-store"></i> GoShop
    </div>
</a>

<h1>Create Account</h1>
<p class="subtitle">
Register your account and upload profile image.
</p>

<?php echo $message; ?>

<form method="POST"
enctype="multipart/form-data">

<div class="profile-upload">
    <label class="avatar">
        <img id="preview" src="https://cdn-icons-png.flaticon.com/512/149/149071.png">
        <input type="file" name="profile_image" id="profile" accept="image/*">
    </label>
</div>

<div class="form-group">
    <i class="fa fa-user"></i>
    <input type="text" name="fullname" class="form-control" placeholder="Full Name" required>
</div>

<div class="form-group">
    <i class="fa fa-envelope"></i>
    <input type="email" name="email" class="form-control" placeholder="Email" required>
</div>

<div class="form-group">
    <i class="fa fa-lock"></i>
    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
</div>

<button type="submit" name="register" class="btn"> Create Account </button>

<div class="login-link"> Already have account? <a href="index.php">Login</a> </div>

</form>

</div>

<div class="right">

<div class="laptop">

<img src="uploads/taru-goyal-bXWiwLQjQu0-unsplash.jpg">

</div>

</div>

</div>

<script>

document.getElementById('profile')
.addEventListener('change',function(e){

const file = e.target.files[0];

if(file){

const reader = new FileReader();

reader.onload=function(event){
document.getElementById('preview')
.src=event.target.result;
};

reader.readAsDataURL(file);

}

});

function togglePassword(){

let pass=
document.getElementById("password");

if(pass.type==="password"){
pass.type="text";
}
else{
pass.type="password";
}

}

</script>

</body>
</html>