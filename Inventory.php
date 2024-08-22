<?php
session_start();
include 'PHP/connect.php';

if (!isset($_SESSION['admin'])) {
    header('location:adminlogin.php');
    exit();
}

// Get product page results
$sql = "SELECT * FROM products";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Products</title>
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
            <li><a href="AdminOrders.php.">Orders</a><span class="material-symbols-outlined">shopping_cart</span></li>
            <li><a href="Customers.php">Customers</a><span class="material-symbols-outlined">groups</span></li>
            <li><a href="Inventory.php.">Inventory</a><span class="material-symbols-outlined">inventory</span></li>
            <li><a href="logout.php">Logout</a><span class="material-symbols-outlined">logout</span></li>
        </ul>
    </nav>
    <section>
        <div class="section_nav">
            <h2><span>Welcome Admin </span><br> What would you like to do today?</h2>
            <input type="text" placeholder="Search for orders" name="Search" id="Search">
        </div>

        <h2 class="Display">Products Display <span class="material-symbols-outlined" onclick="addcontainer()" >add</span></h2>
        <?php
        echo "<table>";
        echo "<tr>";
        echo "<th>Image</th>";
        echo "<th>Product Name</th>";
        echo "<th>Price</th>";
        echo "<th>Quantity</th>";
        echo "<th>Category</th>";
        echo "<th>Actions</th>";
        echo "</tr>";


        while ($row = $result->fetch_assoc()) {
            $sql = "SELECT name FROM categories WHERE id = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $row['category_id']);
            $stmt->execute();
            $result2 = $stmt->get_result();
            $category = $result2->fetch_assoc();


            echo "<tr>";
            echo "<td>";
            echo '<img src="images/' . htmlspecialchars($row["image"]) . '" alt="' . htmlspecialchars($row["name"]) . '">';
            echo "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['price']) . "</td>";  
            echo "<td>" . htmlspecialchars($row['Quantity']) . "</td>";
            echo "<td>" . htmlspecialchars($category['name']) . "</td>";
            echo "<td>";
            echo '<button onclick="editProduct(' . htmlspecialchars($row['id']) . ')">Edit</button>';
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        ?>

        <div class="addcontainer">
            <h2>Add Product</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <label for="name">Product Name</label>
                <input type="text" name="name" id="name" required>
                <label for="price">Price</label>
                <input type="number" name="price" id="price" required>
                <label for="category">Category</label>
                <select name="category" id="category">
                    <?php
                    $sql = "SELECT * FROM categories";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                    }
                    ?>
                </select>
                <label for="image">Image</label>
                <input type="file" name="image" id="image" accept="image/*" required>
                <button type="submit">Add Product</button>
            </form>
        </div>

        <!-- Edit Product Form -->
        <div id="editProductForm" style="display:none;">
            <h2>Edit Product</h2>
            <form action="PHP/UpdateTest.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="edit_id" id="edit_id">
                <label for="edit_name">Product Name</label>
                <input type="text" name="edit_name" id="edit_name" required>
                <label for="edit_price">Price</label>
                <input type="number" name="edit_price" id="edit_price" required>
                <label for="edit_quantity">Product Quantity</label>
                <input type="number" name="quantity" id="edit_quantity">
                <label for="edit_category">Category</label>
                <select name="edit_category" id="edit_category">
                    <?php
                    $sql = "SELECT * FROM categories";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                    }
                    ?>
                </select>
                <label for="edit_image">Image</label>
                <input type="file" name="edit_image" id="edit_image" accept="image/*">
                <button type="submit">Update Product</button>
            </form>
        </div>

        <script>
        function addcontainer() {
            var productcont=document.querySelector('.addcontainer');
            if(productcont.style.display=='flex'){
                productcont.style.display='none';

            } else {
                productcont.style.display='flex';
                document.querySelector('body').addEventListener('click', function(event){
                    if(event.target.className=='addcontainer'){
                        productcont.style.display='none';
                    }
                });
            }


        }

            function editProduct(id) {
                fetch('PHP/get_product.php?id=' + id)
                    .then(response => response.text())  // Use text() to check raw response first
                    .then(text => {
                        if (text.trim() === '') {
                            console.error('Empty response from server');
                            return;
                        }
                        try {
                            const data = JSON.parse(text); // Parse JSON manually
                            if (data.error) {
                                console.error(data.error);
                                alert('Error fetching product details');
                            } else {
                                document.getElementById('edit_id').value = data.id;
                                document.getElementById('edit_quantity').value = data.Quantity;
                                document.getElementById('edit_name').value = data.name;
                                document.getElementById('edit_price').value = data.price;
                                document.getElementById('edit_category').value = data.category_id;
                                document.getElementById('editProductForm').style.display = 'flex';

                            }
                        } catch (e) {
                            console.error('Error parsing JSON:', e);
                        }
                    })
                    .catch(error => console.error('Error fetching product details:', error));
                
            }


            function deleteProduct(id) {
                if (confirm('Are you sure you want to delete this product?')) {
                    fetch('PHP/delete_product.php?id=' + id, { method: 'POST' })
                        .then(response => response.text())
                        .then(data => {
                            alert(data);
                            location.reload();
                        })
                        .catch(error => console.error('Error deleting product:', error));
                }
            }
        </script>

        <?php
        // Logic for adding product
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name']) && isset($_POST['price']) && isset($_POST['category']) && isset($_FILES['image'])) {
            $name = $_POST['name'];
            $price = $_POST['price'];
            $category = $_POST['category'];
            $image = $_FILES['image']['name'];
            $target = "Images/" . basename($image);

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                // Retrieve new product ID
                $sql = "INSERT INTO products (name, price, category_id, image) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sdis", $name, $price, $category, $image);
                if ($stmt->execute()) {
                    $sql = "SELECT id FROM products WHERE name = ? AND price = ? AND category_id = ? AND image = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sdis", $name, $price, $category, $image);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    $productId = $row['id'];

                    //Insert new product into popularity table
                    $sql = "INSERT INTO popularity (product_id, no_of_clicks) VALUES (?, 0)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $productId);
                    $stmt->execute();
                    echo "Product added successfully";
                    echo "<meta http-equiv='refresh' content='0'>";
                    
                } else {
                    echo "Error: " . $stmt->error;
                }
            } else {
                echo "Failed to upload image.";
            }
        }
        ?>

    </section>
</body>
</html>