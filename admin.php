<!-- =========================================================
ADMIN REGISTER + LOGIN PAGE
FILE NAME : admin_auth.php
========================================================= -->

<?php

session_start();

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
ADMIN REGISTER
========================================================= */

if(isset($_POST['admin_register'])){

    $fullname =
    $_POST['fullname'];

    $email =
    $_POST['email'];

    $password =
    $_POST['password'];

    /* IMAGE */

    $image_name =
    $_FILES['image']['name'];

    $tmp_name =
    $_FILES['image']['tmp_name'];

    if(!empty($image_name)){

        move_uploaded_file(
            $tmp_name,
            "uploads/" . $image_name
        );

    }else{

        $image_name = "default.png";

    }

    /* INSERT ADMIN */

    $insert_sql = "

    INSERT INTO admins(

        fullname,
        email,
        password,
        image

    )

    VALUES(

        '$fullname',
        '$email',
        '$password',
        '$image_name'

    )

    ";

    if($conn->query($insert_sql)){

        echo "
        <script>
        alert('Admin Register Success');
        window.location='admin_auth.php';
        </script>
        ";

    }

}

/* =========================================================
ADMIN LOGIN
========================================================= */

if(isset($_POST['admin_login'])){

    $email =
    $_POST['email'];

    $password =
    $_POST['password'];

    $login_sql = "

    SELECT *
    FROM admins
    WHERE email='$email'
    AND password='$password'

    ";

    $login_result =
    $conn->query($login_sql);

    if($login_result->num_rows > 0){

        $admin =
        $login_result->fetch_assoc();

        /* SESSION */

        $_SESSION['admin_id'] =
        $admin['id'];

        $_SESSION['admin_name'] =
        $admin['fullname'];

        $_SESSION['admin_email'] =
        $admin['email'];

        $_SESSION['admin_image'] =
        $admin['image'];

        header(
            "Location: dashboard.php"
        );

    }else{

        echo "
        <script>
        alert('Invalid Email or Password');
        </script>
        ";

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
Admin Authentication
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
    height:100vh;

    display:flex;
    justify-content:center;
    align-items:center;

    background:
    linear-gradient(
        135deg,
        #0f172a,
        #2563eb
    );

    overflow:hidden;
}

/* =========================================================
CONTAINER
========================================================= */

.container{
    width:950px;
    height:600px;

    background:white;

    border-radius:30px;

    overflow:hidden;

    display:flex;

    box-shadow:
    0 15px 40px rgba(0,0,0,.2);

    animation:fadeIn 1s ease;
}

/* =========================================================
LEFT PANEL
========================================================= */

.left-panel{
    width:50%;

    background:
    linear-gradient(
        135deg,
        #2563eb,
        #1e3a8a
    );

    color:white;

    padding:60px;

    display:flex;
    flex-direction:column;
    justify-content:center;

    position:relative;
}

.left-panel h1{
    font-size:50px;
    margin-bottom:20px;
}

.left-panel p{
    line-height:1.8;
    opacity:.9;
}

/* =========================================================
RIGHT PANEL
========================================================= */

.right-panel{
    width:50%;

    position:relative;

    overflow:hidden;
}

/* =========================================================
FORMS
========================================================= */

.form-box{
    position:absolute;

    width:100%;
    height:100%;

    padding:50px;

    transition:.6s;
}

.login-form{
    left:0;
}

.register-form{
    left:100%;
}

.container.active .login-form{
    left:-100%;
}

.container.active .register-form{
    left:0;
}

/* =========================================================
FORM TITLE
========================================================= */

.form-box h2{
    margin-bottom:30px;
    font-size:35px;
    color:#111827;
}

/* =========================================================
INPUTS
========================================================= */

.input-box{
    margin-bottom:20px;
    position:relative;
}

.input-box input{
    width:100%;

    padding:16px 18px;

    border:none;

    background:#f1f5f9;

    border-radius:14px;

    outline:none;

    font-size:15px;
}

.input-box i{
    position:absolute;
    right:18px;
    top:18px;
    color:#666;
}

/* FILE */

.input-box input[type="file"]{
    padding:14px;
}

/* BUTTON */

.btn{
    width:100%;

    padding:16px;

    border:none;

    background:
    linear-gradient(
        135deg,
        #2563eb,
        #1d4ed8
    );

    color:white;

    border-radius:14px;

    font-size:16px;

    cursor:pointer;

    transition:.3s;
}

.btn:hover{
    transform:translateY(-3px);
}

/* =========================================================
SWITCH TEXT
========================================================= */

.switch-text{
    margin-top:25px;
    text-align:center;
    color:#666;
}

.switch-text span{
    color:#2563eb;
    cursor:pointer;
    font-weight:bold;
}

/* =========================================================
ANIMATION
========================================================= */

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

/* =========================================================
RESPONSIVE
========================================================= */

@media(max-width:900px){

    .container{
        width:95%;
        height:auto;
        flex-direction:column;
    }

    .left-panel{
        width:100%;
        height:220px;
    }

    .right-panel{
        width:100%;
        height:700px;
    }

}

</style>

</head>

<body>

<div class="container" id="container">

    <!-- =====================================================
    LEFT PANEL
    ====================================================== -->

    <div class="left-panel">

        <h1>
            GOSHOP ADMIN
        </h1>

        <p>
            Modern admin dashboard login and register system with profile image upload.
        </p>

    </div>

    <!-- =====================================================
    RIGHT PANEL
    ====================================================== -->

    <div class="right-panel">

        <!-- LOGIN -->

        <div class="form-box login-form">

            <h2>
                Admin Login
            </h2>

            <form method="POST">

                <div class="input-box">

                    <input
                    type="email"
                    name="email"
                    placeholder="Admin Email"
                    required
                    >

                    <i class="fa-solid fa-envelope"></i>

                </div>

                <div class="input-box">

                    <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    required
                    >

                    <i class="fa-solid fa-lock"></i>

                </div>

                <button
                type="submit"
                name="admin_login"
                class="btn"
                >

                    Login

                </button>

            </form>

            <div class="switch-text">

                Don't have account?

                <span id="showRegister">
                    Register
                </span>

            </div>

        </div>

        <!-- REGISTER -->

        <div class="form-box register-form">

            <h2>
                Admin Register
            </h2>

            <form
            method="POST"
            enctype="multipart/form-data"
            >

                <div class="input-box">

                    <input
                    type="text"
                    name="fullname"
                    placeholder="Full Name"
                    required
                    >

                    <i class="fa-solid fa-user"></i>

                </div>

                <div class="input-box">

                    <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    required
                    >

                    <i class="fa-solid fa-envelope"></i>

                </div>

                <div class="input-box">

                    <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    required
                    >

                    <i class="fa-solid fa-lock"></i>

                </div>

                <div class="input-box">

                    <input
                    type="file"
                    name="image"
                    >

                </div>

                <button
                type="submit"
                name="admin_register"
                class="btn"
                >

                    Register

                </button>

            </form>

            <div class="switch-text">

                Already have account?

                <span id="showLogin">
                    Login
                </span>

            </div>

        </div>

    </div>

</div>

<!-- =========================================================
JAVASCRIPT
========================================================= -->

<script>

const container =
document.getElementById("container");

document
.getElementById("showRegister")
.onclick = () => {

    container.classList.add("active");

};

document
.getElementById("showLogin")
.onclick = () => {

    container.classList.remove("active");

};

</script>

</body>
</html>