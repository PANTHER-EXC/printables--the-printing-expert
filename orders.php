<?php
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

// Get a list of all shop tables
$shopTables = ['shop_kattappana', 'shop_nedumkandam', 'shop_kumily', 'shop_thodupuzha', 'shop_adimali'];

// Initialize an array to hold all orders
$allOrders = [];

// Fetch orders from each shop table
foreach ($shopTables as $shopTable) {
    $sql = "SELECT * FROM $shopTable WHERE username = ? ORDER BY id DESC"; // Ordering by `id` to show latest first
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Fetch results and add to $allOrders array
    while ($row = $result->fetch_assoc()) {
        $row['shop_table'] = $shopTable; // Add shop table name to results
        $allOrders[] = $row;
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
    <title>Your Orders</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .container:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }
        h2 {
            color: #343a40;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #dee2e6;
        }
        th {
            background-color: #343a40; /* Darker color for header */
            color: #000;
            text-align: center;
            padding: 15px;
            transition: background-color 0.3s ease;
        }
        th:hover {
            background-color: #007bff;
        }
        td {
            padding: 12px;
            text-align: center;
            color: #495057;
            transition: background-color 0.3s ease;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-align: center;
            border: none;
            border-radius: 4px;
            font-size: 1.1em;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Recent Orders</h2>
        <?php if (!empty($allOrders)): ?>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Print Type</th>
                    <th>Color Option</th>
                    <th>Paper Size</th>
                    <th>Paper Material</th>
                    <th>Additional Services</th>
                    <th>Quantity</th>
                    <th>Delivery Address</th>
                    <th>Number of Pages</th>
                    <th>Total Cost (INR)</th>
                    <th>Shop</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allOrders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['print_type'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($order['color_option'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($order['paper_size'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($order['paper_material'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($order['additional_services'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($order['quantity'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($order['delivery_address'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($order['number_of_pages'] ?? 'N/A'); ?></td>
                    <td>₹<?php echo isset($order['total_cost']) ? number_format($order['total_cost'], 2) : 'N/A'; ?></td>
                    <td><?php echo htmlspecialchars($order['shop_table'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($order['status'] ?? 'Pending'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="text-center">No orders found.</p>
        <?php endif; ?>
        <a href="dashboard.php" class="btn-back">Back to Dashboard</a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
