<?php
// Railway database connection
if (isset($_ENV['DATABASE_URL'])) {
    // Parse Railway's DATABASE_URL format
    $db_parts = parse_url($_ENV['DATABASE_URL']);
    $host = $db_parts['host'];
    $username = $db_parts['user'];
    $password = $db_parts['pass'];
    $database = ltrim($db_parts['path'], '/');
    $port = $db_parts['port'] ?? 3306;
} else {
    // Fallback for local development
    $host = $_ENV['DB_HOST'] ?? "localhost";
    $username = $_ENV['DB_USER'] ?? "root";
    $password = $_ENV['DB_PASSWORD'] ?? "";
    $database = $_ENV['DB_NAME'] ?? "bakery";
    $port = $_ENV['DB_PORT'] ?? 3306;
}

try {
    $conn = new mysqli($host, $username, $password, $database, $port);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8
    $conn->set_charset("utf8");
} catch (Exception $e) {
    // Log error and show user-friendly message
    error_log("Database connection error: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}
?>