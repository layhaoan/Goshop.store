<?php

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "store_db"
);

$message = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $category_name  = trim($_POST['category_name']);
    $category_title = trim($_POST['category_title']);

    $image_name = "";

    if(isset($_FILES['category_image']) &&
       $_FILES['category_image']['error'] == 0){

        if(!is_dir("uploaded_categories")){
            mkdir("uploaded_categories",0777,true);
        }

        $image_name =
        time() . "_" .
        basename(
            $_FILES['category_image']['name']
        );

        move_uploaded_file(
            $_FILES['category_image']['tmp_name'],
            "uploaded_categories/" . $image_name
        );
    }

    $stmt = $conn->prepare("
        INSERT INTO categories
        (
            category_name,
            category_title,
            category_image
        )
        VALUES
        (
            ?, ?, ?
        )
    ");

    $stmt->bind_param(
        "sss",
        $category_name,
        $category_title,
        $image_name
    );

    if($stmt->execute()){

        $message =
        "<div class='success'>
        Category Added Successfully
        </div>";

    }else{

        $message =
        "<div class='error'>
        Failed To Add Category
        </div>";

    }
}
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Add Category</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

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

background:#f4f7fc;

padding:20px;
}

.container{

width:1100px;
max-width:100%;

background:#fff;

border-radius:30px;

overflow:hidden;

display:flex;

box-shadow:
0 20px 50px
rgba(0,0,0,.15);

}

/* LEFT */

.left{

width:40%;

background:
linear-gradient(
135deg,
#2563eb,
#4f46e5
);

color:white;

padding:50px 40px;

display:flex;
flex-direction:column;
justify-content:center;
align-items:center;
text-align:center;

position:relative;
overflow:hidden;
}

.left::before{

content:'';

position:absolute;

width:300px;
height:300px;

background:
rgba(255,255,255,.08);

border-radius:50%;

top:-80px;
right:-80px;

}

.left::after{

content:'';

position:absolute;

width:250px;
height:250px;

background:
rgba(255,255,255,.08);

border-radius:50%;

bottom:-80px;
left:-80px;

}

.logo{

width:120px;
height:120px;

background:white;

border-radius:50%;

display:flex;
align-items:center;
justify-content:center;

margin-bottom:20px;
}

.logo i{

font-size:50px;

color:#2563eb;
}

.left h1{

margin-bottom:15px;
font-size:35px;

}

.left p{

line-height:1.8;

opacity:.9;
}

/* RIGHT */

.right{

width:60%;

padding:50px;
}

.right h2{

text-align:center;

margin-bottom:30px;

font-size:34px;

color:#111827;
}

.form-group{

margin-bottom:20px;
}

label{

display:block;

margin-bottom:8px;

font-weight:600;

color:#374151;
}

input[type=text]{

width:100%;

padding:15px;

border:none;

background:#f3f4f6;

border-radius:12px;

font-size:15px;

outline:none;
}

.image-upload{

border:2px dashed #4f46e5;

padding:20px;

text-align:center;

border-radius:15px;

cursor:pointer;

transition:.3s;
}

.image-upload:hover{

background:#eef2ff;
}

.image-upload input{

display:none;
}

.preview{

margin-top:15px;

display:none;
}

.preview img{

width:150px;
height:150px;

object-fit:cover;

border-radius:15px;

box-shadow:
0 10px 25px
rgba(0,0,0,.15);
}

button{

width:100%;

padding:16px;

border:none;

border-radius:12px;

background:
linear-gradient(
135deg,
#2563eb,
#4f46e5
);

color:white;

font-size:17px;

font-weight:600;

cursor:pointer;

transition:.3s;
}

button:hover{

transform:translateY(-2px);
}

.success{

background:#dcfce7;
color:#166534;

padding:15px;

border-radius:12px;

margin-bottom:20px;
}

.error{

background:#fee2e2;
color:#991b1b;

padding:15px;

border-radius:12px;

margin-bottom:20px;
}

@media(max-width:900px){

.container{

flex-direction:column;
}

.left,
.right{

width:100%;
}

}

</style>

</head>
<body>

<div class="container">

<div class="left">

<div class="logo">
<i class="fas fa-layer-group"></i>
</div>

<h1>GoShop</h1>

<p>

Create and manage product categories
for your ecommerce store.

Add category images, titles,
and organize your products easily.

</p>

</div>

<div class="right">

<h2>Add Category</h2>

<?php echo $message; ?>

<form
method="POST"
enctype="multipart/form-data">

<div class="form-group">

<label>
Category Name
</label>

<input
type="text"
name="category_name"
required>

</div>

<div class="form-group">

<label>
Category Title
</label>

<input
type="text"
name="category_title"
required>

</div>

<div class="form-group">

<label>
Category Image
</label>

<label class="image-upload">

<i class="fas fa-cloud-upload-alt"></i>
<br><br>

Click To Upload Image

<input
type="file"
name="category_image"
id="imageInput"
accept="image/*"
required>

</label>

<div
class="preview"
id="preview">

<img id="previewImg">

</div>

</div>

<button type="submit">

<i class="fas fa-plus-circle"></i>

Add Category

</button>

</form>

</div>

</div>

<script>

const imageInput =
document.getElementById(
'imageInput'
);

const preview =
document.getElementById(
'preview'
);

const previewImg =
document.getElementById(
'previewImg'
);

imageInput.addEventListener(
'change',
function(){

const file =
this.files[0];

if(file){

const reader =
new FileReader();

reader.onload =
function(e){

preview.style.display =
'block';

previewImg.src =
e.target.result;

}

reader.readAsDataURL(file);

}

});

</script>

</body>
</html>