<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "store_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
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

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Banner</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg, #4F46E5, #7C3AED, #2563EB);
    padding:30px;
}

.container{
    width:100%;
    max-width: 780px;
}

.form-card{
    background:rgba(255,255,255,.95);
    border-radius:20px;
    padding:40px;
    box-shadow:0 20px 40px rgba(0,0,0,.15);
}

.form-title{
    font-size:30px;
    font-weight:700;
    color:#1F2937;
}

.form-subtitle{
    color:#6B7280;
    margin-top:8px;
    margin-bottom:35px;
}

.form-group{
    margin-bottom:22px;
}

.form-group label{
    display:block;
    font-weight:600;
    color:#374151;
    margin-bottom:8px;
}

.form-control{
    width:100%;
    padding:14px 16px;
    border:1px solid #D1D5DB;
    border-radius:12px;
    outline:none;
    transition:.3s;
    font-size:15px;
}

.form-control:focus{
    border-color:#4F46E5;
    box-shadow:0 0 0 4px rgba(79,70,229,.15);
}

textarea.form-control{
    resize:vertical;
    min-height:120px;
}

.row{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

input[type=file]{
    background:#F9FAFB;
    cursor:pointer;
}

.preview{
    margin-top:15px;
    display:none;
}

.preview img{
    width:100%;
    max-height:250px;
    object-fit:cover;
    border-radius:15px;
    border:2px dashed #CBD5E1;
}

.btn-group{
    display:flex;
    gap:15px;
    margin-top:30px;
}

button{
    flex:1;
    border:none;
    padding:15px;
    font-size:16px;
    border-radius:12px;
    cursor:pointer;
    transition:.3s;
    font-weight:600;
}

.save{
    background:#4F46E5;
    color:#fff;
}

.save:hover{
    background:#4338CA;
}

.reset{
    background:#E5E7EB;
}

.reset:hover{
    background:#D1D5DB;
}

@media(max-width:768px){

.row{
    grid-template-columns:1fr;
}

.form-card{
    padding:25px;
}

.form-title{
    font-size:24px;
}

}
</style>
</head>
<body>

<div class="container">

<div class="form-card">

<h2 class="form-title">Add Homepage Banner</h2>
<p class="form-subtitle">
Fill in the banner information below.
</p>

<form action="" method="POST" enctype="multipart/form-data">

<div class="preview" id="preview">
<img id="output">
</div>

<div class="form-group">
<label>Banner Image</label> 
<input type="file" name="image" id="image" class="form-control" accept="image/*" require>
</div>

<div class="form-group">
<label>Banner Title</label>
<input type="text" name="title" class="form-control" placeholder="Summer Sale" require>
</div>

<div class="form-group">
<label>Banner Subtitle</label>
<textarea name="subtitle" class="form-control" placeholder="Write banner description..." require></textarea>
</div>

<div class="btn-group">

<button type="submit" class="save" name="add_banner"> Add </button>

<a href="setting.php?page=banner_manager">
    <button type="button" class="reset"> Back </button>
</a>



</div>

</form>

</div>

</div>

<script>

image.onchange=function(e){

const reader=new FileReader();

reader.onload=function(){

output.src=reader.result;
preview.style.display='block';

}

reader.readAsDataURL(e.target.files[0]);

}

</script>

</body>
</html>