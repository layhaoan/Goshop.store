<?php
$conn = mysqli_connect("localhost", "root", "", "store_db");

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$product = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
$row = mysqli_fetch_assoc($product);

if (!$row) {
    die("Product not found!");
}

$message = "";

if (isset($_POST['update'])) {

    $code = mysqli_real_escape_string($conn, $_POST['code']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $product_category = mysqli_real_escape_string($conn, $_POST['product_category']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $qaunlity = intval($_POST['qaunlity']);
    $discount = mysqli_real_escape_string($conn, $_POST['discount']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

   /* IMAGE UPDATE */
    if (!empty($_FILES['image']['name'])) {

        $image_name = time() . '_' . $_FILES['image']['name'];
        $tmp_name   = $_FILES['image']['tmp_name'];

        move_uploaded_file($tmp_name, "uploads/" . $image_name);

        $image = $image_name;
    }

    $update = mysqli_query($conn, "
        UPDATE products SET
            code='$code',
            name='$name',
            price='$price',
            discount='$discount',
            qaunlity='$qaunlity',
            product_category='$product_category',
            description='$description',
            status='$status',
            image='$image'
        WHERE id='$id'
    ");

    if ($update) {
        $message = "Product updated successfully!";
        header("Location: new_products.php");

        $product = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
        $row = mysqli_fetch_assoc($product);
    } else {
        $message = "Update failed!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Product</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    min-height:100vh;
    background:linear-gradient(135deg,#0f172a,#1e293b);
    display:flex;
    justify-content:center;
    align-items:center;
    padding:30px;
}

.container{
    width:100%;
    max-width:950px;
}

.card{
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(18px);
    border:1px solid rgba(255,255,255,0.1);
    border-radius:25px;
    padding:35px;
    animation:fadeIn .5s ease;
    box-shadow:0 15px 40px rgba(0,0,0,.3);
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.header h1{
    color:#fff;
}

.back-btn{
    text-decoration:none;
    color:#fff;
    background:#334155;
    padding:10px 16px;
    border-radius:10px;
}

.preview-box{
    text-align:center;
    margin-bottom:25px;
}

.preview-box img{
    width:180px;
    height:180px;
    object-fit:cover;
    border-radius:20px;
    border:4px solid #3b82f6;
}

.input-group{
    margin-bottom:18px;
}

.input-group label{
    display:block;
    margin-bottom:8px;
    color:#cbd5e1;
}

.input-group input,
.input-group textarea,
.input-group select{
    width:100%;
    padding:14px;
    border:none;
    border-radius:12px;
    background:#1e293b;
    color:white;
    outline:none;
}

textarea{
    resize:none;
    height:120px;
}

.row{
    display:grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap:18px;
}

.save-btn{
    width:100%;
    padding:16px;
    border:none;
    border-radius:12px;
    background:#2563eb;
    color:white;
    font-size:16px;
    cursor:pointer;
    transition:.3s;
}

.save-btn:hover{
    background:#1d4ed8;
    transform:translateY(-2px);
}

.message{
    background:#16a34a;
    color:white;
    padding:12px;
    border-radius:10px;
    margin-bottom:20px;
    text-align:center;
}

.file-input{
    background:#1e293b;
    padding:12px;
    border-radius:12px;
}

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(20px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

@media(max-width:768px){

    .row{
        grid-template-columns:1fr;
    }

    .header{
        flex-direction:column;
        gap:15px;
    }

}

</style>
</head>

<body>

<div class="container">

    <div class="card">

        <div class="header">
            <h1>✏️ Edit Product</h1>
            <a href="new_products.php" class="back-btn">← Back</a>
        </div>

        <?php if(!empty($message)): ?>
            <div class="message">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="preview-box">
                <img
                    src="uploads/<?php echo $row['image']; ?>"
                    id="preview"
                    alt="Product Image">
            </div>

            <div class="input-group">
                <label>Product Image</label>
                <input
                    type="file"
                    name="image"
                    id="imageInput"
                    class="file-input"
                    accept="image/*">
            </div>

            <div class="input-group">
                <label>Product Code</label>
                <input
                    type="text"
                    name="code"
                    value="<?php echo htmlspecialchars($row['code']); ?>"
                    required>
            </div>

            <div class="input-group">
                <label>Product Name</label>
                <input
                    type="text"
                    name="name"
                    value="<?php echo htmlspecialchars($row['name']); ?>"
                    required>
            </div>

            <div class="input-group">
                <label>Qaunlity</label>
                <input
                    type="text"
                    name="qaunlity"
                    value="<?php echo htmlspecialchars($row['qaunlity']); ?>">
            </div>

            <div class="input-group">
                <label>Category</label>
                <input
                    type="text"
                    name="product_category"
                    value="<?php echo htmlspecialchars($row['product_category']); ?>">
            </div>

            <div class="row">

                <div class="input-group">
                    <label>Price ($)</label>
                    <input
                        type="number"
                        step="0.01"
                        name="price"
                        value="<?php echo $row['price']; ?>">
                </div>

                <div class="input-group">
                    <label>Stock</label>
                    <input
                        type="text"
                        name="stock"
                        value="<?php echo $row['stock']; ?>">
                </div>

                <div class="input-group">
                    <label>Discount</label>
                    <input
                        type="number"
                        name="discount"
                        value="<?php echo $row['discount']; ?>">
                </div>

            </div>

            <div class="input-group">
                <label>Description</label>
                <textarea
                    name="description"><?php echo htmlspecialchars($row['description']); ?></textarea>
            </div>

            <button type="submit" name="update" class="save-btn">
                💾 Update Product
            </button>

        </form>

    </div>

</div>

<script>

const imageInput = document.getElementById('imageInput');
const preview = document.getElementById('preview');

imageInput.addEventListener('change', function(){

    const file = this.files[0];

    if(file){

        const reader = new FileReader();

        reader.onload = function(e){
            preview.src = e.target.result;
        }

        reader.readAsDataURL(file);
    }

});

</script>

</body>
</html>