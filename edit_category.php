<?php
$conn = new mysqli("localhost", "root", "", "store_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$result = $conn->query("SELECT * FROM categories WHERE id=$id");
if (!$result || $result->num_rows == 0) die("Category not found.");
$category = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $types = $conn->real_escape_string($_POST['types']);
    $status = $conn->real_escape_string($_POST['status']);
    $qaunlity = $conn->real_escape_string($_POST['qaunlity']);
    $price = $conn->real_escape_string($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);
    $image = $category['image']; // default old image

    if (!empty($_FILES['image']['name'])) {
        $types = $_FILES['types']['types'];
        $status = $_FILES['status']['status'];
        $qaunlity = $_FILES['qaunlity']['qaunlity'];
        $price = $_FILES['price']['price'];
        $image = $_FILES['image']['name'];
        $description = $_FILES['description']['description'];
        $target = "uploads/" . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    $conn->query("UPDATE categories SET name='$name', types='$types', qaunlity='$qaunlity', status='$status',price='$price', description='$description', image='$image' WHERE id=$id");
    header("Location: categories_list.php");
    exit;
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
     <style>
         *{
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }
        body{
            position: relative;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: sans-serif;
        }
        .container{
            position: absolute;
            transform: translate(-50%,-50%);
            top: 50%;
            left: 50%;
            width: 800px;
            height: 530px;
            background: #fff;
            box-shadow: 0 5px 8px 0 rgba(0,0,0,0.2);
            border: 1px solid #555;
        }
        h2{
            text-align: center;
            justify-content: center;
            margin-top: 34px;
            font-size: 1.4rem;
        }
        form{
            margin-left: 40px;
            margin-top: 40px;
        }
        input{
            height: 32px;
        }
        input, textarea{
            width: 40%;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 0.7rem;
            border: 1px solid #989898;
            outline: none;
            position: relative;
            top: 5px;
            
        }
        textarea{
            width: 40%;
            height: 100px;
            padding: 10px 10px;
            border-radius: 8px;
            font-size: 0.7rem;
            border: 1px solid #989898;
            font-family: sans-serif;
            outline: none;
            position: relative;
            top: 10px;
        }
        .image{
            width: none;
            padding: none;
            border-radius:none;
            box-shadow: none;
            position: relative;
            top: -15px;
        }
        button{
            width: 90px;
            height: 30px;
            background: #0000ff;
            color: #fff;
            font-size: 0.9rem;
            border: 1px solid #989898;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-cancel{
            background: none;
            border: 1px solid #555;
        }
        .btn-cancel a{
            text-decoration: none;
            color: #555;
        }
        #preview {
            margin-top: -25px;
            width: 380px;
            height: 280px;
            border: 2px solid #ccc;
            padding: 5px;
            display: none;
            object-fit: cover;
            position: absolute;
        }
        .showImage{
            position: absolute;
            display: flex;
            flex-direction: column;
            right: 220px;
            top: 125px;
        }
        .showImage input{
            width: 200px;
        }
        .chooseFileimage{
            margin-top: 260px;
        }
        hr{
            position: relative;
            bottom: 6px;
            width: 95%;
        }
        .input-group {
            position: relative;
            width: 40%;
            margin: 15px;
            left: -13px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            outline: none;
            font-size: 0.85rem;
            background: transparent;
            color: #444;
            padding-left: 16px;
        }

        .input-group label {
            position: absolute;
            left: 10px;
            top: 12px;
            color: #6c6c6c;
            font-size: 0.92rem;
            pointer-events: none;
            transition: 0.3s ease;
            background: none;
            padding: 0 5px;
        }

        /* Animation */
        .input-group input:focus + label,
        .input-group input:valid + label {
            top: -5px;
            font-size: 0.7rem;
            color: #35383b;
            background: #fff;
        }

        .input-group input:focus {
            border-color: #007bff;
        }
    </style>
</head>
<body>

<div class="container">
          <h2>Edit Category</h2>
            <form method="POST" enctype="multipart/form-data">
                
                <div class="input-group">
                    <input type="text" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                    <label>Username</label>
                </div>
                <div class="input-group">
                    <input type="text" name="price" value="<?php echo htmlspecialchars($category['price']); ?>" required>
                    <label>Price</label>
                </div>
                <div class="input-group">
                    <input type="text" name="types" value="<?php echo htmlspecialchars($category['types']); ?>" required>
                    <label>Types</label>
                </div>
                <div class="input-group">
                    <input type="text" name="status" value="<?php echo htmlspecialchars($category['status']); ?>" required>
                    <label>Status</label>
                </div>
                <div class="input-group">
                    <input type="text" name="qaunlity" value="<?php echo htmlspecialchars($category['qaunlity']); ?>" required>
                    <label>Qaunlity</label>
                </div>
                <textarea name="description"><?php echo htmlspecialchars($category['description']); ?></textarea><br><br>
                <hr>
                <div class="showImage">
                    <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                    <!-- 🔥 Image Preview -->
                    <img 
                        id="preview"
                        src="uploads/<?php echo htmlspecialchars($category['image']); ?>" 
                        style="display:block;"
                    >
                    <!-- File Input -->
                    <input type="file" name="image" id="imageInput" class="chooseFileimage">
                </div>
                
                <button type="submit" name="update">Update</button>
                <button type="button" class="btn-cancel"><a href="categories_list.php">Cancel</a></button>
            </form>
    </div>


           
        <script>
            document.getElementById("imageInput").addEventListener("change", function(e) {
                const file = e.target.files[0];

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(event) {
                        document.getElementById("preview").src = event.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        </script>
</body>
</html>

