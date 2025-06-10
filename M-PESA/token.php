<?php
// fetch and output access token
$consumerKey = 'IJPs8xAggznZ0n0sLwzE5GFmBGcoyQaACjZPlUMRscfyv6jX';
$consumerSecret = 'ihi8Sfffwc01TVdU2NMn6NqwlysxJmToqQDlEOrhImXJSc10wOz2xsNdxFb7oeAD';

$headers = ['Content-Type:application/json; charset=utf8'];
$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_HEADER, FALSE);
curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
$result = curl_exec($curl); 
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

if ($status !== 200) {
    die("Error: Failed to retrieve access token.");
}

$response = json_decode($result);

if (!isset($response->access_token)) {
    die("Error: Access token not found in response.");
}

$accessToken = $response->access_token;

echo $response->access_token;
// Close cURL resource
curl_close($curl);