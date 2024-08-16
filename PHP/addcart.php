<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['customCart'])) {
    $_SESSION['customCart'] = [];
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'add':
        $productId = $_POST['productId'];
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]++;
        } else {
            $_SESSION['cart'][$productId] = 1;
        }
        // Increase popularity of product
        $sql="SELECT * FROM popularity WHERE product_id = $productId";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $popularity = $row['no_of_clicks'] + 1;
            $sql = "UPDATE popularity SET no_of_clicks = $popularity WHERE product_id = $productId";
            $conn->query($sql);
        } else {
            $sql = "INSERT INTO popularity (product_id, no_of_clicks) VALUES ($productId, 1)";
            $conn->query($sql);
        }
        $feedback = "Product added to cart";
        header('Location: ../gallery.php?feedback=' . $feedback);

        break;

    case 'increase':
        $productId = $_POST['productId'];
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]++;
        }
        break;

    case 'decrease':
        $productId = $_POST['productId'];
        if (isset($_SESSION['cart'][$productId])) {
            if ($_SESSION['cart'][$productId] > 1) {
                $_SESSION['cart'][$productId]--;
            } else {
                unset($_SESSION['cart'][$productId]);
            }
        }
        break;

    case 'remove':
        $productId = $_POST['productId'];
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
        break;

    case 'remove_custom':
        $customCakeId = $_POST['customCakeId'];
        if (isset($_SESSION['customCart'][$customCakeId])) {
            unset($_SESSION['customCart'][$customCakeId]);
        }
        break;

    case 'clear':
        $_SESSION['cart'] = [];
        $_SESSION['customCart'] = [];
        break;
}
exit();
?>