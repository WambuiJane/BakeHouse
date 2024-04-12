<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bakery";
    $conn = new mysqli($servername, $username, $password, $dbname);
    //exception handling
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>