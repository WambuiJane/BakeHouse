<?php
session_start();
require_once 'PHP/connect.php';

function getProduct($productId) {
    global $conn;

    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Database query error: " . $conn->error);
        return null;
    }
    $stmt->bind_param("s", $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;

// Calculate total and prepare cart items for display
$cartItems = [];
foreach ($cart as $productId => $quantity) {
    $product = getProduct($productId);
    if ($product) {
        $price = $product['price'];
        $subtotal = $price * $quantity;
        $total += $subtotal;
        $cartItems[] = [
            'id' => $productId,
            'name' => $product['name'],
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $subtotal
        ];
    }
}

// Custom order cart
$customCart = isset($_SESSION['customCart']) ? $_SESSION['customCart'] : [];
foreach ($customCart as $customCakeId => $customCake) {
    $price = $customCake['price'];
    $subtotal = $price;
    $total += $subtotal;
    $cartItems[] = [
        'id' => $customCakeId,
        'name' => "Custom Cake #$customCakeId",
        'quantity' => 1,
        'price' => $price,
        'subtotal' => $subtotal
    ];
}

$formattedTotal = number_format($total, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Page</title>
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
</head>
<body>
<div class="navbar">
        <div class="nav-links">
            <h1>BAKE HOUSE</h1>
            <a href="#hero">Home</a>
            <a href="#about-us">About Us</a>
            <a href="Order.php">Order</a>
        </div>
        <div class="nav-logos">
            <span class="material-symbols-outlined" onclick="window.location.href='search.php'">search</span>
            <span class="material-symbols-outlined" onclick="window.location.href='Cart.php'">shopping_cart</span>
            <span class="material-symbols-outlined" onclick="toggleDropdown()">account_circle</span>
            <div class="dropdown-menu" id="dropdown-menu">
                <a href="settings.php">Profile <span class="material-symbols-outlined">person</span></a>
                <a href="PHP/logout.php">Log Out <span class="material-symbols-outlined">logout</span></a>
            </div>
        </div>
    </div>
    <h2>Checkout</h2>
    <div class="Product-Summary">
        <table>
            <tr><th>Product Name</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr>
            <?php foreach ($cartItems as $item): ?>
                <tr>
                    <td>
                    <?php 
                    //    if custom cake
                    if (strpos($item['name'], 'Custom Cake') !== false) {
                        echo "<img src='Images/Custom Cake.avif'>";
                        // continue;
                    }
                    else{
                        echo "<img src='Images/" . htmlspecialchars($product['image']) . "' alt='" . htmlspecialchars($product['name']) . "'>";
                    }
                    ?>

                    <?= htmlspecialchars($item['name']) ?>
                    </td>
                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td>$<?= number_format($item['subtotal'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan='3'>Total:</td><td>$<?= htmlspecialchars($formattedTotal) ?></td>
            </tr>
            <tr>
                <td><button onclick="window.location.href='Cart.php'">Back to Cart</button></td>
                <td>
                    <form action="M-PESA/payment.php" method="post">
                        <input type="hidden" name="total" value="<?= $formattedTotal ?>">
                        <button type="submit">Checkout</button>
                    </form>
                </td>
            </tr>
        </table>
    </div>

    

    

    <script src="https://www.paypal.com/sdk/js?client-id=Aazr50ohh5iut8PVxFMB3PRER5U5q3i06V_RxfZCcDzNLztgc6im-mDtkg0GMsJdnIZuf0BYM_f_VxFV&components=buttons"></script>
    <script>
        function toggleDropdown() {
            var dropdownMenu = document.getElementById("dropdown-menu");
            dropdownMenu.style.display = (dropdownMenu.style.display === "block") ? "none" : "block";
        }
        
        paypal.Buttons({
            createOrder: function(data, actions) {
                return fetch('PHP/create-order.php', {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        total: '<?= $formattedTotal ?>'
                    })
                }).then(function(res) {
                    return res.json();
                }).then(function(orderData) {
                    return orderData.id;
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    console.log('Order ID:', data.orderID);
                    window.location.href = 'PHP/process_checkout.php?token=' + data.orderID;
                });
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>
