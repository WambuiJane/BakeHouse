<?php
// Start the session
session_start();

if(isset($_SESSION['user'])) {
    // Unset all of the session variables
    unset($_SESSION['user']);

    if(isset($_SESSION['cart'])) {
        // Unset all of the session variables
        unset($_SESSION['cart']);
    }
    header('location: ../index.php');

}
if(isset($_SESSION['admin'])) {
    // Unset all of the session variables
    unset($_SESSION['admin']);
    header('location: ../adminlogin.php');
}