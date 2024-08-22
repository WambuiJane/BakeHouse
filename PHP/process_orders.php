<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: ../adminlogin.php');
    exit();
}

if (isset($_POST['selected_orders']) && is_array($_POST['selected_orders'])) {
    require_once 'connect.php';

    foreach ($_POST['selected_orders'] as $order_id) {
        $sql = "UPDATE orders SET OrderStatus = 'PROCESSED' WHERE Order_Id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $order_id);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Error preparing statement for order ID: $order_id<br>";
        }
    }
    
    $conn->close();
    header('Location: ../admin.php?status=success');
    exit();
} else {
    header('Location: ../admin.php?status=error');
    exit();
}
