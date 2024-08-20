<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/cart.css">
</head>

<body>
    <div class="navbar">
        <div class="nav-links">
            <h1>BAKE HOUSE</h1>
            <a href="Dashboard.php#hero">Home</a>
            <a href="Dashboard.php#about">About Us</a>
            <a href="Order.php">Order</a>
        </div>
        <div class="nav-logos">
            <span class="material-symbols-outlined">search</span>
            <span class="material-symbols-outlined">shopping_cart</span>
            <span class="material-symbols-outlined" id="login-btn">account_circle</span>
        </div>
    </div>
    <div class="container">
        <h2>Your Cart</h2>
        <?php
        session_start();
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        require_once "PHP/connect.php";

        // Function to get product details by ID
        function getProduct($productId)
        {
            global $conn;
            $sql = "SELECT * FROM products WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Database query error: " . $conn->error);
            }
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->num_rows > 0 ? $result->fetch_assoc() : null;
        }

        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $customCart = isset($_SESSION['customCart']) ? $_SESSION['customCart'] : [];
        $total = 0;

        if (empty($cart) && empty($customCart)) {
            echo "<p>Your cart is empty.</p>";
            echo '<button onclick="window.location.href=\'gallery.php\'">Gallery</button>';
            return;
        }

        echo "<table>";
        echo "<tr><th>Product Name</th><th>Details</th><th>Price</th><th>Subtotal</th><th>Actions</th></tr>";

        // Display custom cakes
        foreach ($customCart as $customCakeId => $customCake) {
            $size = $customCake['size'];
            $flavors = $customCake['flavors'];
            $layers = $customCake['layers'];
            $frosting = $customCake['frosting'];
            $decoration = $customCake['decoration'];
            $price = $customCake['price'];

            $subtotal = $price;
            $total += $subtotal;

            $flavornames = [
                1 => "Vanilla",
                2 => "Chocolate",
                3 => "Strawberry",
                4 => "Red Velvet",
                5 => "Lemon",
                6 => "Blueberry",
                7 => "Coconut",
                8 => "Carrot",
                9 => "Coffee",
                10 => "Pistachio"
            ];
            

            echo "<tr>";
            echo "<td>";
            echo "<img src='Images/custom cake.avif' alt='Custom Cake'>";
            echo "Custom Cake #$customCakeId";
            echo "</td>";
            echo "<td>
                    <ul>
                        <li>Size: {$size}</li>
                        <li>Flavors: <ul>";
            foreach ($flavors as $flavorId => $flavorPercentage) {
                if ($flavorPercentage != 0) {
                    foreach ($flavornames as $id => $name) {
                        if ($id == $flavorId) {
                            echo "<li>{$flavorPercentage}% " . htmlspecialchars($name) . "</li>";
                            break;
                        }
                    }
                }
            }
            echo "</ul></li>
                        <li>Layers: {$layers}</li>
                        <li>Frosting: {$frosting}</li>
                        <li>Decoration: {$decoration}</li>
                    </ul>
                  </td>";
            echo "<td>$" . number_format($price, 2) . "</td>";
            echo "<td>$" . number_format($subtotal, 2) . "</td>";
            echo "<td>
    <form method='post' action='PHP/addcart.php' style='display:inline;'>
        <input type='hidden' name='customCakeId' value='{$customCakeId}'>
        <input type='hidden' name='action' value='remove_custom'>
        <button type='submit'>Remove</button>
    </form>
        </td>";
        }

        // Display regular products
        foreach ($cart as $productId => $quantity) {
            $product = getProduct($productId);
            if ($product) {
                $price = $product['price'];
                $subtotal = $price * $quantity;
                $total += $subtotal;

                echo "<tr>";
                echo "<td><img src='Images/" . htmlspecialchars($product['image']). "' alt='" . htmlspecialchars($product['name']) . "'> {$product['name']}</td>";
                echo "<td>
                        <form method='post' action='PHP/addcart.php' style='display:inline;'>
                            <input type='hidden' name='productId' value='{$productId}'>
                            <input type='hidden' name='action' value='decrease'>
                            <button type='submit'>-</button>
                        </form>
                        {$quantity}
                        <form method='post' action='PHP/addcart.php' style='display:inline;'>
                            <input type='hidden' name='productId' value='{$productId}'>
                            <input type='hidden' name='action' value='increase'>
                            <button type='submit'>+</button>
                        </form>
                      </td>";
                echo "<td>$" . number_format($price, 2) . "</td>";
                echo "<td>$" . number_format($subtotal, 2) . "</td>";
                echo "<td>
                        <form method='post' action='PHP/addcart.php' style='display:inline;'>
                            <input type='hidden' name='productId' value='{$productId}'>
                            <input type='hidden' name='action' value='remove'>
                            <button type='submit'>Remove</button>
                        </form>
                      </td>";
                echo "</tr>";
            } else {
                echo "<tr>";
                echo "<td colspan='5'>Product not found (ID: $productId)</td>";
                echo "</tr>";
            }
        }

        // Display total
        echo "<tr><td>Total: $" . number_format($total, 2) . "</td></tr>";

        // Action buttons
        echo "<tr>";
        echo '<td><button onclick="window.location.href=\'gallery.php\'">Gallery</button></td>';
        echo "<td></td><td></td>";
        echo "<td>
                <form method='post' action='PHP/addcart.php'>
                    <input type='hidden' name='action' value='clear'>
                    <button type='submit'>Clear Cart</button>
                </form>
              </td>";
        echo "<td>
                <form method='post' action='checkout.php'>
                    <input type='hidden' name='total' value='" . number_format($total, 2) . "'>
                    <button type='submit'>Checkout</button>
                </form>
              </td>";
        echo "</tr>";
        echo "</table>";
        ?>
    </div>
</body>

</html>