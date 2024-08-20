<?php
session_start();
include 'connect.php';

// Check if the user is already logged in
if(isset($_SESSION['admin'])) {
    header("Location: ../admin.php");
    exit();
}

$email = $_POST['email'];
$password = $_POST['password'];

// Check whether email exists
$sql = "SELECT email FROM admin WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($existsemail);
$stmt->fetch();
$stmt->close();
if (!$existsemail) {
            $error = "Invalid password or email!";
            header('Location: adminlogin.php?error=' . urlencode($error));
            exit();
}

// Check the password for the existing email
$sql = "SELECT password FROM admin WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($hashedPass);
$stmt->fetch();
$stmt->close();
$conn->close();

if ($password == $hashedPass) { 
    $_SESSION['admin'] = $email;
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);
    header("Location: ../admin.php");
    exit(); 
} else {
    $error = "Invalid password!";
    header('Location: adminlogin.php?error=' . urlencode($error)); 
    exit(); 
}
?>