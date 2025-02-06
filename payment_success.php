<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: dashboard.php");
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

$paymentId = $_GET['payment_id'] ?? '';
$shopSelector = $_GET['shop_selector'] ?? ''; // Get shop selector value from GET parameters
$userId = $_SESSION['username'];

// Debugging statements
if (!$shopSelector) {
    echo "Shop not specified. Debug info: ";
    print_r($_GET);
    exit;
}

// Map shop selectors to actual table names
$shopTables = [
    'shop_kattappana' => 'shop_kattappana',
    'shop_nedumkandam' => 'shop_nedumkandam',
    'shop_kumily' => 'shop_kumily',
    'shop_thodupuzha' => 'shop_thodupuzha',
    'shop_adimali' => 'shop_adimali',
];

$shopTable = $shopTables[$shopSelector] ?? null;

if (!$shopTable) {
    die("Invalid shop selected.");
}

// Update payment ID in the relevant shop table
if ($paymentId) {
    $stmt = $conn->prepare("UPDATE $shopTable SET payment_id = ? WHERE username = ? AND payment_id = ''");
    $stmt->bind_param("ss", $paymentId, $userId);

    if ($stmt->execute()) {
        $message = "Payment successful. Your payment ID is: $paymentId";
    } else {
        $message = "Error updating payment ID: " . $stmt->error;
    }

    $stmt->close();
} else {
    $message = "Payment ID not received.";
}
if (isset($_POST['home-btn'])) {
    echo "<script>if (window.opener) { window.opener.location.href = 'dashboard.php'; window.close(); } else { window.location.href = 'dashboard.php'; }</script>";
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Animation Library -->
    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Arial', sans-serif;
        }

        .modal-content {
            border-radius: 15px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .home-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .home-button:hover {
            background-color: #0056b3;
            transform: scale(1.1);
        }

        /* Confetti styles */
        canvas {
            position: fixed;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 9999;
        }
    </style>
</head>
<body>

    <!-- Modal (Bootstrap Pop-up) -->
    <div class="modal show d-block" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-header">
                    <h5 class="modal-title text-success">Payment Successful</h5>
                </div>
                <div class="modal-body">
                    <p><?php echo $message; ?></p>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Canvas for confetti -->
    <canvas id="confetti"></canvas>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Confetti Script -->
    <script>
        (function() {
            var confettiCanvas = document.getElementById('confetti');
            var confettiCtx = confettiCanvas.getContext('2d');
            var confettiPieces = [];
            var numPieces = 100;

            confettiCanvas.width = window.innerWidth;
            confettiCanvas.height = window.innerHeight;

            // Generate random confetti pieces
            for (var i = 0; i < numPieces; i++) {
                confettiPieces.push({
                    x: Math.random() * confettiCanvas.width,
                    y: Math.random() * confettiCanvas.height,
                    r: Math.random() * 5 + 2, // radius
                    d: Math.random() * 2 + 1, // density
                    color: "rgba(" + Math.floor(Math.random() * 255) + "," +
                                   Math.floor(Math.random() * 255) + "," +
                                   Math.floor(Math.random() * 255) + ", 1)",
                    tilt: Math.random() * 10 - 10,
                    tiltAngleIncrement: Math.random() * 0.07 + 0.05,
                    tiltAngle: Math.random() * Math.PI,
                });
            }

            function drawConfetti() {
                confettiCtx.clearRect(0, 0, confettiCanvas.width, confettiCanvas.height);

                confettiPieces.forEach(function(p) {
                    confettiCtx.beginPath();
                    confettiCtx.lineWidth = p.d;
                    confettiCtx.strokeStyle = p.color;
                    confettiCtx.moveTo(p.x + p.tilt + p.r, p.y);
                    confettiCtx.lineTo(p.x + p.tilt, p.y + p.r * 2);
                    confettiCtx.stroke();

                    p.x += Math.sin(p.tiltAngle) * 2;
                    p.y += Math.cos(p.tiltAngle + p.d) + 1 + p.d / 2;
                    p.tiltAngle += p.tiltAngleIncrement;

                    // Reset position if out of view
                    if (p.x > confettiCanvas.width || p.y > confettiCanvas.height) {
                        p.x = Math.random() * confettiCanvas.width;
                        p.y = -10;
                    }
                });

                requestAnimationFrame(drawConfetti);
            }

            drawConfetti();
        })();
    </script>
</body>
</html>
