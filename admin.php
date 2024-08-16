<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: adminlogin.php');
    exit();
}
//fetch num rows for customers,orders,inventory and pending orders
require_once 'PHP/connect.php';
$sql = "SELECT * FROM user";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$customers = $result->num_rows;


$sql = "SELECT * FROM orders";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->num_rows;

$sql = "SELECT * FROM products";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->num_rows;

$sql = "SELECT * FROM orders WHERE OrderStatus = 'PENDING'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$pending = $result->num_rows;

$sql = "SELECT * FROM orders WHERE OrderStatus = 'COMPLETED'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$completed = $result->num_rows;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <script src='fullcalendar-6.1.15/dist/index.global.min.js'></script>
    <script src='js/calendar.js'></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <nav>
        <div class="card">
            <img src="Images/photo-1571115177098-24ec42ed204d.avif" alt="Profile Image" class="card_load">
            <div class="card_load_extreme_title">
                <?php
              echo $_SESSION['admin'];
                ?>
            </div>
        </div>
        <ul>
            <li><a href="admin.php">Home</a><span class="material-symbols-outlined">home</span></li>
            <li><a href="AdminOrders.php">Orders</a><span class="material-symbols-outlined">shopping_cart</span></li>
            <li><a href="Customers.php">Customers</a><span class="material-symbols-outlined">groups</span></li>
            <li><a href="Inventory.php">Inventory</a><span class="material-symbols-outlined">inventory</span></li>
            <li><a href="PHP/logout.php">Logout</a><span class="material-symbols-outlined">logout</span></li>
        </ul>
    </nav>
    <section>
        <div class="section_nav">
            <h2><span>Welcome Admin </span></br> What would you like to do today?</h2>
            <input type="text" placeholder="Search for orders" name="Search" id="Search">
        </div>
        
        <div class="section_top">
            <div id="calendar"></div>
            <div class="pending">
                <h2>Pending Orders</h2>
                <p><?php echo $pending; ?></p>
            </div>
        </div>
        <div class="section_bottom">
            <div class="catalogue">
                <div class="orders">
                    <h2>Orders</h2>
                    <p><?php echo $orders; ?></p>
                </div>
                <div class="products">
                    <h2>Products</h2>
                    <p><?php echo $products; ?></p>
                </div>
            </div>
            <div class="catalogue_2">
                <div class="recent">
                    <h2>Completed Orders</h2>
                    <p><?php echo $completed; ?></p>  
                </div>
            </div>
        </div>
    </section>

</body>
</html>