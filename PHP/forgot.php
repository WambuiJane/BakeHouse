<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/register.css"> 
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
</head>
<body>
    <div class="navbar">
        <h1>BAKE HOUSE</h1>
        <p>
        <?php
            if (isset($_GET['error'])) {
                $feedback = urldecode($_GET['error']);
                echo "<p>" . htmlspecialchars($feedback) . "</p>";
            }
        ?>
        </p>
    </div>
    <div class="main">
        <div class="image">
        </div>
    <form id="login-form" class="container" action="Login.php">
    <h1>Welcome Back</h1>
    <p>Log in your account</p>
    <div class="input">
        <h1>Email</h1>
        <input type="email" name="email" placeholder="Enter your Email" required>
    </div>
    <div class="submit">
        <button type="submit">LOGIN</button>
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </div> 
</form>
</div>
</body>
</html>