<?php
// Include your database connection details
require 'db.php';

// Start session
session_start();

// Initialize error message
if (!isset($_SESSION['error_message'])) {
    $_SESSION['error_message'] = '';
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get username and password from POST request
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL query to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if a user with the given username exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify the password
        if ($password === $user['password']) {
            // Set session variable for the logged-in user
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect to admin.php
            header("Location: admin.php");
            exit();
        } else {
            // Incorrect password, store error in session
            $_SESSION['error_message'] = "Invalid username or password.";
            header("Location: admin_1login.php");
            exit();
        }
    } else {
        // User does not exist, store error in session
        $_SESSION['error_message'] = "Invalid username or password.";
        header("Location: admin_1login.php");
        exit();
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
