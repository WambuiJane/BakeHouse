<?php
session_start();

ob_start();
require_once 'token.php'; 
$accessToken = ob_get_clean();
require_once '../PHP/connect.php';

// Check if we got the access token
if (!isset($accessToken) || empty($accessToken)) {
    die("Failed to get access token");
}

// Check if connection exists
if (!isset($conn) || $conn === null) {
    die("Database connection failed. Please check your connection settings.");
}

// Check if email session exists
if (!isset($_SESSION['email'])) {
    die("User email not found in session. Please login again.");
}

$price = $_POST['total'];
$_SESSION['price'] = $price;
$price = 1;


$sql = "SELECT phone from user where email = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Database query error: " . $conn->error);
}

$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$stmt->bind_result($phone);
$stmt->fetch();
$stmt->close();

// Validate phone number
if (empty($phone)) {
    die("Phone number not found for user. Please update your profile.");
}

$phone = intval("254".$phone);

$url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$shortCode = '174379';
$timestamp = date('YmdHis');
$passKey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
$password = base64_encode($shortCode . $passKey . $timestamp);

$curl_post_data = [
    'BusinessShortCode' => $shortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $price,
    'PartyA' => $phone,
    'PartyB' => $shortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => 'https://bakehouse-production.up.railway.app/M-PESA/callback.php',
    'AccountReference' => 'BAKEHOUSE',
    'TransactionDesc' => 'Payment for BakeHouse Order'
];

$data_string = json_encode($curl_post_data);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $accessToken));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

$curl_response = curl_exec($curl);

if ($curl_response === false) {
    $error = curl_error($curl);
    echo "Curl Error: " . $error;
} else {
    $response = json_decode($curl_response, true);
    if (isset($response['CheckoutRequestID'])) {
        $checkoutRequestID = $response['CheckoutRequestID'];
        // Redirect to query.php passing CheckoutRequestID as a query parameter
        header("Location: query.php?checkoutRequestID=$checkoutRequestID");
        exit;
    } else {
        echo "Failed to retrieve CheckoutRequestID from response. Response: " . $curl_response;
    }
}

curl_close($curl);
?>