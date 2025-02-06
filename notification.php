<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Notifications</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(-45deg, #d122e3 0%, #6610f2 100%);
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        .container {
            width: 80%;
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.3);
        }
        table {
            width: 100%;
        }
        th, td {
            text-align: center;
            padding: 15px;
            transition: background-color 0.3s ease;
        }
        th {
            background-color: rgba(0, 123, 255, 0.8);
            color: white;
        }
        tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.2);
        }
        tr:hover {
            background-color: rgba(255, 255, 255, 0.5);
            transition: background-color 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Notifications</h1>

        <?php
        // Start the session
        session_start();

        // Check if username is set in session
        if (!isset($_SESSION['username'])) {
            echo "<div class='alert alert-danger'>User is not logged in. Please log in to see notifications.</div>";
            exit; // Stop execution if user is not logged in
        }

        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'print');

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch notifications based on the user's username
        $firstName = $_SESSION['username']; // Assuming first name is stored in session as username
        $sql = "SELECT reply_message, created_at FROM complaint_notifications WHERE username = ? ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            die("SQL Error: " . $conn->error);
        }

        $stmt->bind_param("s", $firstName);
        $stmt->execute();
        $result = $stmt->get_result();
        ?>

        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Complaint Reply</th>
                    <th>Received At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['reply_message']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No notifications found.</td></tr>";
                }

                // Close the statement and connection
                $stmt->close();
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
