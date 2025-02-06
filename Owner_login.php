<?php
session_start();

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if shopname and password are set
    if (isset($_POST['shopname']) && isset($_POST['password'])) {
        $shopname = $_POST['shopname'];
        $password = $_POST['password'];

        // SQL query to fetch the shop owner details
        $sql = "SELECT * FROM shop_owners WHERE shopname = ? AND password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $shopname, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Valid login, set session variables
            $_SESSION['shopname'] = $shopname;
            header("Location: owner_dashboard.php");
            exit();
        } else {
            // Invalid credentials, set error message in session
            $_SESSION['error_message'] = "Invalid shop name or password.";
            header("Location: Owner_login.html"); // Replace with your login page
            exit();
        }

        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Please fill in both fields.";
        header("Location: Owner_login.html"); // Replace with your login page
        exit();
    }
}

$conn->close();
?>
