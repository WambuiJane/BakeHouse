<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('location:admin_login.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer History</title>
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
            <li><a href="PHP/logout.php">Logout</a><span class="material-symbols-outlined">logout</span></li>
        </ul>
    </nav>
    <section>
        <div class="section_nav">
            <h2><span>Welcome Admin </span></br> What would you like to do today?</h2>
            <input type="text" placeholder="Search for orders" name="Search" id="Search">
        </div>
        <?php

        include 'PHP/connect.php';

        //display customer page results
        $sql = "SELECT * FROM user";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<table>";
        echo "<tr>";
        echo "<th>Customer ID</th>";
        echo "<th>Full Name</th>";
        echo "<th>Email</th>";
        echo "<th>Phone Number</th>";
        echo "</tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['UserID'] . "</td>";
            echo "<td>" . $row['fullname'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['phone'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        ?>




        <div class="pagination">
            <a href="#">&laquo;</a>
            <a href="#">1</a>
            <a href="#">2</a>
            <a href="#">3</a>
            <a href="#">&raquo;</a>
        </div>
    </section>
</body>

</html>