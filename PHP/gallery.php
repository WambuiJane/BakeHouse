<!DOCTYPE html>
<html lang="en">
<title>Cake Catalogue</title>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <h1>Our Various Products</h1>
    <link rel="stylesheet" href="Gallery.css">
</head>
<body>
    <div class="gallery">
        <?php
        include_once 'connect.php';
        // Query to fetch products from the `products` table
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql) or die($conn->error);

        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<div class='product'>";
                echo "<h2>" . $row["name"] . "</h2>";
                echo "<p>" . $row["description"] . "</p>";
                echo "<p>Price: $" . $row["price"] . "</p>";
                echo "<p>Size: " . $row["size"] . "</p>";
                echo '<img src="image.php?id=' . $row["id"] . '" alt="' . $row["name"] . '">';
                echo '<button class="order-button" onclick="window.location.href=\'Order.html\';">Order Here</button>';
                echo "</div>";
            }
        } else {
            echo "0 results";
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
