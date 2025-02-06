<?php
session_start();

// Ensure that the user is logged in as a shop owner
if (!isset($_SESSION['shopname'])) {
    header("Location: Owner_login.php");
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

$shopname = $_SESSION['shopname'];
$shopTable = "shop_" . strtolower(str_replace(' ', '_', $shopname));

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['status'];

    // Update the status
    $updateSql = "UPDATE $shopTable SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("si", $newStatus, $orderId);
    $stmt->execute();
    $stmt->close();
}

// Handle logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.html");
    exit();
}

// Handle file download request
if (isset($_GET['job_id']) && isset($_GET['shop_selector'])) {
    $jobId = $_GET['job_id'];
    $shopSelector = $_GET['shop_selector'];

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
            echo "Invalid shop selected.";
            exit();
    }

    // Retrieve file path from the database
    $stmt = $conn->prepare("SELECT file_path FROM $shopTable WHERE id = ?");
    $stmt->bind_param("i", $jobId);
    $stmt->execute();
    $stmt->bind_result($filePath);
    $stmt->fetch();
    $stmt->close();

    if ($filePath && file_exists($filePath)) {
        // Provide the file for download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        flush(); // Flush system output buffer
        readfile($filePath);
        exit();
    } else {
        echo "File not found.";
    }
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_job'])) {
    $orderId = $_POST['order_id'];

    // Delete the order
    $deleteSql = "DELETE FROM $shopTable WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $stmt->close();
}

// Handle status update and rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['status'];

    // Update the status
    $updateSql = "UPDATE $shopTable SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("si", $newStatus, $orderId);
    $stmt->execute();
    $stmt->close();

    // Handle refund for rejected jobs
    if ($newStatus == 'Rejected') {
        // Retrieve the payment ID and amount
        $stmt = $conn->prepare("SELECT payment_id, total_cost FROM $shopTable WHERE id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $stmt->bind_result($paymentId, $totalCost);
        $stmt->fetch();
        $stmt->close();

        // Refund the amount using Razorpay
        $apiKey = 'rzp_test_4tpD72KnHdRtz1';
        $apiSecret = 'rs5ezWErKOpoGF3iBdB8mJp9
';
        $razorpayUrl = "https://api.razorpay.com/v1/refunds";

        $ch = curl_init($razorpayUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$apiKey:$apiSecret");

        $data = [
            'payment_id' => $paymentId,
            'amount' => $totalCost * 100, // Amount in paise
            'notes' => 'Refund for rejected print job'
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($ch);
        curl_close($ch);

        $responseArray = json_decode($response, true);

        if ($responseArray['error']) {
            echo "Error processing refund: " . htmlspecialchars($responseArray['error']['description']);
        } else {
            echo "Refund processed successfully.";
        }
    }
}

// Fetch print orders for the shop by status
$sqlPending = "SELECT * FROM $shopTable WHERE status = 'Pending'";
$sqlCompleted = "SELECT * FROM $shopTable WHERE status = 'Completed'";
$sqlDelivered = "SELECT * FROM $shopTable WHERE status = 'Delivered'";

// Calculate total earnings for completed orders
$sqlEarnings = "SELECT SUM(total_cost) AS total_earnings FROM $shopTable WHERE status = 'Completed'";
$resultEarnings = $conn->query($sqlEarnings);
$rowEarnings = $resultEarnings->fetch_assoc();
$totalEarnings = $rowEarnings['total_earnings'] ? $rowEarnings['total_earnings'] : 0;

$resultPending = $conn->query($sqlPending);
$resultCompleted = $conn->query($sqlCompleted);
$resultDelivered = $conn->query($sqlDelivered);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['status'];

    // Update the status
    $updateSql = "UPDATE $shopTable SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("si", $newStatus, $orderId);
    $stmt->execute();
    $stmt->close();

    // Get the username associated with the order
    $stmt = $conn->prepare("SELECT username FROM $shopTable WHERE id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();

    // Send notification if the status is 'Completed'
    if ($newStatus == 'Completed') {
        $message = "Your print job (Order ID: $orderId) has been completed.";
        $notifSql = "INSERT INTO notifications (username, message) VALUES (?, ?)";
        $notifStmt = $conn->prepare($notifSql);
        $notifStmt->bind_param("ss", $username, $message);
        $notifStmt->execute();
        $notifStmt->close();
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Owner Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./style3.css" />
    <style>
        /* Dark theme background */
        body {
            background-color: #121212;
            color: #ffffff;
        }
        
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #1e1e1e;
        }
        
        th, td {
            border: 1px solid #333;
            padding: 12px;
            text-align: left;
        }
        
        th {
            background-color: #333;
        }
        
        td {
            background-color: #2e2e2e;
        }
        
        /* Hover effect for rows */
        tr:hover {
            background-color: #444;
            transition: background-color 0.3s ease;
        }

        /* Button styling */
        .btn-custom, .btn-danger, .btn-success, .btn-primary {
            padding: 5px 10px;
            font-size: 14px;
            margin: 2px;
            border-radius: 5px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        
        .btn-danger {
            background-color: #e74c3c;
            border: none;
            color: white;
        }
        
        .btn-success {
            background-color: #27ae60;
            border: none;
            color: white;
        }
        
        .btn-primary {
            background-color: #3498db;
            border: none;
            color: white;
        }

        /* Button hover and animation */
        .btn-danger:hover, .btn-success:hover, .btn-primary:hover {
            transform: scale(1.1);
            background-color: #555;
        }

        /* Logout button */
        .logout-btn {
            background-color: #ff6347;
            border: none;
            padding: 10px 20px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        
        .logout-btn:hover {
            background-color: #ff4500;
            transform: scale(1.1);
        }
    </style>
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this job? This action cannot be undone.");
        }

        function confirmStatusChange(status) {
            return confirm("Are you sure you want to mark this job as " + status + "?");
        }
    </script>

<script>
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.innerText = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 500);
        }, 3000);
    }

    function checkForNewOrders() {
        fetch('check_new_orders.php')
            .then(response => response.json())
            .then(data => {
                if (data.new_orders > 0) {
                    showNotification('You have ' + data.new_orders + ' new print job orders!');
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Check for new orders every 30 seconds
    setInterval(checkForNewOrders, 30000);

    // Initial check when the page loads
    checkForNewOrders();
</script>

<style>
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #007bff;
        color: white;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
        opacity: 1;
        transition: opacity 0.5s;
    }
</style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($shopname); ?>! Shop Owner</h2>

        <!-- Logout Form -->
        <form method="POST" style="text-align: right;">
            <button type="submit" name="logout" class="logout-btn">Logout</button>
        </form>

        <h3>Balance Overview</h3>
        <p>Total Earnings from Completed Orders: ₹<?php echo number_format($totalEarnings, 2); ?></p>
        
        <h3>Pending Print Orders</h3>
<?php if ($resultPending->num_rows > 0): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Print Type</th>
            <th>Color Option</th>
            <th>Paper Size</th>
            <th>Paper Material</th>
            <th>Additional Services</th>
            <th>Quantity</th>
            <th>Delivery Address</th>
            <th>Number of Pages</th>
            <th>File Path</th>
            <th>Total Cost</th>
            <th>Payment ID</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $resultPending->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['print_type']); ?></td>
                <td><?php echo htmlspecialchars($row['color_option']); ?></td>
                <td><?php echo htmlspecialchars($row['paper_size']); ?></td>
                <td><?php echo htmlspecialchars($row['paper_material']); ?></td>
                <td><?php echo htmlspecialchars($row['additional_services']); ?></td>
                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                <td><?php echo htmlspecialchars($row['delivery_address']); ?></td>
                <td><?php echo htmlspecialchars($row['number_of_pages']); ?></td>
                <td><a href="owner_dashboard.php?job_id=<?php echo $row['id']; ?>&shop_selector=<?php echo urlencode($shopTable); ?>">Download</a></td>
                <td><?php echo htmlspecialchars($row['total_cost']); ?></td>
                <td><?php echo htmlspecialchars($row['payment_id']); ?></td>
                <td>
                    <form method="POST" style="display:inline-block;" onsubmit="return confirmStatusChange('Completed');">
                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="status" value="Completed">
                        <button type="submit" name="update_status" class="btn btn-success btn-custom">Mark as Completed</button>
                    </form>
                    
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No pending print orders found.</p>
<?php endif; ?>


        <h3>Completed Print Orders</h3>
        <?php if ($resultCompleted->num_rows > 0): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Print Type</th>
                    <th>Color Option</th>
                    <th>Paper Size</th>
                    <th>Paper Material</th>
                    <th>Additional Services</th>
                    <th>Quantity</th>
                    <th>Delivery Address</th>
                    <th>Number of Pages</th>
                    <th>File Path</th>
                    <th>Total Cost</th>
                    <th>Payment ID</th>
                    <th>Actions</th>
                </tr>
                <?php while($row = $resultCompleted->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['print_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['color_option']); ?></td>
                        <td><?php echo htmlspecialchars($row['paper_size']); ?></td>
                        <td><?php echo htmlspecialchars($row['paper_material']); ?></td>
                        <td><?php echo htmlspecialchars($row['additional_services']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['delivery_address']); ?></td>
                        <td><?php echo htmlspecialchars($row['number_of_pages']); ?></td>
                        <td><a href="owner_dashboard.php?job_id=<?php echo $row['id']; ?>&shop_selector=<?php echo urlencode($shopTable); ?>">Download</a></td>
                        <td><?php echo htmlspecialchars($row['total_cost']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_id']); ?></td>
                        <td>
                            <form method="POST" style="display:inline-block;" onsubmit="return confirmStatusChange('Delivered');">
                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="status" value="Delivered">
                                <button type="submit" name="update_status" class="btn btn-primary btn-custom">Mark as Delivered</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No completed print orders found.</p>
        <?php endif; ?>

        <h3>Delivered Print Orders</h3>
        <?php if ($resultDelivered->num_rows > 0): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Print Type</th>
                    <th>Color Option</th>
                    <th>Paper Size</th>
                    <th>Paper Material</th>
                    <th>Additional Services</th>
                    <th>Quantity</th>
                    <th>Delivery Address</th>
                    <th>Number of Pages</th>
                    <th>File Path</th>
                    <th>Total Cost</th>
                    <th>Payment ID</th>
                    <th>Actions</th>
                </tr>
                <?php while($row = $resultDelivered->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['print_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['color_option']); ?></td>
                        <td><?php echo htmlspecialchars($row['paper_size']); ?></td>
                        <td><?php echo htmlspecialchars($row['paper_material']); ?></td>
                        <td><?php echo htmlspecialchars($row['additional_services']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['delivery_address']); ?></td>
                        <td><?php echo htmlspecialchars($row['number_of_pages']); ?></td>
                        <td><a href="owner_dashboard.php?job_id=<?php echo $row['id']; ?>&shop_selector=<?php echo urlencode($shopTable); ?>">Download</a></td>
                        <td><?php echo htmlspecialchars($row['total_cost']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_id']); ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirmDelete();">
                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete_job" class="btn btn-danger btn-custom">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No delivered print orders found.</p>
        <?php endif; ?>

    </div>

    <!-- Bootstrap JavaScript and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
