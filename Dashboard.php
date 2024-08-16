<?php
include 'PHP/session_check.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/Dashboard.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
        rel="stylesheet">
    <style>
        /* Ensure the dropdown menu is hidden by default */
        .dropdown-menu {
            display: none;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="nav-links">
            <h1>BAKE HOUSE</h1>
            <a href="#hero">Home</a>
            <a href="#about-us">About Us</a>
            <a href="Order.php">Order</a>
        </div>
        <div class="nav-logos" <?php echo $display; ?>>
            <span class="material-symbols-outlined" onclick="window.location.href='search.php'">search</span>
            <span class="material-symbols-outlined" onclick="window.location.href='Cart.php'">shopping_cart</span>
            <span class="material-symbols-outlined" onclick="toggleDropdown()">account_circle</span>
            <div class="dropdown-menu" id="dropdown-menu">
                <a href="settings.php">Profile</a>
                <a href="PHP/logout.php">Log Out</a>
            </div>
        </div>
        <div class="button" <?php echo $button; ?>>
            <button onclick="window.location.href='index.php'">Log In</button>
        </div>
    </div>

    <section id="hero">
        <div class="hero-text">
            <h1>Baked with passion,<br /> served with love</h1>
            <p>Discover the artistry of indulgence at BakeHouse, let us elevate your taste experience with every bite.</p>
            <div class="search">
                <label class="label">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" class="input" placeholder="What cake are you craving?" />
                </label>
                <button type="submit">Search</button>
            </div>
        </div>
        <div class="hero-image"></div>
    </section>

    <section id="recommendation">
        <p>POPULAR FLAVORS</p>
        <h1>Popular flavors for you</h1>
        <div class="carousel" id="popular-cakes">
            <?php
            include 'PHP/connect.php';

            // SQL query to select the three most popular items based on no_of_clicks
            $sql = "SELECT * FROM popularity ORDER BY no_of_clicks DESC LIMIT 3";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();

            // Fetch the data and display the popular items
            while ($row = $result->fetch_assoc()) {
                $sql = "SELECT * FROM products WHERE id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $row['product_id']);
                $stmt->execute();
                $productResult = $stmt->get_result();

                while ($product = $productResult->fetch_assoc()) {
                    echo "<div class='carousel-item'>";
                    echo "<div class='card'>";
                    echo "<img src='Images/" . $product["image"] . "' alt='" . $product["name"] . "'>";
                    echo "<h3>" . $product["name"] . "</h3>";
                    echo "<p>$" . $product["price"] . "</p>";
                    echo "</div>";
                    echo "</div>";
                }
            }
            ?>
        </div>
        <div class="view-more" <?php echo $display; ?>>
            <a href="gallery.php">View More</a>
        </div>
    </section>

    <section id="about-us">
        <div class="about-top">
            <h1>About Us</h1>
            <p><a href="#hero">Home</a> . <a href="#about-us">About Us</a></p>
        </div>
        <div class="about-middle">
            <img src="Images/blake-carpenter-7sMvmabgXAo-unsplash.jpg" alt="Profile Picture">
            <div class="about-text">
                <h1>Founder's Remarks</h1>
                <p>At BakeHouse, we believe that every cake has a story to tell. Our story began in 2010 when we opened
                    our first bakery in Nairobi. We have since grown to become one of the leading bakeries in the
                    country. Our cakes are made with the finest ingredients and baked with love. We take pride in
                    creating delicious cakes that are perfect for any occasion. Whether you are celebrating a birthday,
                    wedding, or any other special event, we have the perfect cake for you. Our team of talented bakers
                    and decorators work hard to create beautiful and delicious cakes that will delight your taste buds
                    and impress your guests. We are committed to providing our customers with the best possible
                    experience and look forward to serving you soon.</p>
                <div class="card">
                    <img src="Images/photo-1571115177098-24ec42ed204d.avif" alt="Profile Image" class="card_load">
                    <div class="card_load_extreme_title">
                        <div class="h1">Jane Karuga</div>
                        <div class="p">Founder and CEO</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="about-bottom">
            <p>Craving something unique? Design your perfect treat with a custom order tailored just for you!</p>
            <a href="Order.php">Custom Order</a>
        </div>
    </section>

    <!-- JS function to toggle the dropdown menu -->
    <script>
        function toggleDropdown() {
            var dropdownMenu = document.getElementById("dropdown-menu");
            dropdownMenu.style.display = (dropdownMenu.style.display === "block") ? "none" : "block";
        }
    </script>
</body>
</html>
