<?php
// DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "", "store_db");

$message = "";

if(isset($_POST['add_product']))
{
    $code = $_POST['code'];
    $name = $_POST['name'];
    $product_category = $_POST['product_category'];
    $price = $_POST['price'];
    $qaunlity = $_POST['qaunlity'];
    $stock = $_POST['stock'];
    $discount = $_POST['discount'];
    $description = $_POST['description'];

    $image = "";

    if(isset($_FILES['image']) && $_FILES['image']['name'] != "")
    {
        $image = time() . "_" . $_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "uploads/" . $image
        );
    }

    $sql = "INSERT INTO products
    (
        code,
        name,
        price,
        discount,
        qaunlity,
        stock,
        product_category,
        description,
        image
    )
    VALUES
    (
        '$code',
        '$name',
        '$price',
        '$discount',
        '$qaunlity',
        '$stock',
        '$product_category',
        '$description',
        '$image'
    )";

    if($conn->query($sql))
    {
        $message = "Product Added Successfully";
         header("Location: new_products.php");
        
    }
    else
    {
        $message = "Insert Failed";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Products</title>

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
padding:30px;
background:
linear-gradient(
135deg,
#f7f8fc,
#eef2ff
);
}

/* MAIN CARD */

.container{
  width:100%;
  max-width:1000px;
  background:white;
  border-radius:30px;
  overflow:hidden;
  box-shadow:
  0 20px 50px rgba(0,0,0,.08);
  display:flex;
  gap:40px;
  padding:40px;
  animation:fadeUp .8s ease;
}

/* LEFT */

.left{
width:45%;
}

.upload-box{
  height: 570px;
  border-radius:25px;
  background:#f6f7fb;
  border:3px dashed #2851e6;
  cursor:pointer;
  position:relative;
  overflow:hidden;
  display:flex;
  align-items:center;
  justify-content:center;
  transition:.4s;
}

.upload-box:hover{
  transform:translateY(-5px);
  border-color:#4f46e5;
}

.upload-box img{
  width:100%;
  height:100%;
  object-fit:cover;
  display:none;
}

.upload-content{
text-align:center;
}

.upload-content i{
  font-size:70px;
  color:#4f46e5;
}

.upload-content h2{
  font-size:22px;
  margin-top:15px;
}

/* RIGHT */

.right{
  flex:1;
}

.title{
  font-size: 1.3rem;
  font-weight:700;
  margin-bottom:35px;
  position:relative;
  display:inline-block;
}

.title:after{
  content:"";
  position:absolute;
  bottom:-10px;
  left:0;
  width:100%;
  height:4px;
  background:#4f46e5;
  border-radius:10px;
}

.row{
  display:grid;
  grid-template-columns:1fr 1fr 1fr;
  gap: 10px;
}

.input-group{
  margin-bottom: 15px;
}

.input-group label{
  display:block;
  margin-bottom:8px;
  font-weight:600;
  color:#555;
  font-size: 0.80rem;
}

.input-group input,
.input-group select{
  width:100%;
  padding: 8px;
  border-radius: 8px;
  border:1px solid #ddd;
  outline:none;
  font-size:16px;
  transition:.3s;
  background:#fafafa;
}

.input-group input:focus,
.input-group select:focus{
  border-color:#4f46e5;
  box-shadow:
  0 0 15px rgba(79,70,229,.15);
  background:white;
}

/* BUTTONS */

.btn-area{
  display:flex;
  justify-content:flex-end;
  gap:15px;
  margin-top:20px;
}

.btn{
  padding: 10px 20px;
  border:none;
  cursor:pointer;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight:600;
  transition:.4s;
}

.back{
background:#6b7280;
color:white;
}

.back:hover{
transform:translateY(-3px);
}

.insert{
background:
linear-gradient(
135deg,
#4f46e5,
#6366f1
);
color:white;
}

.insert:hover{
transform:translateY(-3px);
box-shadow:
0 10px 25px rgba(79,70,229,.3);
}

.message{
margin-bottom:20px;
padding:15px;
border-radius:12px;
background:#ecfdf5;
color:#16a34a;
font-weight:600;
}

/* ANIMATION */

@keyframes fadeUp{
from{
opacity:0;
transform:translateY(50px);
}
to{
opacity:1;
transform:translateY(0);
}
}

/* TABLET */

@media(max-width:992px){

.container{
flex-direction:column;
}

.left{
width:100%;
}

.upload-box{
height:350px;
}

.title{
font-size:35px;
}

}

/* MOBILE */

@media(max-width:768px){

.container{
padding:20px;
}

.row{
grid-template-columns:1fr;
}

.upload-box{
height:280px;
}

.title{
font-size:28px;
}

.btn-area{
flex-direction:column;
}

.btn{
width:100%;
}

}

/* SMALL MOBILE */

@media(max-width:480px){

body{
padding:10px;
}

.upload-box{
height:240px;
}

.input-group input,
.input-group select{
padding:14px;
}

}

</style>
</head>

<body>

<div class="container">

<form method="POST"
enctype="multipart/form-data"
style="width:100%; display:flex; gap:40px; flex-wrap:wrap;">

<div class="left">

<label class="upload-box">

<input
type="file"
name="image"
id="imageInput"
hidden
accept="image/*">

<img id="previewImage">

<div class="upload-content" id="uploadContent">
<h2>Select Product Image</h2>
<p>Click to Upload</p>
</div>

</label>

</div>

<div class="right">


<h1 class="title">Add New Product</h1>

<div class="row">

<div class="input-group">
<label>Product Code</label>
<input
type="text"
name="code"
required>
</div>

<div class="input-group">
<label>Price ($)</label>
<input
type="number"
step="0.01"
name="price"
required>
</div>

<div class="input-group">
<label>Stock</label>
<input
type="text"
name="stock"
required>
</div>

</div>

<div class="input-group">
<label>Product Name</label>
<input
type="text"
name="name"
required>
</div>

<div class="input-group">
<label>Product Categories</label>
<input
type="text"
name="product_category"
required>
</div>


<div class="input-group">
<label>Quantity</label>
<input
type="number"
name="qaunlity"
required>
</div>

<div class="input-group">
<label>Discount (%)</label>
<input
type="number"
name="discount"
value="0">
</div>

<div class="input-group">
<label>Description</label>
<input
type="text"
name="description">
</div>

<div class="btn-area">

<button
type="button"
class="btn back"
onclick="history.back()">
Back
</button>

<button
type="submit"
name="add_product"
class="btn insert">
Insert Product
</button>

</div>

</div>

</form>

</div>

<script>

// IMAGE PREVIEW

const imageInput =
document.getElementById('imageInput');

const preview =
document.getElementById('previewImage');

const uploadContent =
document.getElementById('uploadContent');

imageInput.addEventListener(
'change',
function()
{
const file = this.files[0];

if(file)
{
const reader = new FileReader();

reader.onload = function(e)
{
preview.src = e.target.result;
preview.style.display = "block";
uploadContent.style.display = "none";
}

reader.readAsDataURL(file);
}
}
);

// FORM ANIMATION

document.querySelectorAll("input,select")
.forEach(item=>{
item.addEventListener("focus",()=>{
item.parentElement.style.transform=
"translateY(-3px)";
});

item.addEventListener("blur",()=>{
item.parentElement.style.transform=
"translateY(0)";
});
});

</script>

</body>
</html>