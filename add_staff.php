<?php

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "store_db"
);

if(isset($_POST['add_staff'])){

    $fullname =
    $_POST['fullname'];

    $email =
    $_POST['email'];

    $password =
    $_POST['password'];

    $phone =
    $_POST['phone'];

    $role =
    $_POST['role'];

    /* IMAGE */

    $image =
    $_FILES['image']['name'];

    $tmp =
    $_FILES['image']['tmp_name'];

    if(!empty($image)){

        move_uploaded_file(
            $tmp,
            "uploads/" . $image
        );

    }else{

        $image = "default.png";

    }

    /* INSERT */

    $sql = "

    INSERT INTO staff(

        fullname,
        email,
        password,
        phone,
        role,
        image

    )

    VALUES(

        '$fullname',
        '$email',
        '$password',
        '$phone',
        '$role',
        '$image'

    )

    ";

    if($conn->query($sql)){

        echo "
        <script>
        alert('Staff Added Successfully');
        window.location='staff_list.php';
        </script>
        ";

    }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Staff</title>

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
    background:linear-gradient(
        135deg,
        #2563eb,
        #7c3aed
    );
    padding:20px;
}

.form-container{
    width:550px;
    max-width:100%;
    background:rgba(255,255,255,.12);
    backdrop-filter:blur(15px);
    border:1px solid rgba(255,255,255,.2);
    border-radius:25px;
    padding:35px;
    box-shadow:0 20px 50px rgba(0,0,0,.25);
    animation:fadeIn .6s ease;
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

.title{
    text-align:center;
    color:white;
    margin-bottom:30px;
}

.title h2{
    font-size:32px;
    margin-bottom:8px;
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

/* INPUT */

.form-group{
    position:relative;
    margin-bottom:20px;
}

.form-group input,
.form-group select{
    width:100%;
    padding:15px;
    border:none;
    outline:none;
    border-radius:12px;
    background:rgba(255,255,255,.15);
    color:white;
    font-size:15px;
}

.form-group select option{
    color:black;
}

.form-group input::placeholder{
    color:#ddd;
}

.icon{
    position:absolute;
    right:15px;
    top:17px;
    color:white;
}

.password-toggle{
    cursor:pointer;
}

/* BUTTON */

.btn{
    width:100%;
    padding:15px;
    border:none;
    border-radius:12px;
    background:white;
    color:#2563eb;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
    margin: 10px;
}

.btn:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 20px rgba(0,0,0,.2);
}

.back{
    background: #444;
    color: #fff;
}

.row{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
}

@media(max-width:600px){

    .row{
        grid-template-columns:1fr;
    }

    .form-container{
        padding:25px;
    }

}

</style>
</head>
<body>

<div class="form-container">

    <div class="title">
        <h2>Add New Staff</h2>
        <p>Create a new staff account</p>
    </div>

    <form method="POST" enctype="multipart/form-data">

        <div class="image-upload">

            <label for="profileImage">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" id="preview">
            </label>

            <input type="file" name="image" id="profileImage" accept="image/*">
            <label for="profileImage"> Upload Staff Photo </label>

        </div>

        <div class="form-group">
            <input type="text" name="fullname"  placeholder="Full Name" required>
            <i class="fa-solid fa-user icon"></i>
        </div>

        <div class="row">

            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" required>
                <i class="fa-solid fa-envelope icon"></i>
            </div>

            <div class="form-group">
                <input  type="text" name="phone" placeholder="Phone Number" required>
                <i class="fa-solid fa-phone icon"></i>
            </div>

        </div>

        <div class="row">

            <div class="form-group">

                <select name="role">

                <option value="Admin">
                    Admin
                </option>

                <option value="Manager">
                    Manager
                </option>

                <option value="Staff">
                    Staff
                </option>

            </select>

            </div>

            <div class="form-group">

                <select name="status" required>
                    <option value="Status"> Status </option>
                    <option value="Active">  Active </option>
                    <option value="Inactive"> Inactive </option>
                </select>

            </div>

        </div>

        <div class="form-group">
            <input  type="password" id="password" name="password" placeholder="Password"  required>
            <i class="fa-solid fa-eye icon password-toggle"  onclick="togglePassword()"></i>
        </div>

        <button type="submit" name="add_staff" class="btn">

            <i class="fa-solid fa-user-plus"></i>
            Add Staff

        </button>
        <a href="staff_list.php">
            <button type="button"  class="btn back">
            Back
        </button>
        </a>

    </form>

</div>

<script>

function togglePassword(){

    let password =
    document.getElementById(
        "password"
    );

    if(password.type==="password"){
        password.type="text";
    }else{
        password.type="password";
    }

}

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