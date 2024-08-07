<?php
session_start(); 
// Include PHPMailer library files
require '../vendor/autoload.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

include 'connect.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required fields are filled
    if (isset($_POST['fullname'], $_POST['email'], $_POST['password'], $_POST['phone'])) {
        $fullname = $conn->real_escape_string($_POST['fullname']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = $conn->real_escape_string($_POST['password']);
        $phone = $conn->real_escape_string($_POST['phone']);
        // Array to store duplicate fields
        $duplicateFields = [];

        // Check for duplicate fullname
        $checkFullnameQuery = "SELECT fullname FROM user WHERE fullname = ?";
        $checkStmt = $conn->prepare($checkFullnameQuery);
        $checkStmt->bind_param("s", $fullname);
        $checkStmt->execute();
        $checkStmt->store_result();
        if ($checkStmt->num_rows > 0) {
            $duplicateFields[] = "fullname";
        }
        $checkStmt->close();

        // Check for duplicate email
        $checkEmailQuery = "SELECT email FROM user WHERE email = ?";
        $checkStmt = $conn->prepare($checkEmailQuery);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkStmt->store_result();
        if ($checkStmt->num_rows > 0) {
            $duplicateFields[] = "email";
        }
        $checkStmt->close();

        // Check for duplicate password (hashed version)
        $checkPasswordQuery = "SELECT password FROM user WHERE password = ?";
        $checkStmt = $conn->prepare($checkPasswordQuery);
        $checkStmt->bind_param("s", $hashedPassword);
        $checkStmt->execute();
        $checkStmt->store_result();
        if ($checkStmt->num_rows > 0) {
            $duplicateFields[] = "password";
        }
        $checkStmt->close();

        // Check for duplicate phone
        if (!empty($phone)) {
            $checkPhoneQuery = "SELECT phone FROM user WHERE phone = ?";
            $checkStmt = $conn->prepare($checkPhoneQuery);
            $checkStmt->bind_param("s", $phone);
            $checkStmt->execute();
            $checkStmt->store_result();
            if ($checkStmt->num_rows > 0) {
                $duplicateFields[] = "phone";
            }
            $checkStmt->close();
        }

        if (count($duplicateFields) > 0) {
            // Display error messages for duplicate fields
            $error="Error: The following fields are already registered: " . implode(", ", $duplicateFields);
            header('Location: ../signup.php?error=' . $error);
        } else {

            try {
                // Generate a random verification code
                $vericode = rand(100000, 999999);
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'karugajane511@gmail.com';
                $mail->Password = 'qetu xeyp weqs dtbz';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->setFrom('karugajane511@gmail.com', 'Jane');
                $mail->addAddress($email, $fullname);
                $mail->Subject = 'Account Verification';
                $mail->Body = 'Hello ' . $fullname . ', Welcome to BakeHouse! Your verification code is ' . $vericode . '. Please enter this code to verify your email address.';
                $mail->send();
            
                // Hashing the password
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Prepare SQL statement to prevent SQL injection
                $stmt = $conn->prepare("INSERT INTO user (fullname, email, password, phone) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $fullname, $email, $hashedPassword, $phone);
                
                if ($stmt->execute()) {
                    $feedback = "New record created successfully";
                    $stmt2 = $conn->prepare("INSERT INTO verification (email, vercode) VALUES (?, ?)");
                    $stmt2->bind_param("si", $email, $vericode);
                    $stmt2->execute();
            
                    $_SESSION['email'] = $email;
                    header('Location: ../Dashboard.html');
                } else {
                    $stmt3 = $conn->prepare("DELETE FROM user WHERE email = ?");
                    $stmt3->bind_param("s", $email);
                    $feedback = "Error, please try again later.";
                    header('Location: ../signup.php?error=' . $feedback);
                }
            } catch (Exception $e) {
                // Display error message if something goes wrong
                $feedback = "Connection error, please try again later.";
                header('Location: ../signup.php?error=' . $feedback);
            }
            
            // Close statement
            $stmt->close();
        }
    } else {
        // Output which field is causing the issue
        if (!isset($_POST['fullname'])) {
            $error= "Error: Fullname field is not set.<br>";
            header('Location: ../signup.php?error=' . $error);
        }
        if (!isset($_POST['email'])) {
            $error= "Error: Email field is not set.<br>";
            header('Location: ../signup.php?error=' . $error);
        }
        if (!isset($_POST['password'])) {
            $error= "Error: Password field is not set.<br>";
            header('Location: ../signup.php?error=' . $error);
        }
    }
} else {
    echo "No POST request received.";
}

// Close database connection
$conn->close();
?>
