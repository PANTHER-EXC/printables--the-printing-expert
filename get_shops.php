<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'print');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all shops
$sql = "SELECT shopname FROM shop_owners";
$result = $conn->query($sql);

// Generate HTML options for the shop selector
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $shopname = htmlspecialchars($row['shopname']);
        echo "<option value='" . strtolower(str_replace(' ', '_', $shopname)) . "'>" . $shopname . "</option>";
    }
} else {
    echo "<option value=''>No shops available</option>";
}

$conn->close();
?>
