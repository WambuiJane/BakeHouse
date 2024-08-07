<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if file was uploaded without errors
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $image = $_FILES["image"]["tmp_name"];
        $imgContent = addslashes(file_get_contents($image));
        $imageType = $_FILES["image"]["type"];
        
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $size = $_POST['size'];
        
        // Insert image content into database
        $sql = "INSERT INTO products (name, description, price, image, size, image_type) VALUES ('$name', '$description', '$price', '$imgContent', '$size', '$imageType')";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error: " . $_FILES["image"]["error"];
    }
}

$conn->close();
?>
