<?php
include 'db.php';

/* PAGINATION */

$limit = 12;

$page = isset($_GET['page'])
? (int)$_GET['page']
: 1;

$offset = ($page - 1) * $limit;

/* COUNT PRODUCTS */

$count_query = mysqli_query(
$conn,
"SELECT COUNT(*) as total FROM products"
);

$count_data = mysqli_fetch_assoc($count_query);

$total_products = $count_data['total'];

$total_pages = ceil(
$total_products / $limit
);

/* GET CATEGORIES */

$category_query = mysqli_query(
    $conn,
    "SELECT DISTINCT product_category FROM products"
);

/* GET PRODUCTS */

$product_query = mysqli_query(
$conn,
"SELECT *
FROM products
ORDER BY id DESC
LIMIT $offset,$limit"
);
?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>GoShop Shopping</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Poppins,sans-serif;
}

body{
background:#f4f5f7;
}

/* NAVBAR */

.navbar{
display:flex;
justify-content:space-between;
align-items:center;
padding:20px 8%;
background:#fff;
position:sticky;
top:0;
z-index:999;
box-shadow:0 3px 15px rgba(0,0,0,.05);
}

.logo{
font-size:30px;
font-weight:700;
}

.logo span{
color:red;
}

.nav-links{
display:flex;
gap:25px;
list-style:none;
}

.nav-links a{
text-decoration:none;
color:#444;
font-weight:500;
}

.cart{
font-size:24px;
}

/* HERO */

.hero{
height: 220px;
background:#e8ecef;
display:flex;
justify-content:center;
align-items:center;
flex-direction:column;
}

.hero h1{
font-size:50px;
}

.hero p{
margin-top:10px;
color:#666;
}

.container{
width: 100%;
margin:auto;
position: relative;
top: 30px;

}

/* SEARCH */

.search-section{
    padding: 40px 20px;
    position: relative;
}

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


/* CATEGORY */

.categories{
    max-width:1400px;
    margin:auto;
    padding:0 20px 20px;
    display:flex;
    gap:12px;
    overflow-x:auto;
}

.categories::-webkit-scrollbar{
    display:none;
}

.category-btn{
    padding:12px 25px;
    border:none;
    background:white;
    border-radius:12px;
    cursor:pointer;
    transition:.3s;
    white-space:nowrap;
}

.category-btn:hover{
    transform:translateY(-2px);
}

.category-btn.active{
    background:#111;
    color:white;
}

/* COUNTER */

.counter{
    max-width: 1400px;
    margin:auto;
    padding:  20px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.counter h2{
    font-size: 0.92rem;
    padding-left: 10px;
}


/* PRODUCTS */

.product-grid{
    max-width:1400px;
    margin:auto;
    padding:20px;
    display:grid;
    grid-template-columns:
    repeat(auto-fill,minmax(260px,1fr));
    gap:25px;
}

.product-card{
    background:white;
    border-radius:20px;
    overflow:hidden;
    box-shadow:
    0 5px 20px rgba(0,0,0,.08);
    transition:.4s;
}

.product-card:hover{
    transform:translateY(-10px);
}

.product-image{
    height:260px;
    overflow:hidden;
}

.product-image img{
    width:100%;
    height:100%;
    object-fit:cover;
    transition:.5s;
}

.product-card:hover img{
    transform:scale(1.08);
}

.product-info{
    padding:15px;
}

.product-title{
    font-size:20px;
    font-weight:600;
    margin-bottom:10px;
}

.price{
    font-size:24px;
    font-weight:700;
}

.bottom{
    margin-top:15px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.cart-btn{
    width:50px;
    height:50px;
    border:none;
    background:#111;
    color:white;
    border-radius:12px;
    cursor:pointer;
    transition:.3s;
}

.cart-btn:hover{
    transform:scale(1.1);
}

/* PAGINATION */

.pagination{
    padding:40px;
    display:flex;
    justify-content:center;
    gap:10px;
}

.pagination a{
    width:45px;
    height:45px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:white;
    text-decoration:none;
    border-radius:10px;
    color:black;
}

.pagination a.active{
    background:#111;
    color:white;
}

/* FOOTER */

footer{
background:#111;
color:#fff;
padding:70px 8% 30px;
}

.footer-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:40px;
}

.footer-col h3{
margin-bottom:20px;
}

.footer-col a{
display:block;
margin-bottom:10px;
color:#bbb;
transition:.3s;
}

.footer-col a:hover{
color:#fff;
padding-left:5px;
}

.socials{
display:flex;
gap:15px;
margin-top:20px;
}

.socials i{
width:40px;
height:40px;
border-radius:50%;
background:#222;
display:flex;
align-items:center;
justify-content:center;
transition:.4s;
cursor:pointer;
}

.socials i:hover{
background:#fff;
color:#111;
transform:translateY(-5px);
}

.footer-bottom{
text-align:center;
margin-top:50px;
color:#888;
font-size:14px;
}

/* RESPONSIVE */

@media(max-width:992px){

.product-grid{
    grid-template-columns:
    repeat(3,1fr);
    }

}

@media(max-width:768px){

    .product-grid{
        grid-template-columns:
        repeat(2,1fr);
    }

    .counter{
        flex-direction:column;
        gap:10px;
    }

    .page-header h1{
        font-size:30px;
    }

}

@media(max-width:576px){

    .product-grid{
        grid-template-columns:1fr;
    }

    .product-image{
        height:220px;
    }

    .search-box input{
        height:55px;
    }

}

/* MOBILE */

@media(max-width:768px){

.nav-links{
display:none;
}

.hero h1{
font-size:35px;
}

.products{
grid-template-columns:repeat(2,1fr);
}

}

@media(max-width:500px){

.products{
grid-template-columns:1fr;
}

}

</style>

</head>

<body>

<!-- NAVBAR -->

<nav class="navbar">

    <div class="logo">
    Go<span>Shop</span>
    </div>

    <ul class="nav-links">
    <li><a href="#">Homepage</a></li>
    <li><a href="#">Dashboard</a></li>
    <li><a href="#">Categories</a></li>
    <li><a href="#">Featured</a></li>
    <li><a href="#">Contact</a></li>
    </ul>

    <a href="view_cart.php">
        <div class="cart">
        <i class="fa-solid fa-cart-shopping"></i>
        <span><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
      </div>
    </a>

    

      

</nav>

<!-- HERO -->

<section class="hero">
<h1>Shopping</h1>
<p>Discover Amazing Products</p>
</section>

<div class="container">

    <!-- SEARCH -->

    <form>
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search Products...">
            <i class="fas fa-search"></i>
        </div>
    </form>

    <div class="counter">

    <h2 id="productCount"> Showing <?= mysqli_num_rows($product_query) ?> of <?= $total_products ?> Products </h2>

    <div> Page <?= $page ?> of <?= $total_pages ?> </div>

</div>

<!-- CATEGORY -->

<div class="categories">

    <button class="category-btn active" data-category="all"> All </button>

    <?php while( $cart = mysqli_fetch_assoc($category_query) ): ?>

        <button class="category-btn" data-category="<?= strtolower($cart['product_category']) ?>">

        <?= htmlspecialchars($cart['product_category']) ?>

        </button>

    <?php endwhile; ?>

</div>

<!-- PRODUCT GRID -->
<div class="product-grid">

<?php while( $row = mysqli_fetch_assoc($product_query) ): ?>

<div class="product-card" data-category="<?= strtolower($row['product_category']) ?>">

<div class="product-image">

<img src="uploads/<?php echo $row['image']; ?>" alt="">

</div>

<div class="product-info">

<h3 class="product-title"> <?= $row['name'] ?> </h3>

<div class="price"> $<?= number_format($row['price'],2) ?> </div>

<div class="bottom">

<span> <?= $row['product_category'] ?> </span>

<button class="cart-btn" data-id="<?= $row['id'] ?>"> <i class="fas fa-cart-plus"></i> </button>

</div>

</div>

</div>

<?php endwhile; ?>

</div>

    <div class="pagination">

        <?php for(
            $i=1;
            $i<=$total_pages;
            $i++
            ):
        ?>
        <a href="?page=<?= $i ?>" class="<?= $page==$i ? 'active' : '' ?>"> <?= $i ?></a>

    <?php endfor; ?>

    </div>

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

const searchInput = document.getElementById('searchInput');

const cards = document.querySelectorAll('.product-card');

searchInput.addEventListener('keyup',function(){
    let value =
    this.value.toLowerCase();
    cards.forEach(card=>{
        let title = card.querySelector( '.product-title' )
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

document.querySelectorAll('.category-btn').forEach(btn=>{

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

</body>
</html>