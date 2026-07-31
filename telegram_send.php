
<?php

$botToken = "8902382730:AAF0hv51mQrCmVjoE8ZZkr4o1Y1vG0u7joA";
$chatId   = "5256157060";

/* --------------------------
   CUSTOMER INFO
--------------------------- */

$fullname = $_POST['fullname'] ?? '';
$phone    = $_POST['phone'] ?? '';
$email    = $_POST['email'] ?? '';
$address  = $_POST['address'] ?? '';
$payment   = $_POST['payment '] ?? '';

/* --------------------------
   SEND TEXT MESSAGE
--------------------------- */

function sendTelegramMessage($botToken, $chatId, $message)
{
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

    $data = [
        'chat_id' => $chatId,
        'text'    => $message
    ];

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true
    ]);

    $response = curl_exec($ch);

    curl_close($ch);

    return $response;
}

/* --------------------------
   SEND PRODUCT IMAGE
--------------------------- */

function sendProductPhoto(
    $botToken,
    $chatId,
    $imagePath,
    $caption
)
{
    if (!file_exists($imagePath)) {
        return false;
    }

    $url = "https://api.telegram.org/bot{$botToken}/sendPhoto";

    $postFields = [
        'chat_id' => $chatId,
        'photo'   => new CURLFile($imagePath),
        'caption' => $caption
    ];

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $postFields,
        CURLOPT_RETURNTRANSFER => true
    ]);

    $response = curl_exec($ch);

    curl_close($ch);

    return $response;
}

/* --------------------------
   BUILD ORDER SUMMARY
--------------------------- */

$message  = "🛒 NEW ORDER\n\n";
$message .= "👤 Customer: {$fullname}\n";
$message .= "📞 Phone: {$phone}\n";
$message .= "📧 Email: {$email}\n";
$message .= "📍 Address: {$address}\n\n";
$message .= "💳 Payment: $payment\n\n" .


$grandTotal = 0;
$totalItems = 0;

if (!empty($_SESSION['cart']))
{
    foreach ($_SESSION['cart'] as $item)
    {
        $qty = (int)($item['qty'] ?? 1);
        $price = (float)($item['price'] ?? 0);

        $grandTotal += ($qty * $price);
        $totalItems += $qty;
    }
}

$message .= "🛍 Items: {$totalItems}\n";
$message .= "💰 Total: $" . number_format($grandTotal, 2) . "\n";
$message .= "🕒 " . date('Y-m-d H:i:s');

/* --------------------------
   SEND ORDER SUMMARY
--------------------------- */

sendTelegramMessage(
    $botToken,
    $chatId,
    $message
);

/* --------------------------
   SEND EACH PRODUCT IMAGE
--------------------------- */

if (!empty($_SESSION['cart']))
{
    foreach ($_SESSION['cart'] as $item)
    {
        $name  = $item['name'] ?? 'Product';
        $qty   = $item['qty'] ?? 1;
        $price = $item['price'] ?? 0;

        /*
         Example:
         uploads/airpods.jpg
        */
        $imageFile = $item['image'] ?? '';

        $imagePath = __DIR__ . "/uploads/" . $imageFile;

        $caption =
            "📦 Product: {$name}\n" .
            "🔢 Qty: {$qty}\n" .
            "💰 Price: $" . number_format($price, 2) . "\n" .
            "💵 Subtotal: $" . number_format(($qty * $price), 2);
            "💳 Payment: $payment\n\n" .

        sendProductPhoto(
            $botToken,
            $chatId,
            $imagePath,
            $caption
        );
    }
}


?>




