<?php
session_start();
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../php/connect.php';

function getProduct($productId) {
    global $conn;

    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Database query error: " . $conn->error);
        return null;
    }
    $stmt->bind_param("s", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

// normal cart
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
$cartItems = [];
foreach ($cart as $productId => $quantity) {
    $product = getProduct($productId);
    if ($product) {
        $price = $product['price'];
        $subtotal = $price * $quantity;
        $total += $subtotal;
        $cartItems[] = [
            'id' => $productId,
            'name' => $product['name'],
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $subtotal
        ];
    }
}

// Custom order cart
$customCart = isset($_SESSION['customCart']) ? $_SESSION['customCart'] : [];
foreach ($customCart as $customCakeId => $customCake) {
    $price = $customCake['price'];
    $subtotal = $price;
    $total += $subtotal;
    $cartItems[] = [
        'id' => $customCakeId,
        'name' => "Custom Cake #$customCakeId",
        'quantity' => 1,
        'price' => $price,
        'subtotal' => $subtotal
    ];
}

if (isset($_GET['checkoutRequestID'])) {
    $checkoutRequestID = $_GET['checkoutRequestID'];

    ob_start();
    include 'token.php';
    $accessToken = ob_get_clean();

    $retryCount = 5;
    $retryInterval = 10;
    $paymentStatusUpdated = false;

    while ($retryCount > 0 && !$paymentStatusUpdated) {
        $ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query');

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);

        $shortCode = '174379';
        $timestamp = date('YmdHis');
        $passKey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919'; // Sandbox Pass Key
        $password = base64_encode($shortCode . $passKey . $timestamp);

        $requestBody = json_encode([
            "BusinessShortCode" => $shortCode,
            "Password" => $password,
            "Timestamp" => $timestamp,
            "CheckoutRequestID" => $checkoutRequestID
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo "Failed to query payment status. Retrying...";
            $retryCount--;
            sleep($retryInterval);
            continue;
        }

        $responseDecoded = json_decode($response, true);

        if (isset($responseDecoded['ResultCode']) && $responseDecoded['ResultCode'] == 0) {
            $paymentStatusUpdated = true;
            $email = $_SESSION['email'];

            $sql = "SELECT * FROM user WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $userId = $user['UserID'];
            $date = date("Y-m-d H:i:s");
            $orderStatus = "Paid";

            $sql = "INSERT INTO tickets (user_email, PayPalOrderId, OrderDate, OrderStatus, total) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $email, $checkoutRequestID, $date, $orderStatus, $_SESSION['price']);
            $stmt->execute();

            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'karugajane511@gmail.com';
                $mail->Password = 'qetu xeyp weqs dtbz';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->setFrom('no-reply@bakehouse.com', 'Jane Karuga');
                $mail->addAddress($email);
                $mail->Subject = 'Payment Confirmation';

                $mail->Body = "Dear Customer, Your payment was successful. Your order will be processed shortly. Thank you for shopping with us.";
                $mail->send();

            } catch (Exception $e) {
                echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

            // Add th Orders to the database
            foreach ($cartItems as $item) {
                $productId = $item['id'];
                $quantity = $item['quantity'];
                $price = $item['price'];
                $subtotal = $item['subtotal'];

                // check if the cake is already in the database and if not create a new record
                $sql = "SELECT * FROM products WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $productId);
                $stmt->execute();
                $result = $stmt->get_result();
                $product = $result->fetch_assoc();
                $customId = 7;
                $image = "custom cake.avif";

                if ($product) {
                    $sql = "UPDATE products SET Quantity = Quantity - ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ii", $quantity, $productId);
                    $stmt->execute();
                }else{
                    $sql = "INSERT INTO products (id, name, price, image, category_id) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("isssi", $productId, $item['name'], $price, $image, $customId);
                    $stmt->execute();
                }
                

                $sql = "INSERT INTO orders (User_Id, Cake_Id, Paypal_Id, Quantity, Price, Subtotal) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iisiss", $userId, $productId, $checkoutRequestID,$quantity, $price, $subtotal);
                $stmt->execute();
            }
            unset($_SESSION['cart']);
            unset($_SESSION['customCart']);
            header('Location: ../dashboard.php?message=' . urlencode("Payment was successful"));
        }

        $retryCount--;
        sleep($retryInterval);
    }

    if (!$paymentStatusUpdated) {
        $feedback = "Payment was not successful";
        header('Location: ../checkout.php?message=' . urlencode($feedback));
    }

} else {
    echo "CheckoutRequestID not provided";
}
?>
