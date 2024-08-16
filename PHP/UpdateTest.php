<?php

include 'PHP/connect.php';
        // Logic for updating product
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_id']) && isset($_POST['edit_name']) && isset($_POST['edit_price']) && isset($_POST['edit_category'])) {
            $id = $_POST['edit_id'];
            $name = $_POST['edit_name'];
            $price = $_POST['edit_price'];
            $category = $_POST['edit_category'];
            $image = $_FILES['edit_image']['name'];
            $target = "Images/" . basename($image);

            if ($image) {
                $sql = "UPDATE products SET name = ?, price = ?, category_id = ?, image = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sdiss", $name, $price, $category, $image, $id);
            } else {
                $sql = "UPDATE products SET name = ?, price = ?, category_id = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sdis", $name, $price, $category, $id);
            }

            if ($stmt->execute()) {
                echo "Product updated successfully";
                if ($image) {
                    move_uploaded_file($_FILES['edit_image']['tmp_name'], $target);
                }
                header('Location: Inventory.php');
            } else {
                echo "Error: " . $stmt->error;
            }
        }
        ?>
