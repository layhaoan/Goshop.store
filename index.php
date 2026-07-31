<!-- save as: login.php -->

<?php
session_start();
include "db.php";

$message = "";

if(isset($_POST['login'])){

$email = $_POST['email'];
$password = md5($_POST['password']);

$sql = "SELECT * FROM users
WHERE email='$email'
AND password='$password'";

$result = $conn->query($sql);

if($result->num_rows > 0){

    $user = $result->fetch_assoc();

    $_SESSION['user_id'] = $user['id'];

    echo "

            <script>

            alert('Login Success');

            window.location=
            'Homepage.php';

            </script>

            ";
    exit();

}else{
    $message = "Invalid Email or Password";
}
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>GoShop Login</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
background:#ececf5;
padding:20px;
overflow:hidden;
}

.container{
width: 900px;
height: 500px;
max-width:100%;
background:#fff;
border-radius:20px;
overflow:hidden;
display:flex;
box-shadow:0 20px 40px rgba(0,0,0,.15);
animation:fadeIn 1s ease;
}

.left{
width:50%;
padding:60px;
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

.logo i{
margin-right:8px;
}

.left h1{
font-size: 1.4rem;
position: relative;
bottom: 10px;
margin-bottom:10px;
color:#333;
}

.left p{
color:#777;
margin-bottom: 5px;
font-size: 0.89rem;
position: relative;
bottom: 20px;
}

.input-group{
position:relative;
margin-bottom:18px;
}

.input-group input{
width:100%;
padding: 8px 45px;
border:none;
background:#f4f4fb;
border-radius:30px;
font-size:15px;
outline:none;
transition:.3s;
}

.input-group input:focus{
box-shadow:0 0 0 3px rgba(123,44,255,.15);
}

.input-group i{
position:absolute;
left:18px;
top:50%;
transform:translateY(-50%);
color:#999;
}

.toggle{
left:auto !important;
right:18px;
cursor:pointer;
}

.option{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;
font-size:14px;
}

.option label{
display:flex;
align-items:center;
gap:5px;
}

.option a{
text-decoration:none;
color:#7b2cff;
font-weight:600;
}

.btn{
width:100%;
padding: 8px;
border:none;
border-radius:30px;
background:linear-gradient(45deg,#8e2de2,#4a00e0);
color:#fff;
font-size:16px;
cursor:pointer;
transition:.3s;
}

.btn:hover{
transform:translateY(-3px);
box-shadow:0 10px 20px rgba(123,44,255,.3);
}

.register-link{
margin-top:20px;
text-align:center;
}

.register-link a{
color:#7b2cff;
font-weight:600;
text-decoration:none;
}

.right{
width:50%;
background:linear-gradient(135deg,#8e2de2,#4a00e0);
display:flex;
justify-content:center;
align-items:center;
position:relative;
overflow:hidden;
}

.right::before{
content:'';
position:absolute;
width:650px;
height:650px;
background:rgba(255,255,255,.08);
border-radius:50%;
top:-200px;
left:-100px;
}

.right::after{
content:'';
position:absolute;
width:450px;
height:450px;
background:rgba(255,255,255,.06);
border-radius:50%;
bottom:-150px;
right:-80px;
}

.floating-card{
position:relative;
width:350px;
height:220px;
animation:float 4s ease-in-out infinite;
}

.card{
position:absolute;
width:100%;
height:100%;
border-radius:20px;
background:#fff;
box-shadow:0 15px 30px rgba(0,0,0,.2);
}

.card:nth-child(1){
transform:translateY(40px) rotate(-10deg);
opacity:.25;
}

.card:nth-child(2){
transform:translateY(20px) rotate(-5deg);
opacity:.5;
}

.card:nth-child(3){
display:flex;
align-items:center;
justify-content:center;
font-size:80px;
color:#7b2cff;
font-weight:bold;
}

.message{
padding:12px;
border-radius:10px;
margin-bottom:15px;
font-size:14px;
}

.success{
background:#d4edda;
color:#155724;
}

.error{
background:#f8d7da;
color:#721c24;
}

@keyframes float{
0%,100%{
transform:translateY(0);
}
50%{
transform:translateY(-20px);
}
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

@media(max-width:900px){

.container{
flex-direction:column;
}

.left,
.right{
width:100%;
}

.right{
height:320px;
}

.left{
padding:40px 25px;
}

.left h1{
font-size:30px;
}
}

</style>
</head>

<body>

<div class="container">

<div class="left">

<div class="logo">
<i class="fa-solid fa-store"></i>
GoShop
</div>

<h1>Welcome Back</h1>
<p>Sign in to continue shopping.</p>

<?php if($message!=""){ ?>
<div class="message <?php echo $type; ?>">
<?php echo $message; ?>
</div>
<?php } ?>

<form method="POST">

<div class="input-group">
    <i class="fa fa-user"></i>
    <input type="text" name="email" placeholder="Email" required>
</div>

<div class="input-group">
    <i class="fa fa-lock"></i>
    <input type="password" name="password" id="password" placeholder="Password" required>
    <i class="fa fa-eye toggle" onclick="togglePassword()"></i>
</div>

<div class="option">

<a href="new_register.php">Forgot Password?</a>
</div>

<button class="btn" name="login">
Login
</button>

<div class="register-link">
Don't have an account?
<a href="register.php">Register</a>
</div>

</form>

</div>

<div class="right">

<div class="floating-card">

<div class="card"></div>
<div class="card"></div>

<div class="card">
<i class="fa-solid fa-cart-shopping"></i>
</div>

</div>

</div>

</div>

<script>

function togglePassword()
{
    let pass =
    document.getElementById("password");

    let icon =
    document.querySelector(".toggle");

    if(pass.type=="password")
    {
        pass.type="text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    }
    else
    {
        pass.type="password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}

</script>

</body>
</html>