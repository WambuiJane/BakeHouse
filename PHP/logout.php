<?php
// Start the session
session_start();

if(isset($_SESSION['email'])) {
    unset($_SESSION['email']);

    if(isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
    header('location: ../dashboard.php');

}
if(isset($_SESSION['admin'])) {
    // Unset all of the session variables
    unset($_SESSION['admin']);
    header('location: ../adminlogin.php');
}
else{
    echo "You are not logged in";

}
?>