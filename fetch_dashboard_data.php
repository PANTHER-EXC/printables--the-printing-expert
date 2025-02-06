<?php
$conn = new mysqli('localhost', 'root', '', 'print');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = array();

// Fetch total shops
$result = $conn->query("SELECT COUNT(*) AS totalShops FROM shop_owners");
$data['totalShops'] = $result->fetch_assoc()['totalShops'];

// Fetch total users
$result = $conn->query("SELECT COUNT(*) AS totalUsers FROM users");
$data['totalUsers'] = $result->fetch_assoc()['totalUsers'];

// Fetch total complaints
$result = $conn->query("SELECT COUNT(*) AS totalComplaints FROM complaints");
$data['totalComplaints'] = $result->fetch_assoc()['totalComplaints'];

// Fetch total orders
$result = $conn->query("SELECT COUNT(*) AS totalOrders FROM orders");
$data['totalOrders'] = $result->fetch_assoc()['totalOrders'];

echo json_encode($data);
$conn->close();
?>
