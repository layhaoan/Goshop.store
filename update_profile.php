<?php

session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];

$message = "";

if(isset($_POST['update'])){

    /* SAFE DATA */

    $phone = $conn->real_escape_string($_POST['phone']);

    $gender = $conn->real_escape_string($_POST['gender']);

    $birthday = $conn->real_escape_string($_POST['birthday']);

    $country = $conn->real_escape_string($_POST['country']);

    $city = $conn->real_escape_string($_POST['city']);

    $bio = $conn->real_escape_string($_POST['bio']);

    /* IMAGE */

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

        $conn->query("
        UPDATE users SET
        profile_image='$imageName'
        WHERE id='$id'
        ");
    }

    /* UPDATE */

    $sql = "
    UPDATE users SET

    phone='$phone',
    gender='$gender',
    birthday='$birthday',
    country='$country',
    city='$city',
    bio='$bio'

    WHERE id='$id'
    ";

    if($conn->query($sql)){

        $message = "Profile Updated Successfully!";
        header("Location: profile.php");

    }else{

        $message = "SQL Error : " . $conn->error;
    }
}

$result = $conn->query(
"SELECT * FROM users WHERE id='$id'"
);

$user = $result->fetch_assoc();

?>

    <!DOCTYPE html>
    <html>
    <head>
    <title>Update Profile</title>

    <style>

    *{
        margin:0;
        padding:0;
        box-sizing:border-box;
        font-family:Arial;
    }

    body{
        background:#0f172a;
        display:flex;
        justify-content:center;
        align-items:center;
        min-height:100vh;
        padding:20px;
    }

    .form-box{
        width:100%;
        max-width:700px;
        background:white;
        padding:30px;
        border-radius:20px;
    }

    h2{
        text-align:center;
        margin-bottom:20px;
    }

    .preview{
        width:120px;
        height:120px;
        border-radius:50%;
        object-fit:cover;
        display:block;
        margin:10px auto;
        border:4px solid #2563eb;
    }

    .input-box{
        margin-bottom:15px;
    }

    .input-box input,
    .input-box select,
    .input-box textarea{
        width:100%;
        padding:12px;
        border:1px solid #ccc;
        border-radius:10px;
    }

    textarea{
        height:120px;
        resize:none;
    }

    button{
        width:100%;
        padding:14px;
        background:#2563eb;
        color:white;
        border:none;
        border-radius:10px;
        cursor:pointer;
    }

    button:hover{
        background:#1d4ed8;
    }

    .message{
        background:#dcfce7;
        color:#166534;
        padding:10px;
        border-radius:10px;
        margin-bottom:15px;
        text-align:center;
    }

    .profile-btn{
        display:block;
        text-align:center;
        margin-top:15px;
    }

    </style>
    </head>
    <body>

    <div class="form-box">

    <h2>Update Personal Information</h2>

    <?php if($message != ""){ ?>
    <div class="message">
    <?php echo $message; ?>
    </div>
    <?php } ?>

    <form method="POST" enctype="multipart/form-data">

    <img
    src="uploads/<?php echo $user['profile_image']; ?>"
    id="preview"
    class="preview">

    <div class="input-box">
    <input type="file"
    name="profile_image"
    accept="image/*"
    onchange="previewImage(event)">
    </div>

    <div class="input-box">
    <input type="text" value="<?php echo $user['fullname']; ?>" disabled> 
    </div>

    <div class="input-box">
    <input type="email" value="<?php echo $user['email']; ?>" disabled>
    </div>

    <div class="input-box">
    <input type="text" name="phone" placeholder="Phone" value="<?php echo $user['phone']; ?>">
    </div>

    <div class="input-box">
    <select name="gender">

    <option value="">Gender</option>

    <option <?php if($user['gender']=="Male"){ echo "selected"; } ?>> Male </option>

    <option  <?php if($user['gender']=="Female"){ echo "selected"; } ?>> Female </option>

    <option <?php if($user['gender']=="Other"){ echo "selected"; } ?>> Other </option>

    </select>
    </div>

    <div class="input-box">
    <input type="date" name="birthday" value="<?php echo $user['birthday']; ?>">
    </div>

    <div class="input-box">
    <input type="text" name="country" placeholder="Country" value="<?php echo $user['country']; ?>">
    </div>

    <div class="input-box">
    <input type="text" name="city" placeholder="City" value="<?php echo $user['city']; ?>">
    </div>

    <div class="input-box">
    <textarea name="bio" placeholder="Bio"><?php echo $user['bio']; ?></textarea>
    </div>

    <button type="submit" name="update"> Update Profile </button>

    </form>

    <div class="profile-btn">
    <a href="profile.php">View Profile</a>
    </div>

    </div>

    <script>

    function previewImage(event){

        let reader = new FileReader();

        reader.onload = function(){
            document.getElementById("preview").src = reader.result;
        }

        reader.readAsDataURL(event.target.files[0]);
    }

    </script>

    </body>
    </html>