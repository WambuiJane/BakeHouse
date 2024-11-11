<?php
include 'PHP/session_check.php';

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
// Function to calculate price for custom cake
function calculateCustomCakePrice($size, $flavors, $additionalOptions = []) {
    // Base prices for different cake sizes
    $basePrices = [
        'small' => 20.00,
        'medium' => 30.00,
        'large' => 40.00
    ];

    // Price modifiers for different flavors (percentage increase)
    $flavorModifiers = [
        1 => 0,    // Vanilla (base flavor, no extra charge)
        2 => 5,   // Chocolate (5% price increase)
        3 => 10,   // Strawberry (10% price increase)
        4 => 15,   // Red Velvet (15% price increase)
        5 => 15,
        6 => 20,
        7 => 20,
        8 => 15,
        9 => 10,
        10 => 20
    ];

    // Start with the base price for the selected size
    $price = $basePrices[$size];

    // Calculate the weighted average of flavor modifiers
    $flavorModifier = 0;
    foreach ($flavors as $flavorId => $percentage) {
        $flavorModifier += ($flavorModifiers[$flavorId] * $percentage / 100);
    }

    // Apply the flavor modifier to the base price
    $price *= (1 + $flavorModifier / 100);

    // Apply additional options
    foreach ($additionalOptions as $option => $value) {
        switch ($option) {
            case 'layers':
                // Increase price for additional layers
                $extraLayers = $value - 1; // Assuming 1 layer is standard
                $price += ($extraLayers * 5); // $5 per extra layer
                break;
            case 'frosting':
                // Different frostings might have different prices
                $frostingPrices = [
                    'buttercream' => 0,    // Standard frosting
                    'fondant' => 10,       // Premium frosting
                    'whipped' => 5         // Mid-range frosting
                ];
                $price += $frostingPrices[$value];
                break;
            case 'decoration':
                // Charge for cake decoration
                $decorationPrices = [
                    'simple' => 0,
                    'moderate' => 10,
                    'elaborate' => 20
                ];
                $price += $decorationPrices[$value];
                break;
            // Add more options as needed
        }
    }

    // Round to 2 decimal places
    return round($price, 2);
}

// Example usage in order processing logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["place_custom_order"])) {
    $cakeSize = $_POST["cake_size"];
    $layers = $_POST["layers"];
    $frosting = $_POST["frosting"];
    $decoration = $_POST["decoration"];
    $flavors = $_POST['flavors'];
    

    $savedFlavors = [];
    // Loop through the submitted flavors and save them
    foreach ($flavors as $id => $percentage) {
        $savedFlavors[$id] = $percentage;
    }

    $additionalOptions = [
        'layers' => $layers,
        'frosting' => $frosting,
        'decoration' => $decoration
    ];

    $price = calculateCustomCakePrice($cakeSize, $flavors, $additionalOptions);

//add to cart
    if (!isset($_SESSION['customCart'])) {
        $_SESSION['customCart'] = [];
    }
    $customCakeId = uniqid(); // Unique identifier for the custom cake
    $_SESSION['customCart'][$customCakeId] = $price;
    //add all details
    $_SESSION['customCart'][$customCakeId] = [
        'size' => $cakeSize,
        'flavors' => $savedFlavors,
        'layers' => $layers,
        'frosting' => $frosting,
        'decoration' => $decoration,
        'price' => $price
    ];

    echo "Custom cake added to cart. Price: $" . number_format($price, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Cake Order</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200">  
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link href="css/Order.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="navbar">
        <div class="nav-links">
            <h1>BAKE HOUSE</h1>
            <a href="Dashboard.php">Home</a>
            <a href="Dashboard.php #about-us">About Us</a>
            <a href="#">Order</a>
        </div>
        <div class="nav-logos" <?php echo $display; ?>>
            <span class="material-symbols-outlined" onclick="window.location.href='search.php'">search</span>
            <span class="material-symbols-outlined" onclick="window.location.href='Cart.php'">shopping_cart</span>
            <span class="material-symbols-outlined" onclick="toggleDropdown()">account_circle</span>
            <div class="dropdown-menu" id="dropdown-menu">
                <a href="settings.php">Profile <span class="material-symbols-outlined">person</span></a>
                <a href="PHP/logout.php">Log Out <span class="material-symbols-outlined">logout</span></a>
            </div>
        </div>
    </div>
   
    <form method="post" id="customOrderForm">
        <h2>Custom Cake Order</h2>
        <label for="cake_size">Cake Size:</label>
        <select name="cake_size" id="cake_size">
            <option value="small">Small</option>
            <option value="medium">Medium</option>
            <option value="large">Large</option>
        </select>

        <h3>Flavors:</h3>
        <div id="flavorSelections">
            <?php
            $flavors = [
                1 => "Vanilla",
                2 => "Chocolate",
                3 => "Strawberry",
                4 => "Red Velvet",
                5 => "Lemon",
                6 => "Blueberry",
                7 => "Coconut",
                8 => "Carrot",
                9 => "Coffee",
                10 => "Pistachio"
            ];
            foreach ($flavors as $id => $name) {
                echo "<div>";
                echo "<label>{$name}(%)</label>";
                echo "<input type='number' name='flavors[{$id}]' min='0' max='100' value='0'>";
                echo "</div>";
            }
            ?>
        </div>

        <label for="layers">Number of Layers:</label>
        <input type="number" name="layers" id="layers" min="1" max="5" value="1">

        <label for="frosting">Frosting Type:</label>
        <select name="frosting" id="frosting">
            <option value="buttercream">Buttercream</option>
            <option value="fondant">Fondant</option>
            <option value="whipped">Whipped Cream</option>
        </select>

        <label for="decoration">Decoration Level:</label>
        <select name="decoration" id="decoration">
            <option value="simple">Simple</option>
            <option value="moderate">Moderate</option>
            <option value="elaborate">Elaborate</option>
        </select>

        <input type="submit" name="place_custom_order" value="Add to Cart">
    </form>

    <script>
    $(document).ready(function() {
        $('#customOrderForm').on('input', 'input[type="number"]', function() {
            var total = 0;
            $('#flavorSelections input[type="number"]').each(function() {
                total += parseInt($(this).val()) || 0;
            });
            if (total > 100) {
                alert('Total percentage cannot exceed 100%');
                $(this).val(0);
            }
        });

        $('#customOrderForm').submit(function(e) {
            var total = 0;
            $('#flavorSelections input[type="number"]').each(function() {
                total += parseInt($(this).val()) || 0;
            });
            if (total != 100) {
                e.preventDefault();
                alert('Total percentage must equal 100%');
            }
        });
    });

    // JS function to toggle the dropdown menu
        function toggleDropdown() {
            var dropdownMenu = document.getElementById("dropdown-menu");
            dropdownMenu.style.display = (dropdownMenu.style.display === "block") ? "none" : "block";
        }
    </script>
</body>
</html>