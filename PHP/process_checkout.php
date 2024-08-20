<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_log("Received token: " . ($_GET['token'] ?? 'No token'));

session_start();

require_once 'connect.php';
require_once 'paypal-config.php';
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

enum OrderStatus: string {
    case Pending = 'PENDING';
    case Completed = 'COMPLETED';
    case Cancelled = 'CANCELLED';
}

function logDetails($message, $data = null) {
    $logMessage = date('Y-m-d H:i:s') . " - $message";
    if ($data !== null) {
        $logMessage .= "\nData: " . print_r($data, true);
    }
    error_log($logMessage);
}


function getOrderDetails($orderId) {
    $clientId = 'Aazr50ohh5iut8PVxFMB3PRER5U5q3i06V_RxfZCcDzNLztgc6im-mDtkg0GMsJdnIZuf0BYM_f_VxFV';
    $clientSecret = 'EI7UNLeCGuKr3PqvSweiUL6ss2AZwHF0IRez70kw4MZg1Kum-0liCygsF3NMvVLg3lppP0V5pLvZ8UqL';
    $apiUrl = "https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderId";

    $accessToken = getAccessToken($clientId, $clientSecret);
    if (!$accessToken) {
        error_log('Unable to get access token from PayPal.');
        return null;
    }

    logDetails("Attempting to get order details", ['orderId' => $orderId]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: Bearer $accessToken"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    error_log("PayPal API response code: $httpCode");
    error_log("PayPal API response body: $response");
    if ($error) {
        error_log("Curl error: $error");
    }

    if ($httpCode != 200) {
        error_log("PayPal API request failed: $httpCode - $error");
        error_log("PayPal API response: " . print_r($response, true));
        return null;
    }

    error_log("Order details retrieved successfully: " . print_r($response, true));
    logDetails("PayPal getOrderDetails response", ['httpCode' => $httpCode, 'response' => $response]);
    return json_decode($response, true);
}

function capturePayment($orderId) {
    $clientId = 'Aazr50ohh5iut8PVxFMB3PRER5U5q3i06V_RxfZCcDzNLztgc6im-mDtkg0GMsJdnIZuf0BYM_f_VxFV';
    $clientSecret = 'EI7UNLeCGuKr3PqvSweiUL6ss2AZwHF0IRez70kw4MZg1Kum-0liCygsF3NMvVLg3lppP0V5pLvZ8UqL';
    $apiUrl = "https://api-m.sandbox.paypal.com/v2/checkout/orders/$orderId/capture";

    $accessToken = getAccessToken($clientId, $clientSecret);
    if (!$accessToken) {
        error_log('Unable to get access token from PayPal.');
        return null;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: Bearer $accessToken"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($httpCode != 201) {
        error_log("PayPal capture request failed: $httpCode - $error");
        error_log("PayPal capture response: " . print_r($response, true));
        return null;
    }

    return json_decode($response, true);
}

function insertOrder($total, $paypalId, $status) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO tickets (OrderStatus, Total, PayPalID) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $status, $total, $paypalId);
    $stmt->execute();
    $orderId = $stmt->insert_id;
    $stmt->close();
    return $orderId;
}

function updateOrder($orderId, $status) {
    global $conn;
    $stmt = $conn->prepare("UPDATE tickets SET OrderStatus = ? WHERE OrderID = ?");
    $stmt->bind_param("ss", $status, $orderId);
    $stmt->execute();
    $stmt->close();
}

function sendOrderConfirmationEmail($orderId, $paypalId, $status, $total, $userEmail, $userName) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('EMAIL_USERNAME');
        $mail->Password = getenv('EMAIL_PASSWORD');
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('noreply@bakehouse.com', 'BakeHouse');
        $mail->addAddress($userEmail, $userName);

        $mail->Subject = 'Order Confirmation';
        $mail->Body = "Order ID: $orderId\nPayPal ID: $paypalId\nStatus: $status\nTotal: $total";

        $mail->send();
        error_log("Order confirmation email sent successfully.");
    } catch (Exception $e) {
        error_log("Error sending order confirmation email: " . $e->getMessage());
    }
}

// Main logic
$token = $_GET['token'] ?? null;

if ($token) {
    $orderDetails = getOrderDetails($token);

    if ($orderDetails && isset($orderDetails['id'])) {
        $paypalId = $orderDetails['id'];
        $total = $orderDetails['purchase_units'][0]['amount']['value'] ?? 0;
        
        // Capture the payment
        $captureResult = capturePayment($paypalId);
        
        if ($captureResult && $captureResult['status'] === 'COMPLETED') {
            $orderStatus = OrderStatus::Completed->value;
            $orderId = insertOrder($total, $paypalId, $orderStatus);
            
            sendOrderConfirmationEmail(
                $orderId,
                $paypalId,
                $orderStatus,
                $total,
                $_SESSION['email'] ?? 'customer@example.com',
                $_SESSION['fullname'] ?? 'Valued Customer'
            );
            
            echo "Order processed successfully. Order ID: $orderId";
            $sql = "SELECT  id FROM  users where email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $_SESSION['email']);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $userId = $row['id'];


            //  insert multiple cakes
            if($_SESSION['customCart']){
                $customCakeId = $_SESSION['customCart'];
                foreach ($customCakeId as $customCakeId => $customCake) {
                    $sql = "INSERT INTO orders (User_Id, Paypal_Id, Cake_Id) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("isi", $userId, $paypalId, $customCakeId);
                } 
                if($_SESSION['cart']){
                    $cart = $_SESSION['cart'];
                    foreach ($cart as $productId => $quantity) {
                        $sql = "INSERT INTO orders (User_Id, Paypal_Id, Cake_Id) VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("isi", $userId, $paypalId, $productId);
                    }
                    $feedback = "Order processed successfully. Order ID: $orderId";
                }
                else{
                    echo "Order processed successfully. Order ID: $orderId";
                }
                header('Location: ../gallery.php?feedback=Order processed successfully. Order ID: ' . $orderId);
            }
            else {
                if($_SESSION['cart']){
                    $cart = $_SESSION['cart'];
                    foreach ($cart as $productId => $quantity) {
                        $sql = "INSERT INTO orders (User_Id, Paypal_Id, Cake_Id) VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("isi", $userId, $paypalId, $productId);
                    }
                    $feedback = "Order processed successfully. Order ID: $orderId";
                }
                else{
                    $feedback = "Order processed successfully. Order ID: $orderId";
                }

            }
            header('Location: ../gallery.php?feedback= '.$feedback);
        } else {
            $errorMessage = $captureResult['details'][0]['issue'] ?? 'Unknown error occurred';
            logDetails("Payment capture failed", ['error' => $errorMessage]);
            echo "Failed to process the payment: $errorMessage. Please try again or contact support.";
            
            if($_SESSION['customCart']){
                $customCakeId = $_SESSION['customCart'];
                foreach ($customCakeId as $customCakeId => $customCake) {
                    echo $customCakeId;
                    echo $customCake;
                } 
                if($_SESSION['cart']){
                    $cart = $_SESSION['cart'];
                    foreach ($cart as $productId => $quantity) {
                        echo $productId;
                        echo $quantity;
                    }
                    $feedback = "Order processed successfully. Order ID: $orderId";
                }
                else{
                    // echo "Order processed successfully. Order ID: $orderId";
                }
                // header('Location: ../gallery.php?feedback=Order processed successfully. Order ID: ' . $orderId);
            }
            else {
                if($_SESSION['cart']){
                    $cart = $_SESSION['cart'];
                    foreach ($cart as $productId => $quantity) {
                        echo $productId;
                        echo $quantity;
                    }
                }
                else{
                    $feedback = "Order failed. Order ID: $orderId";
                }

            }

        }
    } else {
        error_log("Order details not found for token: $token");
        echo "Order details not found. Please try again.";
    }
} else {
    error_log("No token provided in the request.");
    echo "No token provided. Please start the checkout process again.";
}
