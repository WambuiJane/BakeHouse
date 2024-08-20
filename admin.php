<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: adminlogin.php');
    exit();
}

// Database connection
require_once 'PHP/connect.php';

// Fetch number of customers
$sql = "SELECT * FROM user";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$customers = $result->num_rows;

// Fetch number of orders
$sql = "SELECT * FROM orders";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->num_rows;

// Fetch number of products
$sql = "SELECT * FROM products";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->num_rows;

// Fetch number of pending orders
$sql = "SELECT * FROM orders WHERE OrderStatus = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$pending = $result->num_rows;

// Fetch number of completed orders
$sql = "SELECT * FROM orders WHERE OrderStatus = 'PROCESSED'";
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
                <?php echo $_SESSION['admin']; ?>
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
            <h2><span>Welcome Admin </span><br> What would you like to do today?</h2>
            <input type="text" placeholder="Search for orders" name="Search" id="Search">
        </div>

        <div class="section_top">
            <div id="cards">
                <h1>Customers</h1>
                <h2><?php echo $customers; ?><p>Customers</p>
                </h2>
            </div>
            <div id="cards">
                <h1>Orders</h1>
                <h2><?php echo $orders; ?><p>Orders</p>
                </h2>
            </div>
            <div id="cards">
                <h1>Products</h1>
                <h2><?php echo $products; ?><p>Products</p>
                </h2>
            </div>
            <div id="cards">
                <h1>Completed Orders</h1>
                <h2><?php echo $completed; ?><p>Orders</p>
            </div>
        </div>
        <div class="section_bottom">
            <form class="pending" action='PHP/process_orders.php' method='post'>
                <h2>Pending Orders</h2>
                <?php
                // Resetting and fetching pending orders for the list
                $sql = "SELECT * FROM orders WHERE OrderStatus = 'pending'";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($pending > 0) {
                    echo "<table>";
                    $m = 1;
                    while ($row = $result->fetch_assoc() and $m <= 3) {
                        // Fetch customer name
                        $sql_user = "SELECT fullname FROM user WHERE UserID = ?";
                        $stmt_user = $conn->prepare($sql_user);
                        $stmt_user->bind_param('i', $row['User_Id']);
                        $stmt_user->execute();
                        $result_user = $stmt_user->get_result();
                        $user = $result_user->fetch_assoc();
                        $username = $user['fullname'];

                        // Fetch product name
                        $sql_product = "SELECT name FROM products WHERE id = ?";
                        $stmt_product = $conn->prepare($sql_product);
                        $stmt_product->bind_param('i', $row['Cake_Id']);
                        $stmt_product->execute();
                        $result_product = $stmt_product->get_result();
                        $product = $result_product->fetch_assoc();
                        $name = $product['name'];

                        echo "<tr>";
                        echo "<td><input type='checkbox' name='selected_orders[]' value='" . $row['Order_Id'] . "'></td>";
                        echo "<td>" . $username . "</td>";
                        echo "<td>" . $name . "</td>";
                        echo "</tr>";
                        $m++;
                    }

                    echo "</table>";
                    echo "<input type='submit' value='Process Selected Orders'>";
                } else {
                    echo "No pending orders found.";
                }
                ?>
            </form>
            <div class="catalogue_2">
                <h2>Completed Orders</h2>
                <?php
                // Resetting and fetching completed orders for the list
                $sql = "SELECT * FROM orders WHERE OrderStatus = 'PROCESSED'";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($completed > 0) {
                    echo "<table>";
                    $n = 1;
                    while ($row = $result->fetch_assoc() and $n <= 3) {
                        $sql_user = "SELECT fullname FROM user WHERE UserID = ?";
                        $stmt_user = $conn->prepare($sql_user);
                        $stmt_user->bind_param('i', $row['User_Id']);
                        $stmt_user->execute();
                        $result_user = $stmt_user->get_result();
                        $user = $result_user->fetch_assoc();
                        $username = $user['fullname'];

                        $sql_product = "SELECT name FROM products WHERE id = ?";
                        $stmt_product = $conn->prepare($sql_product);
                        $stmt_product->bind_param('i', $row['Cake_Id']);
                        $stmt_product->execute();
                        $result_product = $stmt_product->get_result();
                        $product = $result_product->fetch_assoc();
                        $name = $product['name'];

                        echo "<tr>";
                        echo "<td>" . $username . "</td>";
                        echo "<td>" . $name . "</td>";
                        echo "</tr>";
                        $n++;
                    }

                    echo "</table>";
                } else {
                    echo "No completed orders found.";
                }
                ?>
            </div>
        </div>
    </section>

</body>

</html>