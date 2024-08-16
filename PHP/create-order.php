<?php
require_once 'connect.php';
require_once 'paypal-config.php';

function createOrder($total) {
    $clientId = 'Aazr50ohh5iut8PVxFMB3PRER5U5q3i06V_RxfZCcDzNLztgc6im-mDtkg0GMsJdnIZuf0BYM_f_VxFV';
    $clientSecret = 'EI7UNLeCGuKr3PqvSweiUL6ss2AZwHF0IRez70kw4MZg1Kum-0liCygsF3NMvVLg3lppP0V5pLvZ8UqL';
    $apiUrl = 'https://api-m.sandbox.paypal.com/v2/checkout/orders';

    // Obtain an access token from PayPal
    $token = getAccessToken($clientId, $clientSecret);
    if (!$token) {
        error_log('Unable to get access token from PayPal.');
        return ['error' => 'Unable to get access token from PayPal.'];
    }

    $payload = [
        'intent' => 'CAPTURE',
        'purchase_units' => [[
            'amount' => [
                'currency_code' => 'USD',
                'value' => number_format($total, 2, '.', '')
            ]
        ]],
        'application_context' => [
            'return_url' => 'http://localhost/Bakery/PHP/process_checkout.php',
            'cancel_url' => 'http://localhost/Bakery/checkout_cancel.php'
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        error_log('Curl error in createOrder: ' . $error);
        curl_close($ch);
        return ['error' => 'Curl error: ' . $error];
    }
    
    curl_close($ch);

    if ($httpCode != 201) {
        error_log('Error creating PayPal order: ' . $response);
        return ['error' => 'Error creating PayPal order: ' . $response];
    }

    $decodedResponse = json_decode($response, true);
    error_log('PayPal order created successfully: ' . print_r($decodedResponse, true));
    
    return $decodedResponse;
}

// Ensure that the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Read the JSON body
$body = file_get_contents('php://input');
$data = json_decode($body, true);

// Validate the input
if (!isset($data['total']) || !is_numeric($data['total'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid total amount']);
    exit;
}

// Create the order and send the response
$response = createOrder($data['total']);
header('Content-Type: application/json');
echo json_encode($response);
?>
