<?php

session_start();

/* =========================================================
DATABASE CONNECTION
========================================================= */

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "store_db"
);

if($conn->connect_error){
    die("Connection Failed");
}

/* =========================================================
STAFF LOGIN
========================================================= */

$error = "";

if(isset($_POST['staff_login'])){

    $email =
    trim($_POST['email']);

    $password =
    trim($_POST['password']);

    /* =====================================
    CHECK STAFF
    ===================================== */

    $sql = "

    SELECT *
    FROM staff
    WHERE email='$email'

    ";

    $result =
    $conn->query($sql);

    if($result->num_rows > 0){

        $staff =
        $result->fetch_assoc();

        /* =====================================
        CHECK PASSWORD
        ===================================== */

        /*
        SIMPLE PASSWORD
        */

        if(
            $password ==
            $staff['password']
        ){

            /* SESSION */

            $_SESSION['admin_id'] =
            $staff['id'];

            $_SESSION['admin_name'] =
            $staff['fullname'];

            $_SESSION['admin_email'] =
            $staff['email'];

            $_SESSION['admin_image'] =
            $staff['image'];

            $_SESSION['role'] =
            $staff['role'];

            /* SUCCESS */

            echo "

            <script>

            alert('Login Success');

            window.location=
            'new_dashboard.php';

            </script>

            ";

        }else{

            $error =
            "Wrong Password";

        }

    }else{

        $error =
        "Staff Not Found";

    }

}

/* =========================================================
STAFF LOGIN BEFORE RESET PASSWORD
========================================================= */
if(isset($_POST['staff_login'])){

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $result = mysqli_query(
        $conn,
        "SELECT * FROM staff
         WHERE email='$email'
         LIMIT 1"
    );

    if(mysqli_num_rows($result) > 0){

        $staff = mysqli_fetch_assoc($result);

        if(password_verify($password, $staff['password'])){

            session_start();
            $_SESSION['staff_id'] = $staff['id'];
            $_SESSION['staff_name'] = $staff['fullname'];

            /* SUCCESS */

            echo "

            <script>

            alert('Login Success');

            window.location=
            'new_dashboard.php';

            </script>

            ";
            exit;

        }else{

            $error = "Incorrect password";
        }

    }else{

        $error = "Email not found";
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>
Staff Login
</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial;
}

body{

    min-height:100vh;

    display:flex;
    justify-content:center;
    align-items:center;

    background: #f0f5f9;

    overflow:hidden;

    position:relative;
}

/* =========================================================
BACKGROUND CIRCLE
========================================================= */

.circle{
    position:absolute;

    border-radius:50%;

    background:
    rgba(255,255,255,.08);

    animation:float 8s infinite linear;
}

.circle1{
    width:300px;
    height:300px;

    top:-100px;
    left:-100px;
}

.circle2{
    width:250px;
    height:250px;

    bottom:-80px;
    right:-80px;
}

.circle3{
    width:180px;
    height:180px;

    top:50%;
    left:10%;
}

/* =========================================================
LOGIN CARD
========================================================= */

.login-card{

    width:420px;

    background:
    rgba(255,255,255,.12);

    backdrop-filter:blur(14px);

    border:
    1px solid rgba(255,255,255,.2);

    border-radius:35px;

    padding:40px;

    position:relative;

    z-index:10;

    box-shadow:
    0 20px 50px rgba(0,0,0,.25);

    animation:fadeUp .8s ease;
}

/* LOGO */

.logo{
    text-align:center;
    margin-bottom:30px;
}

.logo i{
    width:90px;
    height:90px;

    border-radius:50%;

    background: #5379ad;

    color: #fff;

    display:flex;
    justify-content:center;
    align-items:center;

    font-size:38px;

    margin:auto;
    margin-bottom:18px;
}

.logo h1{
    color: #5379ad;
    font-size:34px;
}

.logo p{
    color:#5379ad;
    margin-top:10px;
}

/* ERROR */

.error-box{

    background:#ffebee;

    color:#ff1744;

    padding:14px 18px;

    border-radius:14px;

    margin-bottom:20px;

    font-size:14px;
}

/* INPUT */

.input-box{
    margin-bottom:22px;
    position:relative;
}

.input-box input{

    width:100%;

    padding:18px 20px 18px 55px;

    border: 1px solid #5379ad;
    outline:none;

    border-radius:18px;

    background:
    rgba(255,255,255,.15);

    color:#5379ad;

    font-size:15px;
}

.input-box input::placeholder{
    color:#5379ad;
}

.input-box i{

    position:absolute;

    top:50%;
    left:20px;

    transform:translateY(-50%);

    color:#5379ad;
}

/* BUTTON */

.login-btn{

    width:100%;

    padding:18px;

    border:none;

    border-radius:18px;

    background:#5379ad;

    color:#fff;

    font-size:16px;

    font-weight:700;

    cursor:pointer;

    transition:.3s;
}

.login-btn:hover{

    transform:translateY(-4px);

    box-shadow:
    0 10px 25px rgba(255,255,255,.25);
}

/* FOOTER */

.footer-text{

    margin-top:25px;

    text-align:center;

    color:#5379ad;

    font-size:14px;
}

/* =========================================================
ANIMATION
========================================================= */

@keyframes fadeUp{

    from{
        opacity:0;
        transform:translateY(30px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }

}

@keyframes float{

    0%{
        transform:translateY(0px);
    }

    50%{
        transform:translateY(25px);
    }

    100%{
        transform:translateY(0px);
    }

}

/* =========================================================
RESPONSIVE
========================================================= */

@media(max-width:500px){

    .login-card{
        width:92%;
        padding:30px 20px;
    }

}

</style>

</head>

<body>

<!-- BACKGROUND -->

<div class="circle circle1"></div>
<div class="circle circle2"></div>
<div class="circle circle3"></div>

<!-- =========================================================
LOGIN CARD
========================================================= -->

<div class="login-card">

    <!-- LOGO -->

    <div class="logo">

        <i class="fa-solid fa-user-shield"></i>

        <h1>
            Staff Login
        </h1>

        <p>
            GoShop Management System
        </p>

    </div>

    <!-- ERROR -->

    <?php if(!empty($error)){ ?>

    <div class="error-box">

        <?php echo $error; ?>

    </div>

    <?php } ?>

    <!-- FORM -->

    <form method="POST">

        <!-- EMAIL -->

        <div class="input-box">

            <i class="fa-solid fa-envelope"></i>

            <input

            type="email"

            name="email"

            placeholder="Staff Email"

            required

            >

        </div>

        <!-- PASSWORD -->

        <div class="input-box">

            <i class="fa-solid fa-lock"></i>

            <input

            type="password"

            name="password"

            placeholder="Password"

            required

            >

        </div>

        <!-- BUTTON -->

        <button

        type="submit"

        name="staff_login"

        class="login-btn"

        >

            Login Dashboard

        </button>

    </form>

    <!-- FOOTER -->

    <div class="footer-text">

        © GoShop Ecommerce Dashboard

    </div>

</div>

</body>
</html>