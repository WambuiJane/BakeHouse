<?php
include_once 'connect.php';

// Check if categories are selected
if (isset($_POST['categories']) && !empty($_POST['categories'])) {
    $categories = $_POST['categories'];
    $categories = implode(',', $categories); // Convert array to comma-separated values

    // Query to fetch cakes based on selected categories
    $sql = "SELECT * FROM products WHERE category_id IN ($categories)";
} else {
    // Query to fetch all cakes if no category is selected
    $sql = "SELECT * FROM products";
}

$result = $conn->query($sql);

if ($result === false) {
    // Handle query error
    echo "Error: " . $conn->error;
} else {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<form class='card' method='post' action='PHP/addcart.php'>";
            echo "<img src='Images/" . $row["image"] . "' alt='" . $row["name"] . "'>";
            echo "<h2>" . $row["name"] . "</h2>";
            echo "<div class='inner-card'>";
            if ($row["Quantity"] < 1) {
                echo "<h3><span>Out of stock</span></h3>";
            } else {
                echo "<h3><p>$" . $row["price"] . "</p></h3>";
            }
            echo "<span class='material-symbols-outlined'>favorite</span>";
            echo "</div>";
            echo "<span class='shopping-cart material-symbols-outlined'>shopping_cart</span>";
            echo "<input type='hidden' name='productId' value='" . $row["id"] . "'>";
            echo "<input type='hidden' name='action' value='add'>";
            echo "</form>";
        }
    } else {
        echo "<p>No cakes available.</p>";
    }
}