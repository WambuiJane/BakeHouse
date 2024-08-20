<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('location:adminlogin.php');
    exit();
    
}

include 'PHP/connect.php';

// Pagination setup
$results_per_page = 5; // Number of results per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Get the total number of results
$total_sql = "SELECT COUNT(*) FROM orders";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_row()[0];
$total_pages = ceil($total_rows / $results_per_page);

// Get the current page results
$sql = "SELECT * FROM orders LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $start_from, $results_per_page);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
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
            <li><a href="logout.php">Logout</a><span class="material-symbols-outlined">logout</span></li>
        </ul>
    </nav>
    <section>
        <div class="section_nav">
            <h2><span>Welcome Admin </span></br> What would you like to do today?</h2>
            <input type="text" placeholder="Search for orders" name="Search" id="Search">
        </div>

        <?php
        echo "<table>";
        echo "<tr>";
        echo "<th>Order ID</th>";
        echo "<th>PayPal Order ID</th>";
        echo "<th>Order Date</th>";
        echo "<th>Order Status</th>";
        echo "<th>Total</th>";
        echo "</tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Order_Id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Paypal_Id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['OrderStatus']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Price']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        // Generate pagination links
        echo '<div class="pagination">';
        if ($page > 1) {
            echo '<a href="?page=' . ($page - 1) . '">&laquo; Previous</a>';
        }
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == $page) {
                echo '<span class="current-page">' . $i . '</span>';
            } else {
                echo '<a href="?page=' . $i . '">' . $i . '</a>';
            }
        }
        if ($page < $total_pages) {
            echo '<a href="?page=' . ($page + 1) . '">Next &raquo;</a>';
        }
        echo '</div>';

        $conn->close();
        ?>
    </section>
</body>
</html>
