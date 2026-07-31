<!-- ========================= -->
<!-- process_payment.php -->
<!-- ========================= -->

<?php

if(isset($_FILES['payment_image'])){

    $fileName =
    $_FILES['payment_image']['name'];

    $tmpName =
    $_FILES['payment_image']['tmp_name'];

    $uploadPath =
    "uploads/" . $fileName;

    move_uploaded_file(
        $tmpName,
        $uploadPath
    );

    echo "

    <div style='
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    flex-direction:column;
    font-family:Arial;
    background:#f5f7fb;
    '>

        <h1 style='color:#6c3bff;'>
        Payment Successful 🎉
        </h1>

        <p>
        Your payment screenshot uploaded successfully.
        </p>

        <a href='cart.php'
        style='
        margin-top:20px;
        background:#6c3bff;
        color:white;
        padding:15px 30px;
        border-radius:10px;
        text-decoration:none;
        '>

        Back To Cart

        </a>

    </div>

    ";

}

?>