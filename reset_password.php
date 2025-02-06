<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    $host = 'localhost';
    $db = 'print';
    $user = 'root';
    $pass = '';

 
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];


    if ($new_password !== $confirm_password) {
        echo "Passwords do not match. Please try again.";
        exit;
    }

   
    $conn = new mysqli($host, $user, $pass, $db);

   
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    
    $sql = "UPDATE users SET password = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $new_password, $username);

    
    if ($stmt->execute()) {
        echo "Password updated successfully.";
    } else {
        echo "Error updating password: " . $conn->error;
    }

   
    $stmt->close();
    $conn->close();
}
?>
