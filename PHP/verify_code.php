<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
    <link rel="stylesheet" href="../css/Register.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    
</head>
<body>
    <div class="navbar">
        <h1>BAKE HOUSE</h1>
        <?php
            if (isset($_GET['feedback'])) {
                echo "<p>" . htmlspecialchars($_GET['feedback']) . "</p>";  
                }
        ?>
    </div>
    <div class="main">
        <div class="image">
        </div>
        <form method="post" id="verify-form" class="container" action="verify_code.php">
            <h1>Welcome</h1>
            <p>Email Confirmation</p>
            <div class="input">
                <h1>Email</h1>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="submit">
                <button type="submit">VERIFY</button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';
require_once 'connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// First step verification
// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required fields are filled
    if (isset($_POST['email'])) {
        $email = $conn->real_escape_string($_POST['email']);

        // Check if email exists in the database
        $checkEmailQuery = "SELECT * FROM user WHERE email = ?";
        $checkStmt = $conn->prepare($checkEmailQuery);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            // Generate a secure verification code
            $verificationCode = rand(100000, 999999);
            $expirationTime = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
            // Store the verification code in the verification table
            $storeVerificationCodeQuery = "INSERT INTO passwordreset (email, reset_code, expiration_time) VALUES (?, ?, ?)";
            $storeStmt = $conn->prepare($storeVerificationCodeQuery);
            $storeStmt->bind_param("sss", $email, $verificationCode, $expirationTime);
            $storeStmt->execute();

            if ($storeStmt->affected_rows > 0) {
                try {
                    // Send the verification code to the user's email
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'karugajane511@gmail.com'; 
                    $mail->Password = 'qetu xeyp weqs dtbz'; 
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;
                    $mail->setFrom('Karugajane511@gmail.com', 'BakeHouse');
                    $mail->addAddress($email);
                    $mail->Subject = 'Email Verification Code';
                    $mail->Body = "Your email verification code is: $verificationCode";
                    $mail->send();

                    $feedback = 'Verification code sent successfully. Please check your email.';
                    header('Location: reset_password.php?feedback=' . $feedback);
                    exit();

                } catch (Exception $e) {
                    $feedback = 'Error sending verification code: ' . $mail->ErrorInfo;
                }
            } else {
                $feedback = 'Failed to store verification code in the database.';
            }
        } else {
            $feedback = 'Email not found in the database.';
            header('Location: verify_code.php?feedback=' . $feedback);
        }
    } else {
        $feedback = 'Email is required.';
    }
} else {
    $feedback = 'Method not allowed.';
}
?>

