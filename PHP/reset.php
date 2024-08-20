<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'connect.php';

// Second step verification
// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['code']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
        $email = $conn->real_escape_string($_POST['email']);
        $code = $conn->real_escape_string($_POST['code']);
        $password = $conn->real_escape_string($_POST['password']);
        $confirmPassword = $conn->real_escape_string($_POST['confirm_password']);

        if ($password === $confirmPassword) {
            // Retrieve the current password from the database
            $getCurrentPasswordQuery = "SELECT password FROM user WHERE email = ?";
            $getCurrentPasswordStmt = $conn->prepare($getCurrentPasswordQuery);
            $getCurrentPasswordStmt->bind_param("s", $email);
            $getCurrentPasswordStmt->execute();
            $currentPasswordResult = $getCurrentPasswordStmt->get_result();

            if ($currentPasswordResult->num_rows > 0) {
                $currentPasswordRow = $currentPasswordResult->fetch_assoc();
                $currentPasswordHash = $currentPasswordRow['password'];

                // Check if the new password matches the current password
                if (password_verify($password, $currentPasswordHash)) {
                    $feedback = 'New password cannot be the same as the old password.';
                } else {
                    $checkCodeQuery = "SELECT * FROM passwordreset WHERE email = ? AND reset_code = ? AND used = FALSE AND expiration_time > NOW()";
                    $checkStmt = $conn->prepare($checkCodeQuery);
                    $checkStmt->bind_param("ss", $email, $code);
                    $checkStmt->execute();
                    $result = $checkStmt->get_result();

                    if ($result->num_rows > 0) {
                        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                        $updatePasswordQuery = "UPDATE user SET password = ? WHERE email = ?";
                        $updateStmt = $conn->prepare($updatePasswordQuery);
                        $updateStmt->bind_param("ss", $hashedPassword, $email);
                        $updateStmt->execute();

                        if ($updateStmt->affected_rows > 0) {
                            $markCodeUsedQuery = "UPDATE passwordreset SET used = TRUE WHERE email = ? AND reset_code = ?";
                            $markStmt = $conn->prepare($markCodeUsedQuery);
                            $markStmt->bind_param("ss", $email, $code);
                            $markStmt->execute();

                            $feedback = 'Password reset successful. You can now login with your new password.';
                            header('Location: reset_password.php?feedback=' . urlencode($feedback));
                            exit();
                        } else {
                            $feedback = 'Failed to reset password.';
                        }
                    } else {
                        $feedback = 'Invalid verification code.';
                    }
                }
            } else {
                $feedback = 'Email not found.';
            }
        } else {
            $feedback = 'Passwords do not match.';
        }
    } else {
        $feedback = 'Missing required fields.';
    }
    header('Location: reset_password.php?feedback=' . urlencode($feedback));
    exit();
} else {
    $feedback = 'Method not allowed.';
    header('Location: reset_password.php?feedback=' . urlencode($feedback));
    exit();
}
?>