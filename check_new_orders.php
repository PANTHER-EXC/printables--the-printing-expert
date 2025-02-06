<?php
session_start();

if (!isset($_SESSION['shopname'])) {
    echo json_encode(['new_orders' => 0]);
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "print";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$shopname = $_SESSION['shopname'];
$shopTable = "shop_" . strtolower(str_replace(' ', '_', $shopname));

$sql = "SELECT COUNT(*) AS new_orders FROM $shopTable WHERE status = 'Pending' AND created_at > NOW() - INTERVAL 1 MINUTE";
$result = $conn->query($sql);

$row = $result->fetch_assoc();
$newOrders = $row['new_orders'];

echo json_encode(['new_orders' => $newOrders]);

$conn->close();
?>
