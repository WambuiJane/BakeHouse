<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../css/Register.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RESET PASSWORD</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
</head>
<body>
    <div class="navbar">
        <h1>BAKE HOUSE</h1>
        <?php
            if (isset($_GET['feedback'])) {
                echo "<p>" . htmlspecialchars($_GET['feedback']) . "</p>";  
            }
        ?>
    </div>
    <div class="main">
        <div class="image">
        </div>
        <form method="post" id="reset-form" class="container" action="reset.php">
            <h1>Welcome</h1>
            <p>Reset your password</p>
            <div class="input">
                <input type="text" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input">
                <input type="text" name="code" placeholder="Enter Verification code" required>
            </div>
            <div class="input">
                <input type="password" name="password" placeholder="New Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
            </div>
            <div class="input">
                <input type="password" name="confirm_password" placeholder="Confirm Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
            </div>
            <div class="submit">
                <button type="submit">RESET PASSWORD</button>
            </div>
        </form>
    </div>
</body>
</html>