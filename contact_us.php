<!-- save as: profile.php -->

<?php
    session_start();
    include "db.php";

    if(!isset($_SESSION['user_id'])){
        header("Location: login.php");
    }

    $id = $_SESSION['user_id'];

    $result = $conn->query("SELECT * FROM users WHERE id='$id'");
    $user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact Page</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
<link rel="stylesheet" href="css/contact_us.css">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" /> 
  
 <!-- NAVBAR -->
  <link rel="stylesheet" href="css/index-navbar-style.css">
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
      <li><a class="active" href="contact_us.php">Contact Us</a></li>
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
        <i style="color: #000;" class="fa-solid fa-bars"></i>
      </div>

    </div>

  </nav>


<!-- CONTACT -->
<section id="contact">
  <div class="contact-container">

    <!-- LEFT -->
    <div class="contact-info">
      <h1>Contact Us</h1>
      <p>We are ready to help you with web design, ecommerce, and IT solutions.</p>

      <div class="info-box">
        <i class="fa fa-envelope"></i>
        support@gmail.com
      </div>

      <div class="info-box">
        <i class="fa fa-phone"></i>
        +855 12 345 678
      </div>

      <div class="info-box">
        <i class="fa fa-location-dot"></i>
        Phnom Penh, Cambodia
      </div>

      <div class="socials">
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="https://t.me/yourtelegramusername"><i class="fab fa-telegram"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="contact-form">
      <h2>Send Message</h2>

      <form id="form">
        <div class="input-group">
          <input type="text" placeholder="Name" required>
        </div>

        <div class="input-group">
          <input type="email" placeholder="Email" required>
        </div>

        <div class="input-group">
          <input type="text" placeholder="Subject" required>
        </div>

        <div class="input-group">
          <textarea placeholder="Message" required></textarea>
        </div>

        <button class="btn">Send Message</button>
      </form>

      <div class="telegram">
        <a href="https://t.me/yourtelegramusername" target="_blank">
          <i class="fab fa-telegram"></i>
          Chat on Telegram
        </a>
      </div>
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

<script>
document.getElementById("form").addEventListener("submit", function(e){
  e.preventDefault();
  alert("Message sent successfully!");
  this.reset();
});
</script>
<script src="javascript/toggle_dropdown.js"></script>

<!-- RESPONSIVE NARBAR -->
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