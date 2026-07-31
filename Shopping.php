<?php
session_start();

// DATABASE CONNECTION
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "store_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

// ======================
// GET USER DATA
// ======================

$user = null;

if(isset($_SESSION['user_id'])){

    $user_id = $_SESSION['user_id'];

    $user_sql = "SELECT * FROM users WHERE id='$user_id'";
    $user_result = $conn->query($user_sql);

    if($user_result->num_rows > 0){
        $user = $user_result->fetch_assoc();
    }
}

// ======================
// ADD TO CART
// ======================

if(isset($_POST['add_to_cart'])){

    // CHECK LOGIN
    if(!isset($_SESSION['user_id'])){

        header("Location: login.php");
        exit();
    }

    $product_id     = $_POST['id'];
    $product_name   = $_POST['name'];
    $product_price  = $_POST['price'];
    $product_image  = $_POST['image'];
    $product_status = $_POST['status'];

    $cart_item = [

        'id'     => $product_id,
        'name'   => $product_name,
        'price'  => $product_price,
        'image'  => $product_image,
        'status' => $product_status,
        'qty'    => 1
    ];

    // CREATE CART SESSION
    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = [];
    }

    // CHECK PRODUCT EXIST
    $found = false;

    foreach($_SESSION['cart'] as &$item){

        if($item['id'] == $product_id){

            $item['qty']++;
            $found = true;
            break;
        }
    }

    // image
    $image = $_FILES['image']['name'];

        $tmp_name = $_FILES['image']['tmp_name'];

        move_uploaded_file(
            $tmp_name,
            "uploads/".$image
        );

    // ADD NEW PRODUCT
    if(!$found){
        $_SESSION['cart'][] = $cart_item;
    }

    header("Location: Shopping.php");
    exit();
}

// ======================
// FETCH PRODUCTS
// ======================

$sql = "SELECT * FROM products ORDER BY id DESC";

$product_result = $conn->query($sql);

if(!$product_result){
    die("Product Query Failed : " . $conn->error);
}

?>

<?php

/* =========================================
DATABASE CONNECTION
========================================= */

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "store_db"
);

if($conn->connect_error){
    die("Connection Failed : " . $conn->connect_error);
}

/* =========================================
SEARCH + CATEGORY
========================================= */

$search =
isset($_GET['search'])
? $_GET['search']
: '';

$category =
isset($_GET['category'])
? $_GET['category']
: 'ALL';

/* =========================================
PAGINATION
========================================= */

$limit = 12;

$page =
isset($_GET['page'])
? (int)$_GET['page']
: 1;

if($page < 1){
    $page = 1;
}

$start =
($page - 1) * $limit;

/* =========================================
SQL
========================================= */

$where = "WHERE 1";

if($search != ''){

    $where .= "
    AND name
    LIKE '%$search%'
    ";

}

if($category != 'ALL'){

    $where .= "
    AND product_category
    = '$category'
    ";

}

/* =========================================
TOTAL PRODUCTS
========================================= */

$total_sql = "
SELECT COUNT(*) AS total
FROM products
$where
";

$total_result =
$conn->query($total_sql);

$total_row =
$total_result->fetch_assoc();

$total_products =
$total_row['total'];

$total_pages =
ceil($total_products / $limit);

/* =========================================
GET PRODUCTS
========================================= */

$sql = "
SELECT *
FROM products
$where
ORDER BY id DESC
LIMIT $start, $limit
";

$result =
$conn->query($sql);

/* =========================================
GET CATEGORIES
========================================= */

$category_sql = "
SELECT DISTINCT product_category
FROM products
";

$category_result =
$conn->query($category_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>GoShop</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

 <!-- NAVBAR -->
  <link rel="stylesheet" href="css/index-navbar-style.css">

<style>

/* =========================================
GLOBAL
========================================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    background:#f5f5f5;
}



/* =========================================
HEADER
========================================= */

.header{
    width:100%;
    height: 240px;
    background:#e5e7eb;
    display:flex;
    justify-content:center;
    align-items:center;
}

.header h1{
    font-size:50px;
    margin-top:30px;
    z-index:2;
    animation:fadeDown 1s ease;
}

/* =========================================
SHOP CONTAINER
========================================= */

.shop-container{
    width:95%;
    margin:50px auto;
}

/* =========================================
TOP BAR
========================================= */

.top-bar{
    display: flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap: 20px;
    margin-bottom:40px;
}

/* =========================================
CATEGORY
========================================= */

.categories{
    display:flex;
    gap: 10px;
    flex-wrap:wrap;
}

.categories a{
    text-decoration:none;
    padding: 14px 15px;
    border:1px solid #ddd;
    border-radius:14px;
    background:white;
    color:#555;
    transition:0.3s;
}

.categories a:hover,
.categories a.active{
    background:black;
    color:white;
}

/* =========================================
SEARCH
========================================= */

.search-box{
    max-width:1220px;
    margin:auto;
    position:relative;
}

.search-box input{
    width:100%;
    height:60px;
    border:none;
    outline:none;
    padding:0 70px 0 20px;
    border-radius:15px;
    font-size:16px;
    background:white;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
    transition:.4s;
}

.search-box input:focus{
    transform:translateY(-3px);
    box-shadow:
    0 10px 25px rgba(0,0,0,.15);
}

.search-box i{
    position:absolute;
    right:20px;
    top:50%;
    transform:translateY(-50%);
    font-size:22px;
}

/* =========================================
PRODUCT GRID
========================================= */

.product-grid{
    display:grid;
    grid-template-columns:
    repeat(auto-fit,minmax(250px,1fr));
    gap:25px;
}

/* =========================================
PRODUCT CARD
========================================= */

.product-card{
    background:white;
    border-radius:20px;
    padding:15px;
    border:1px solid #ddd;
    transition:0.4s;
}

.product-card:hover{
    transform:translateY(-8px);
}

.product-image{
    width:100%;
    height:280px;
    overflow:hidden;
    border-radius:16px;
}

.product-image img{
    width:100%;
    height:100%;
    object-fit:cover;
    transition:0.4s;
}

.product-card:hover img{
    transform:scale(1.08);
}

/* =========================================
PRODUCT INFO
========================================= */

.product-info{
    margin-top:18px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.product-info h3{
    font-size: 1rem;
    margin-bottom:8px;
    inline-size: 190px;
}

.product-info p{
    color:#666;
    font-size: 0.95rem;
}

.cart-btn{
    width: 40px;
    height: 40px;
    border:none;
    border-radius:12px;
    background:#555;
    color:white;
    font-size: 0.88rem;
    cursor:pointer;
    transition:0.3s;
}

.cart-btn:hover{
    background:black;
}

/* =========================================
PAGINATION
========================================= */

.pagination{
    margin-top:60px;
    display:flex;
    justify-content:center;
    gap:12px;
}

.pagination a{
    width:45px;
    height:45px;
    border-radius:10px;
    display:flex;
    justify-content:center;
    align-items:center;
    text-decoration:none;
    border:1px solid #ddd;
    background:white;
    color:#555;
    transition:0.3s;
}

.pagination a.active,
.pagination a:hover{
    background:black;
    color:white;
}

/* =========================================
FOOTER
========================================= */

.footer{
    background:#111;
    color:white;
    padding:70px 8%;
    margin-top:80px;
}

.footer-grid{
    display:grid;
    grid-template-columns:
    repeat(auto-fit,minmax(220px,1fr));
    gap:40px;
}

.footer h2{
    margin-bottom:20px;
}

.footer p{
    color:#bbb;
    line-height:1.8;
}

.footer ul{
    list-style:none;
}

.footer ul li{
    margin-bottom:12px;
    color:#bbb;
}

.social{
    margin-top:20px;
    display:flex;
    gap:15px;
}

.social i{
    width:40px;
    height:40px;
    border-radius:50%;
    background:#222;
    display:flex;
    justify-content:center;
    align-items:center;
    cursor:pointer;
}

.copy{
    text-align:center;
    margin-top:60px;
    color:#999;
}

/* ================= RESPONSIVE ================= */

@media(max-width:1200px){

    .nav-links{
        gap:20px;
    }

    .hero-content h1{
        font-size: 2.3rem;
    }

    .hero-content p{
        inline-size: 400;
    }
  
    .nav-right{
        position: relative;
        right: 50px;
    }

    .menu-btn{
        position: relative;
        left: 50px;
    }

}

@media(max-width:124px){
    .nav-right{
        position: relative;
        right: 10px;
    }
}

@media(max-width:992px){

  .navbar{
    padding:0 4%;
  }

  .nav-links{
    position:fixed;
    top:90px;
    right:-100%;
    width:300px;
    height:calc(100vh - 90px);
    background:#fff;
    flex-direction:column;
    align-items:flex-start;
    padding:40px;
    transition:.5s;
    box-shadow:-5px 0 10px rgba(0,0,0,0.08);
  }

  .nav-links.active{
    right:0;
  }

  .menu-btn{
    display:block;
  }

  .profile-text{
    display:none;
  }

  .hero{
    text-align:center;
    justify-content:center;
  }

  .hero-content{
    max-width:100%;
  }

  .hero-btns{
    justify-content:center;
  }

}

@media(max-width:768px){

  .hero-content h1{
    font-size:2.5rem;
  }

  .hero-content p{
    font-size:1rem;
  }

  .profile img{
    width:50px;
    height:50px;
  }

  .logo{
    font-size:1.6rem;
  }

.slider-btn{
    display: none;
}
  

}

@media(max-width:576px){

  .navbar{
    height:80px;
  }

  .nav-links{
    top:80px;
    width:100%;
  }

  .hero{
    padding:120px 6%;
  }

  .hero-content h1{
    font-size:2rem;
  }

  .btn{
    width:100%;
  }

  

  .prev{
    left:10px;
  }

  .next{
    right:10px;
  }

  .dots{
    bottom:20px;
  }

}

</style>

</head>
<body>

<!-- ================= NAVBAR ================= -->
    <nav class="navbar">

        <div class="logo">
        Go<span>Shop</span>
        </div>

        <ul class="nav-links" id="navLinks">
            <li><a href="Homepage.php">Homepage</a></li>
            <li><a href="Shopping.php">Shopping</a></li>
            <li><a href="about_us.php">About Us</a></li>
            <li><a href="contact_us.php">Contact Us</a></li>
        </ul>

        <div class="nav-right">

        <a href="view_cart.php">
            <div class="cart">
                <i class="fa-solid fa-cart-shopping"></i>
                <span><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
            </div>
        </a>

        <div class="profile">
            <a href="profile.php"><img src="uploads/<?php echo $user['profile_image']; ?>"></a>
            <div class="profile-text">
            <h4><?php echo $user['fullname']; ?></h4>
            <p> <?php echo $user['email']; ?> </p>
            </div>
        </div>

        <div class="menu-btn" id="menuBtn">
            <i class="fa-solid fa-bars"></i>
        </div>

        </div>

  </nav>

<!-- =========================================
HEADER
========================================= -->

<div class="header">

    <h1>Shopping</h1>

</div>

<!-- =========================================
SHOP
========================================= -->
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search Products...">
        <i class="fas fa-search"></i>
    </div>
<div class="shop-container">

    <!-- TOP BAR -->

    <div class="top-bar">
    

        <!-- CATEGORIES -->

        <div class="categories">

            <a
            href="?category=ALL"
            class="<?php if($category=='ALL') echo 'active'; ?>">
            ALL
            </a>

            <?php while($cat = $category_result->fetch_assoc()){ ?>

                <a
                href="?category=<?php echo $cat['product_category']; ?>"
                class="<?php
                if($category==$cat['product_category'])
                echo 'active';
                ?>">

                <?php echo $cat['product_category']; ?>

                </a>

            <?php } ?>

        </div>

        

    </div>

    <!-- PRODUCT GRID -->

    <div class="product-grid">

<?php

if($result->num_rows > 0){

while($row = $result->fetch_assoc()){

?>

        <div class="product-card">

            <div class="product-image">

                <a href="products_details.php?id=<?php echo $row['id']; ?>"><img src="uploads/<?php echo $row['image']; ?>" alt="" ></a>
            </div>

            <div class="product-info">

                <div>
                    <h3 class="product-title"> <?php echo $row['name']; ?> </h3>
                    <p> $<?php echo $row['price']; ?> </p>
                </div>

                 <form method="post">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="name" value="<?php echo $row['name']; ?>">
                    <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                    <input type="hidden" name="image" value="<?php echo $row['image']; ?>">
                    <input type="hidden"  name="status" value="<?php echo $row['status']; ?>">
                    <div class="btn_add">
                        <button type="submit" name="add_to_cart" class="cart-btn"><i class="fa-solid fa-cart-shopping"></i></button>
                        
                    </div>

                </form>


            </div>

        </div>

<?php

}

}else{

    echo "
    <h2>No Products Found</h2>
    ";

}

?>

    </div>

    <!-- PAGINATION -->

<?php if($total_products > $limit){ ?>

    <div class="pagination">

<?php for($i=1; $i<=$total_pages; $i++){ ?>

        <a
        href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&category=<?php echo $category; ?>"
        class="<?php if($page==$i) echo 'active'; ?>">

        <?php echo $i; ?>

        </a>

<?php } ?>

    </div>

<?php } ?>

</div>

<!-- =========================================
FOOTER
========================================= -->

<div class="footer">

    <div class="footer-grid">

        <div>

            <h2>GOSHOP</h2>

            <p>

                Professional ecommerce website
                for digital product selling.

            </p>

            <div class="social">

                <i class="fa-brands fa-facebook-f"></i>
                <i class="fa-brands fa-instagram"></i>
                <i class="fa-brands fa-x-twitter"></i>
                <i class="fa-brands fa-youtube"></i>

            </div>

        </div>

        <div>

            <h2>Use Every</h2>

            <ul>

                <li>UI Design</li>
                <li>UX Design</li>
                <li>Programming</li>
                <li>Development</li>

            </ul>

        </div>

        <div>

            <h2>Explore</h2>

            <ul>

                <li>Design</li>
                <li>Development</li>
                <li>Design System</li>
                <li>Figma</li>

            </ul>

        </div>

        <div>

            <h2>Resources</h2>

            <ul>

                <li>Blog</li>
                <li>Support</li>
                <li>Contact</li>
                <li>Developer</li>

            </ul>

        </div>

    </div>

    <div class="copy">

        © 2026 GOSHOP. All Rights Reserved.

    </div>

</div>
<script>

const searchInput =
document.getElementById(
'searchInput'
);

const cards =
document.querySelectorAll(
'.product-card'
);

searchInput.addEventListener(
'keyup',
function(){

let value =
this.value.toLowerCase();

cards.forEach(card=>{

let title =
card.querySelector(
'.product-title'
)
.innerText
.toLowerCase();

if(
title.includes(value)
){
card.style.display='block';
}
else{
card.style.display='none';
}

});

updateCount();

}
);

document
.querySelectorAll(
'.category-btn'
)
.forEach(btn=>{

btn.addEventListener(
'click',
function(){

document
.querySelectorAll(
'.category-btn'
)
.forEach(b=>
b.classList.remove(
'active'
)
);

this.classList.add(
'active'
);

let category =
this.dataset.category;

cards.forEach(card=>{

let productCat =
card.dataset.category;

if(
category==='all'
||
productCat===category
){
card.style.display='block';
}
else{
card.style.display='none';
}

});

updateCount();

}
);

});

function updateCount(){

let count = 0;

cards.forEach(card=>{

if(
card.style.display!='none'
){
count++;
}

});

document
.getElementById(
'productCount'
)
.innerHTML =
`Showing ${count} Products`;

}

</script>

 <script>

    // MOBILE MENU

    const menuBtn = document.getElementById('menuBtn');
    const navLinks = document.getElementById('navLinks');

    menuBtn.onclick = () => {
      navLinks.classList.toggle('active');
    };

    // HERO SLIDER

    const hero = document.getElementById('hero');

    const slides = [

      {
        image:
        "https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=1400&auto=format&fit=crop"
      },

      {
        image:
        "uploads/taru-goyal-bXWiwLQjQu0-unsplash.jpg"
      },

      {
        image:
        "https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1400&auto=format&fit=crop"
      }


    ];

    let current = 0;

    const dots = document.querySelectorAll('.dot');
    const next = document.querySelector('.next');
    const prev = document.querySelector('.prev');

    function changeSlide(index){

      hero.style.background =
      `
      linear-gradient(rgba(0,0,0,0.45),
      rgba(0,0,0,0.45)),
      url('${slides[index].image}')
      `;

      hero.style.backgroundSize = "cover";
      hero.style.backgroundPosition = "center";

      dots.forEach(dot => dot.classList.remove('active'));
      dots[index].classList.add('active');

    }

    next.onclick = () => {

      current++;

      if(current >= slides.length){
        current = 0;
      }

      changeSlide(current);

    };

    prev.onclick = () => {

      current--;

      if(current < 0){
        current = slides.length - 1;
      }

      changeSlide(current);

    };

    // AUTO SLIDE

    setInterval(() => {

      current++;

      if(current >= slides.length){
        current = 0;
      }

      changeSlide(current);

    }, 5000);

  </script>



</body>
</html>