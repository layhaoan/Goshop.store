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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>About Us</title>
    <link rel="stylesheet" href="css/e-about_us.css">
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

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
       <li><a href="shopping.php">Shopping</a></li>
      <li><a class="active" href="about_us.php">About Us</a></li>
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
          <h4><?php echo isset($user['fullname']) ? $user['fullname'] : ''; ?></h4>
          <p><?php echo isset($user['email']) ? $user['email'] : ''; ?></p>
        </div>
      </div>

      <div class="menu-btn" id="menuBtn">
        <i class="fa-solid fa-bars"></i>
      </div>

    </div>

  </nav>



  <!-- HERO -->

  <section class="hero">

    <div class="hero-text">

      <h1>
        About <span>Our Store</span>
      </h1>

      <p>
        We create modern shopping experiences with high-quality products,
        premium designs, and customer-first services. Our goal is to make
        online shopping simple, beautiful, and enjoyable for everyone.
      </p>

      <a href="#" class="hero-btn">Explore More</a>

    </div>

    <div class="hero-image">

      <img src="https://images.unsplash.com/photo-1522199710521-72d69614c702?q=80&w=1200&auto=format&fit=crop" alt="">

    </div>

  </section>

  <!-- ABOUT -->

  <section class="about">

    <h2 class="section-title">Why Choose Us</h2>

    <div class="about-grid">

      <div class="about-card">
        <i class="fa-solid fa-truck-fast"></i>
        <h3>Fast Delivery</h3>
        <p>
          Quick and secure shipping for all your favorite products worldwide.
        </p>
      </div>

      <div class="about-card">
        <i class="fa-solid fa-shield-heart"></i>
        <h3>Trusted Quality</h3>
        <p>
          We provide premium quality products with guaranteed satisfaction.
        </p>
      </div>

      <div class="about-card">
        <i class="fa-solid fa-headset"></i>
        <h3>24/7 Support</h3>
        <p>
          Friendly customer support ready to help anytime you need assistance.
        </p>
      </div>

    </div>

  </section>

  <!-- TEAM -->

  <section class="team">

    <h2 class="section-title">Our Team</h2>

    <div class="team-grid">

      <div class="team-card">

        <div class="team-image">
          <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=1200&auto=format&fit=crop" alt="">
        </div>

        <div class="team-content">
          <h3>John Carter</h3>
          <span>CEO Founder</span>

          <div class="socials">
            <i class="fa-brands fa-facebook-f"></i>
            <i class="fa-brands fa-instagram"></i>
            <i class="fa-brands fa-twitter"></i>
          </div>
        </div>

      </div>

      <div class="team-card">

        <div class="team-image">
          <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=1200&auto=format&fit=crop" alt="">
        </div>

        <div class="team-content">
          <h3>Emily Watson</h3>
          <span>Marketing Manager</span>

          <div class="socials">
            <i class="fa-brands fa-facebook-f"></i>
            <i class="fa-brands fa-instagram"></i>
            <i class="fa-brands fa-twitter"></i>
          </div>
        </div>

      </div>

      <div class="team-card">

        <div class="team-image">
          <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=1200&auto=format&fit=crop" alt="">
        </div>

        <div class="team-content">
          <h3>Michael Lee</h3>
          <span>Developer</span>

          <div class="socials">
            <i class="fa-brands fa-facebook-f"></i>
            <i class="fa-brands fa-instagram"></i>
            <i class="fa-brands fa-twitter"></i>
          </div>
        </div>

      </div>

    </div>

  </section>

  <!-- COUNTER -->

  <section class="counter-section">

    <div class="counter-grid">

      <div class="counter-box">
        <h2 class="counter" data-target="500">0</h2>
        <p>Happy Customers</p>
      </div>

      <div class="counter-box">
        <h2 class="counter" data-target="120">0</h2>
        <p>Products</p>
      </div>

      <div class="counter-box">
        <h2 class="counter" data-target="10">0</h2>
        <p>Years Experience</p>
      </div>

      <div class="counter-box">
        <h2 class="counter" data-target="24">0</h2>
        <p>Support Hours</p>
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
  <script src="javascript/toggle_dropdown.js"></script>
  <script>

    // SCROLL ANIMATION

    const cards = document.querySelectorAll('.about-card, .team-card');

    window.addEventListener('scroll', () => {

      cards.forEach(card => {

        const cardTop = card.getBoundingClientRect().top;

        if(cardTop < window.innerHeight - 100){
          card.classList.add('show');
        }

      });

    });

    // COUNTER ANIMATION

    const counters = document.querySelectorAll('.counter');

    counters.forEach(counter => {

      counter.innerText = '0';

      const updateCounter = () => {

        const target = +counter.getAttribute('data-target');
        const current = +counter.innerText;

        const increment = target / 100;

        if(current < target){

          counter.innerText = `${Math.ceil(current + increment)}`;

          setTimeout(updateCounter, 20);

        }else{

          counter.innerText = target;

        }

      };

      updateCounter();

    });

  </script> 

  
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