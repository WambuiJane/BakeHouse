<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/register.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        <div class="buttons">
            <a href="Dashboard.php">Home</a>
            <a href="signup.php">Sign Up</a>
            <a href="index.php">Login</a>
        </div>
    </div>
    <div class="main">
        <div class="image">
        </div>
        <form method="post" id="register-form" class="container" action="PHP/register.php">
            <h1>Welcome </h1>
            <p>Sign up to your account</p>
            <div class="input">
                <h1>Full Name</h1>
                <input type="text" name="fullname" style="font-family:Arial, FontAwesome;" placeholder="&#xf007;  Enter your full name" required>
            </div>
            <div class="input">
                <h1>Email</h1>
                <input type="email" name="email" style="font-family:Arial, FontAwesome;" placeholder="&#xf0e0;  Enter your Email" required>
            </div>
            <div id="phoneno" class="input">
                <h1>Phone Number</h1>
                <input type="tel" name="phone" style="font-family:Arial, FontAwesome;" placeholder="&#xf095;  Enter your Phone Number" required>
            </div>
            <div class="input">
                <h1>Password</h1>
                <input type="password" name="password" style="font-family:Arial, FontAwesome;" placeholder="&#xf023;  Enter your Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
            </div>
            <div class="submit">
                <button type="submit">REGISTER</button>
                <p>Already have an account? <a href="index.php" onclick="showLoginForm()">Login</a></p>
            </div>
        </form>
    </div>


    <script>
        function showLoginForm() {
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'flex';
        }
        //Validation of phone number
        document.getElementById('register-form').addEventListener('submit', function(event) {

            var phoneNumber = document.querySelector('input[name="phone"]').value;

            // This regex matches a phone number with 10 digits
            var regex = /^\d{10}$/;

            if (!regex.test(phoneNumber)) {
                event.preventDefault();
                alert('Invalid phone number. Please enter a 10-digit phone number.');
                return;
            }
            // Continue with form submission
        });
    </script>
</body>

</html>