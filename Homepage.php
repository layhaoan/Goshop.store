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

    header("Location: Homepage.php");
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


$banner =
mysqli_fetch_assoc(
mysqli_query(
$conn,
"
SELECT *
FROM banners
WHERE is_active=1
LIMIT 1
"
));



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>GOSHOP</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" /> 
  <!-- FONT AWESOME -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
  <!-- GOOGLE FONT -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/index-homepage-style-re.css">

  
</head>
<body>

<!-- ================= NAVBAR ================= -->

  <nav class="navbar">

    <div class="logo">
      Go<span>Shop</span>
    </div>

    <ul class="nav-links" id="navLinks">
      <li><a class="active" href="index.php">Homepage</a></li>
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
          <h4>  <?php echo $user['fullname']; ?></h4>
          <p> <?php echo $user['email']; ?> </p>
        </div>
      </div>

      <div class="menu-btn" id="menuBtn">
        <i class="fa-solid fa-bars"></i>
      </div>

    </div>

  </nav>


  <!-- HERO -->

  <section class="hero" id="hero">
    <img src="<?= $banner['image']; ?>" alt="Banner">
    <div class="hero-content">
      <h1> <?= htmlspecialchars($banner['title']); ?> </h1>
      <p> <?= htmlspecialchars($banner['subtitle']); ?> </p>  
      <a href="register.php"><button class="btn btn-primary">Register</button></a>
      <?php if($banner['show_register_button']==1): ?>

    <a href="<?= htmlspecialchars($banner['button_link']); ?>"
      class="register-btn">

        <?= htmlspecialchars($banner['button_text']); ?>

    </a>

    <?php endif; ?><?php if($banner['show_register_button']==1): ?>

    <a href="<?= htmlspecialchars($banner['button_link']); ?>"
      class="register-btn">

        <?= htmlspecialchars($banner['button_text']); ?>

    </a>

    <?php endif; ?>
    </div>
  </section>

  <!-- STATS -->

  <div class="stats reveal">

    <div class="stat-box">
      <h2>1200+</h2>
      <p>All Products</p>
    </div>

    <div class="stat-box">
      <h2>100+</h2>
      <p>Followers</p>
    </div>

    <div class="stat-box">
      <h2>120</h2>
      <p>Shop</p>
    </div>

    <div class="stat-box">
      <h2>100</h2>
      <p>Total Customer Orderd</p>
    </div>

  </div>

  <!-- CATEGORIES -->

  <section>

    <div class="section-title reveal">
      <h2>Our Categories</h2>
    </div>

    <div class="category-grid">

      <div class="category-card reveal">
        <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=1974&auto=format&fit=crop">
        <h3>Brand Headphone</h3>
      </div>

      <div class="category-card reveal">
        <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=2070&auto=format&fit=crop">
        <h3>Brand Shoes</h3>
      </div>

      <div class="category-card reveal">
        <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1999&auto=format&fit=crop">
        <h3>Smart Watch</h3>
      </div>

    </div>

  </section>

  <!-- BANNER -->

  <section>

    <div class="banner reveal">

      <div class="banner-content">
        <h2>DIGITAL SERVICE AND ONLINE SELLING BRAND PRODUCTS</h2>

        <p>
          This Ecommerce website create for selling all brand Smart-watch,
          Airport, Shoes and new accessory.
        </p>
      </div>

    </div>

  </section>

  <!-- PRODUCTS -->

  <section>

    <div class="section-title reveal">
      <h2>Our Products</h2>
    </div>

    <div class="product-grid">
      <!-- products Data -->

      
       <?php while($product = $product_result->fetch_assoc()){ ?>

          <div class="product-card reveal">
          <a href="products_details.php?id=<?php echo $product['id']; ?>"><img src="uploads/<?php echo $product['image']; ?>"></a>
          <div class="product-info">
              <h4><?php echo $product['name']; ?></h4>

              <div class="price-cart">
              <span> $<?php echo $product['price']; ?> </span>
              <div class="status"><?php echo $product['status']; ?> </div>
              <form method="post">
                  <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                  <input type="hidden" name="name" value="<?php echo $product['name']; ?>">
                  <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                  <input type="hidden" name="image" value="<?php echo $product['image']; ?>">
                  <input type="hidden"  name="status" value="<?php echo $product['status']; ?>">
                  <div class="btn_add">
                      <button type="submit" name="add_to_cart" class="add_to_cart"><i class="fa-solid fa-cart-shopping"></i></button>
                  </div>

              </form>
              </div>
          </div>
          </div>  

      <?php } ?>

      <!-- PRODUCT -->
    </div>

  </section>

  <!-- PROMO -->

  <section>

    <div class="promo reveal">

      <div class="promo-list">

        <div class="promo-item">
          <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1999&auto=format&fit=crop">
          <div>
            <h4>HUAWEI WATCH FIT 3</h4>
            <p>$95.00</p>
          </div>
        </div>

        <div class="promo-item">
          <img src="https://images.unsplash.com/photo-1583394838336-acd977736f90?q=80&w=1974&auto=format&fit=crop">
          <div>
            <h4>Apple AirPods 4</h4>
            <p>$125.00</p>
          </div>
        </div>

        <div class="promo-item">
          <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=2070&auto=format&fit=crop">
          <div>
            <h4>Nike Airforce</h4>
            <p>$95.00</p>
          </div>
        </div>

      </div>

      <div class="promo-right">
        <h2>Enjoy with our products and Pre-order more</h2>

        <p>
          This Ecommerce website create for selling all brand Smart-watch,
          Airport, Shoes and new accessory.
        </p>

        <br>

        <button class="btn btn-primary">
          Learn More
        </button>

      </div>

    </div>

  </section>

  <!-- CUSTOMER -->

  <section>

    <div class="section-title reveal">
      <h2>Top Customer</h2>
    </div>

    <div class="customer-grid">

      <div class="customer-card reveal">
        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=1974&auto=format&fit=crop">
        <h4>Brian Fauzan</h4>
      </div>

      <div class="customer-card reveal">
        <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=1974&auto=format&fit=crop">
        <h4>Zaigam Akhtar</h4>
      </div>

      <div class="customer-card reveal">
        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=1974&auto=format&fit=crop">
        <h4>Mike Morgado</h4>
      </div>

    </div>

  </section>

  <!-- BRANDS -->

  <section>

    <div class="section-title reveal">
      <h2>Products Logos Brand Seller</h2>
    </div>

    <div class="brand-grid">

      <div class="brand-box reveal">
        <img src="Logo/Adidas - Logo.jpg" alt="">
      </div>

      <div class="brand-box reveal">
        <img src="Logo/download.jpg" alt="">
      </div>

      <div class="brand-box reveal">
        <img src="Logo/Huawei Hd Transparent, Huawei Trademarks, Huawei, Phone, Chinese Goods PNG Image For Free Download.jpg" alt="">
      </div>

      <div class="brand-box reveal">
        <img src="Logo/Nike.jpg" alt="">
      </div>

      <div class="brand-box reveal">
        <img src="Logo/Rolex Logo_ The Complete History - Millenary Watches.jpg" alt="">
      </div>
      
      <div class="brand-box reveal">
        <img src="Logo/Xiaomi Logo PNG Vector (EPS) Free Download.jpg" alt="">
      </div>

    </div>

  </section>

  <!-- FOOTER -->
  <footer>

    <div class="footer-grid">

      <div class="footer-col">

        <h3>GOSHOP</h3>

        <p>
          Professional ecommerce website for digital product selling.
        </p>

        <div class="socials">
          <i class="fa-brands fa-facebook-f"></i>
          <i class="fa-brands fa-instagram"></i>
          <i class="fa-brands fa-x-twitter"></i>
          <i class="fa-brands fa-youtube"></i>
        </div>

      </div>

      <div class="footer-col">
        <h3>Use every</h3>

        <a href="#">UI Design</a>
        <a href="#">UX Design</a>
        <a href="#">Programming</a>
        <a href="#">Development</a>
      </div>

      <div class="footer-col">
        <h3>Explore</h3>

        <a href="#">Design</a>
        <a href="#">Development</a>
        <a href="#">Design System</a>
        <a href="#">Figma</a>
      </div>

      <div class="footer-col">
        <h3>Resources</h3>

        <a href="#">Blog</a>
        <a href="#">Support</a>
        <a href="#">Contact</a>
        <a href="#">Developer</a>
      </div>

    </div>

    <div class="footer-bottom">
      © 2026 GOSHOP. All Rights Reserved.
    </div>

  </footer>

  <!-- JAVASCRIPT -->

  <script src="javascript/stick-narbar&Reval-animation_index_page.js"></script>
  <script src="javascript/toggle_dropdown_index_page.js"></script>
  <script>

    // MOBILE MENU

    const menuBtn = document.getElementById('menuBtn');
    const navLinks = document.getElementById('navLinks');

    menuBtn.onclick = () => {
      navLinks.classList.toggle('active');
    };

  </script>


</body>
</html>