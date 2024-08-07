<?php
include 'connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT image, image_type FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($imgContent, $imageType);
    $stmt->fetch();
    
    // Set the content type header
    header("Content-type: $imageType");
    echo $imgContent;
    
    $stmt->close();
}

$conn->close();
?>
