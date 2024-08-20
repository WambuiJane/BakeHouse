<?php
session_start();
// Include gen_token.php to fetch the access token
ob_start();
include 'token.php';
include '../php/connect.php';
$accessToken = ob_get_clean();

$price = $_POST['total'];
$_SESSION['price'] = $price;
$price = 1;

$sql = "SELECT phone from user where email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$stmt->bind_result($phone);
$stmt->fetch();
$stmt->close();
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
    'CallBackURL' => 'https://yourdomain.com/callback.php',
    'AccountReference' => 'WSCS',
    'TransactionDesc' => 'Payment for X'
];

$data_string = json_encode($curl_post_data);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $accessToken)); // Access token passed here
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
        echo "Failed to retrieve CheckoutRequestID from response";
    }
}

curl_close($curl);
