<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: user_dashboard.php");
    exit();
}

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "print";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$formattedCost = 0; // Initialize the variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $printType = $_POST['print_type'];
    $colorOption = $_POST['color_option'];
    $paperSize = $_POST['paper_size'];
    $paperMaterial = $_POST['paper_material'];
    $additionalServices = $_POST['additional_services'];
    $quantity = $_POST['quantity'];
    $deliveryAddress = $_POST['delivery_address'];
    $shopSelector = isset($_POST['shop_selector']) ? trim($_POST['shop_selector']) : '';
    $numberOfPages = $_POST['number_of_pages'];
    $userId = $_SESSION['username'];

    // Handle file upload
    $filePath = "";
    if (isset($_FILES['file_upload']) && $_FILES['file_upload']['error'] == 0) {
        $uploadsDir = 'uploads/';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }
        $fileName = basename($_FILES['file_upload']['name']);
        $filePath = $uploadsDir . $fileName;
        if (move_uploaded_file($_FILES['file_upload']['tmp_name'], $filePath)) {
            // Successful file upload
            echo "File uploaded successfully. File path: " . htmlspecialchars($filePath) . "<br>";
        } else {
            echo "Error uploading file.";
            exit();
        }
    } else {
        echo "No file uploaded or upload error.";
        exit(); // Exit if file upload fails
    }

    // Cost calculation logic
    $baseCost = 2.00; // Base cost per page in INR
    $colorCost = ($colorOption == 'color') ? 2.00 : 0; // Additional cost for color printing
    $materialCost = ($paperMaterial == 'premium') ? 1.00 : 0; // Additional cost for premium paper material
    $serviceCost = ($additionalServices == 'spiral_binding') ? 5.00 : 0; // Service cost for spiral binding
    
    // Total cost calculation in INR
    $totalCostINR = ($baseCost + $colorCost + $materialCost) * $numberOfPages * $quantity + $serviceCost;
    
    // Format cost for display
    $formattedCost = number_format($totalCostINR, 2);
    
    // Cost breakdown HTML with number of pages
    $costBreakdown = "
    <ul class='cost-breakdown'>
        <li>Number of pages: <span>" . $numberOfPages . "</span></li>
        <li>Base cost per page: <span>₹" . number_format($baseCost, 2) . "</span></li>
        <li>Color cost per page: <span>₹" . number_format($colorCost, 2) . "</span></li>
        <li>Material cost per page: <span>₹" . number_format($materialCost, 2) . "</span></li>
        <li>Service cost: <span>₹" . number_format($serviceCost, 2) . "</span></li>
        <li class='total'>Total cost: <span>₹" . $formattedCost . "</span></li>
    </ul>
    <div class='receipt-footer'>Thank you for choosing PRINTABLES</div>
    ";

    // Determine shop table based on selection
    $shopTable = "";
    switch ($shopSelector) {
        case 'shop_kattappana':
            $shopTable = "shop_kattappana";
            break;
        case 'shop_nedumkandam':
            $shopTable = "shop_nedumkandam";
            break;
        case 'shop_kumily':
            $shopTable = "shop_kumily";
            break;
        case 'shop_thodupuzha':
            $shopTable = "shop_thodupuzha";
            break;
        case 'shop_adimali':
            $shopTable = "shop_adimali";
            break;
        default:
            echo "Invalid shop selected: " . htmlspecialchars($shopSelector);
            exit;
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO $shopTable (username, print_type, color_option, paper_size, paper_material, additional_services, quantity, delivery_address, number_of_pages, file_path, total_cost, payment_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $paymentId = '';
    $stmt->bind_param("ssssssssisds", $userId, $printType, $colorOption, $paperSize, $paperMaterial, $additionalServices, $quantity, $deliveryAddress, $numberOfPages, $filePath, $totalCostINR, $paymentId);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Order placed successfully.";
    } else {
        echo "Error executing statement: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Job Cost Estimate</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 30px;
        }
        h2 {
            color: #333;
        }
        /* Cost Breakdown Styles - Printed Bill */
.cost-breakdown {
    list-style-type: none;
    padding: 20px;
    margin: 0;
    background-color: #fff; /* Paper white */
    border: 1px solid #ddd; /* Light border for realism */
    max-width: 400px; /* Limiting width like a real receipt */
    font-family: 'Courier New', Courier, monospace; /* Classic receipt font */
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); /* Light shadow for depth */
}

.cost-breakdown li {
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px dashed #999; /* Dashed line between items */
    font-size: 16px; /* Readable font size */
    color: #333; /* Standard receipt text color */
}

.cost-breakdown li:last-child {
    border-bottom: none; /* Remove border from the last item */
}

.cost-breakdown li span {
    float: right; /* Align the costs to the right */
}

.cost-breakdown li.total {
    font-weight: bold;
    font-size: 18px;
    padding-top: 12px;
    margin-top: 12px;
    border-top: 2px solid #333; /* Bold line above the total */
}

.receipt-header {
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
    font-size: 18px;
    border-bottom: 2px solid #333;
    padding-bottom: 10px;
}

.receipt-footer {
    text-align: center;
    font-size: 12px;
    margin-top: 20px;
    color: #999;
    border-top: 1px solid #ddd;
    padding-top: 10px;
}

        /* Payment Button Styles */
.pay-button {
    position: relative;
    background-color: #4CAF50; /* Primary button color */
    color: #fff; /* White text */
    border: none;
    padding: 15px 30px;
    font-size: 16px;
    border-radius: 50px; /* Rounded button */
    cursor: pointer;
    overflow: hidden;
    transition: background-color 0.3s ease, transform 0.2s ease; /* Smooth background and scale transitions */
}

.pay-button:hover {
    background-color: #45a049; /* Darker shade on hover */
    transform: scale(1.05); /* Slightly increase the size on hover */
}

.pay-button:active {
    transform: scale(0.95); /* Button shrinks slightly when clicked */
}

/* Rocket Animation */
.pay-button .rocket-icon {
    position: absolute;
    top: 50%;
    left: 15px;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    background-image: url('rocket.png'); /* Rocket icon (Use your own image URL) */
    background-size: contain;
    background-repeat: no-repeat;
    animation: fly 2s infinite ease-in-out; /* Continuous rocket animation */
}

@keyframes fly {
    0% {
        transform: translateY(0) rotate(0);
    }
    50% {
        transform: translateY(-10px) rotate(15deg); /* Rocket goes up slightly */
    }
    100% {
        transform: translateY(0) rotate(0);
    }
}

    </style>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        function payNow() {
            var options = {
                "key": "rzp_test_ZIFEZLdYSNBmAU", // Replace with your Razorpay key ID
                "amount": "<?php echo isset($totalCostINR) ? $totalCostINR * 100 : 0; ?>", // Amount in paise (1 INR = 100 paise)
                "currency": "INR",
                "name": "PRINTABLES",
                "description": "Print Job Payment",
                "image": "https://example.com/your_logo.png", // Replace with your logo URL
                "handler": function (response) {
                    var paymentId = response.razorpay_payment_id;
                    var shopSelector = "<?php echo isset($_POST['shop_selector']) ? $_POST['shop_selector'] : ''; ?>"; // Ensure shop_selector is POSTed
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "payment_success.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            window.location.href = "payment_success.php?payment_id=" + paymentId + "&shop_selector=" + shopSelector;
                        } else if (xhr.readyState === 4) {
                            console.error("Failed to submit payment details. Status: " + xhr.status);
                        }
                    };
                    xhr.send("payment_id=" + paymentId 
                             + "&user_id=<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>" 
                             + "&total_cost=<?php echo isset($formattedCost) ? $formattedCost : 0; ?>");
                },
                "prefill": {
                    "name": "<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>",
                    "email": "<?php echo isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'user@example.com'; ?>", // Adjusted to use session email if available
                    "contact": "<?php echo isset($_SESSION['user_contact']) ? $_SESSION['user_contact'] : '9999999999'; ?>" // Adjusted to use session contact if available
                },
                "notes": {
                    "address": "Print Job Delivery Address"
                },
                "theme": {
                    "color": "#007bff"
                }
            };
            var rzp1 = new Razorpay(options);
            rzp1.on('payment.failed', function (response){
                console.error("Payment failed. Reason: " + response.error.reason);
                alert("Payment failed. Please try again or use a different payment method.");
            });
            rzp1.open();
        }
    </script>
</head>
<body>
    
    <div class="container">
        <h2 class="text-center">Print Job Cost Estimate</h2>
        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            <div class="text-center">
    <?php echo isset($costBreakdown) ? $costBreakdown : ''; ?>
    <button class="pay-button" onclick="payNow()">
        <span class="rocket-icon"></span>
        Pay ₹<?php echo isset($formattedCost) ? $formattedCost : '0.00'; ?>
    </button>
</div>

        <?php else: ?>
            <p class="text-center">Please go back and fill out the form to get a cost estimate.</p>
        <?php endif; ?>
    </div>
</body>
</html>

</html>
