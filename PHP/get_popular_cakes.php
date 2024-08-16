<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set content type to JSON
header('Content-Type: application/json');

// Debugging line
file_put_contents('session_debug.log', print_r($_SESSION, true));

try {
    if (isset($_SESSION['popular_cakes'])) {
        $cakes = $_SESSION['popular_cakes'];
        echo json_encode($cakes);
    } else {
        echo json_encode([]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}
?>
