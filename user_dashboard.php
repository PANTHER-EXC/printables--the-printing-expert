<?php
// Start session and include database connection
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $password = $conn->real_escape_string($_POST['password']);

        if (isset($_POST['email'])) {
            // Sign Up
            $email = $conn->real_escape_string($_POST['email']);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Check if username already exists
            $check_sql = "SELECT * FROM users WHERE username='$username'";
            $check_result = $conn->query($check_sql);

            if ($check_result->num_rows > 0) {
                $_SESSION['error_message'] = "Username already exists. Please choose a different username.";
                header("Location: login.php"); // Redirect to login page
                exit();
            } else {
                // Insert new user
                $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";

                if ($conn->query($sql) === TRUE) {
                    $_SESSION['username'] = $username;
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $_SESSION['error_message'] = "Error: " . $sql . "<br>" . $conn->error;
                    header("Location: login.php"); // Redirect to login page
                    exit();
                }
            }
        } else {
            // Sign In
            $sql = "SELECT * FROM users WHERE username='$username'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['username'] = $username;
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $_SESSION['error_message'] = "Invalid password.";
                    header("Location: index.html"); // Redirect to login page
                    exit();
                }
            } else {
                $_SESSION['error_message'] = "No user found with that username.";
                header("Location: index.html"); // Redirect to login page
                exit();
            }
        }
    }
}

$conn->close();
?>
