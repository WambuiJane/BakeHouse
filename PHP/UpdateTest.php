<?php

include 'connect.php';
        // Logic for updating product
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id']) && isset($_POST['edit_name']) && isset($_POST['edit_price']) && isset($_POST['edit_category']) && isset($_POST['quantity'])) {
            $id = $_POST['edit_id'];
            $quantity = $_POST['quantity'];
            $name = $_POST['edit_name'];
            $price = $_POST['edit_price'];
            $category = $_POST['edit_category'];
            $image = $_FILES['edit_image']['name'];
            $target = "Images/" . basename($image);

            if ($image) {
                $sql = "UPDATE products SET name = ?, price = ?, category_id = ?, Quantity = ?, image = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sdiiss", $name, $price, $category,  $quantity, $image, $id);
            } else {
                $sql = "UPDATE products SET name = ?, price = ?, category_id = ?, Quantity = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sdiis", $name, $price, $category, $quantity, $id);
            }

            if ($stmt->execute()) {
                echo "Product updated successfully";
                if ($image) {
                    move_uploaded_file($_FILES['edit_image']['tmp_name'], $target);
                }
                header('Location: ../Inventory.php? message=Product updated successfully');
            } else {
                echo "Error: " . $stmt->error;
            }
        }
        ?>
