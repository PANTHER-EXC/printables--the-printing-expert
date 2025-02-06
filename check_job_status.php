<?php
session_start();

// Ensure that the user is logged in
if (!isset($_SESSION['username'])) {
    header("HTTP/1.1 401 Unauthorized");
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

$userId = $_SESSION['username']; // Assuming you store the username in session

// List of all shop tables
$shops = ['shop_kattappana', 'shop_nedumkandam', 'shop_kumily', 'shop_thodupuzha', 'shop_adimali'];

// Initialize array to store jobs
$jobs = [];

// Query each shop table
foreach ($shops as $shop) {
    $sql = "SELECT id, status FROM $shop WHERE username = ? AND status IN ('Completed', 'Delivered')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $jobs[] = $row;
    }
    
    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode(['jobs' => $jobs]);

$conn->close();
?>
