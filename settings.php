<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('location: index.php');
}

include 'PHP/connect.php';
$userEmail = $_SESSION['email'];

// Fetch current user data from the database
$sql = "SELECT * FROM user WHERE email = ?";
$stmt= $conn->prepare($sql);
$stmt->bind_param('s', $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $newEmail = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate input
    if ($password !== $confirmPassword) {
        $error = "Passwords do not match!";
    } else {
        // Update user data
        $updateSql = "UPDATE user SET fullname = ?, email = ?, phone = ?";
        if (!empty($password)) {
            // Hash the password if it's provided
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateSql .= ", password = ?";
        }
        $updateSql .= " WHERE email = ?";
        $stmt = $conn->prepare($updateSql);
        
        if (!empty($password)) {
            $stmt->bind_param('sssss', $fullname, $newEmail, $phone, $hashedPassword, $userEmail);
        } else {
            $stmt->bind_param('ssss', $fullname, $newEmail, $phone, $userEmail);
        }
        
        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
            // Update session email if email was changed
            if ($newEmail !== $userEmail) {
                $_SESSION['email'] = $newEmail;
            }
        } else {
            $error = "Failed to update profile.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="css/settings.css">
</head>

<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <?php
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        if (isset($success)) {
            echo "<p class='success'>$success</p>";
        }
        ?>
        <form method="post" action="">
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>

            <label for="password">New Password:</label>
            <input type="password" id="password" name="password">

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password">

            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>

</html>