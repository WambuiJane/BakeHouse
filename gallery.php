<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('location: index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cake Catalogue</title>
    <link rel="stylesheet" href="css/Gallery.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <div class="navbar">
        <div class="nav-links">
            <h1>BAKE HOUSE</h1>
            <a href="Dashboard.php #hero#">Home</a>
            <a href="Dashboard.php #about-us">About Us</a>
            <a href="Order.php">Order</a>
        </div>
        <div class="nav-logos">
            <span class="material-symbols-outlined">search</span>
            <span class="material-symbols-outlined" onclick="window.location.href='Cart.php'">shopping_cart</span>
            <span class="material-symbols-outlined" id="login-btn">account_circle</span>
        </div>
    </div>
    <div class="container">
        <form class="filters" method="post" action="">
            <h2>Category</h2>
            <?php
            include_once 'PHP/connect.php';
            // Query to fetch categories from the categories table
            $sql = "SELECT * FROM categories";
            $result = $conn->query($sql);

            if ($result === false) {
                // Handle query error
                echo "Error: " . $conn->error;
            } else {
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='category-filter'>";
                        echo $row["name"];
                        echo "<input type='checkbox' name='category[]' value='" . $row["id"] . "'>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No categories available.</p>";
                }
            }
            ?>
        </form>
        <div class="line"></div>

        <div class="products">
            <?php
            include_once 'PHP/connect.php';
            // Query to fetch products from the products table
            $sql = "SELECT * FROM products";
            $result = $conn->query($sql) or die($conn->error);

            if ($result->num_rows > 0) {
                // Output data of each row
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
                echo "0 results";
            }

            $conn->close();
            ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            function attachCartListeners() {
                $('.shopping-cart').off('click').on('click', function(e) {
                    e.preventDefault();
                    $(this).closest('form').submit();
                });
            }

            attachCartListeners();

            $('.category-filter input').change(function (e) {
                e.preventDefault();

                var selectedCategories = [];
                $('.category-filter input:checked').each(function () {
                    selectedCategories.push($(this).val());
                });

                $.ajax({
                    type: 'POST',
                    url: 'PHP/filter.php',
                    data: {
                        categories: selectedCategories
                    },
                    success: function (response) {
                        $('.products').html(response);
                        attachCartListeners(); // Reattach listeners after AJAX call
                    }
                });
            });
        });
    </script>
    
    <?php
    if (isset($_GET['feedback'])) {
        echo '<div class="error">';
        echo '<span class="material-symbols-outlined">shopping_cart</span>';
        echo '<div class="card_load_extreme_title">';
        echo '<h3>';
        echo $_GET['feedback'];
        echo '</h3>';
        echo '<button onclick="window.location.href=\'Cart.php\'">View Cart</button>';
        echo '</div>';
        echo '</div>';
    }
    ?>

</body>
</html>