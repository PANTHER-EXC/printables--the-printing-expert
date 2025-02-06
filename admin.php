<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'print');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total number of shops
$shops_query = "SELECT COUNT(*) AS total_shops FROM shop_owners";
$shops_result = $conn->query($shops_query);
$shops_data = $shops_result->fetch_assoc();
$total_shops = $shops_data['total_shops'];

// Fetch total number of users
$users_query = "SELECT COUNT(*) AS total_users FROM users";
$users_result = $conn->query($users_query);
$users_data = $users_result->fetch_assoc();
$total_users = $users_data['total_users'];

// Fetch total number of complaints
$complaints_query = "SELECT COUNT(*) AS total_complaints FROM complaints";
$complaints_result = $conn->query($complaints_query);
$complaints_data = $complaints_result->fetch_assoc();
$total_complaints = $complaints_data['total_complaints'];

// Fetch total number of orders (from all shops)
$orders_query = "SELECT COUNT(*) AS total_orders FROM (
    SELECT id FROM shop_kattappana
    UNION ALL
    SELECT id FROM shop_nedumkandam
    UNION ALL
    SELECT id FROM shop_kumily
    UNION ALL
    SELECT id FROM shop_thodupuzha
    UNION ALL
    SELECT id FROM shop_adimali
) AS all_orders";
$orders_result = $conn->query($orders_query);
$orders_data = $orders_result->fetch_assoc();
$total_orders = $orders_data['total_orders'];

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRINTABLES Admin Dashboard</title>
   
    <link rel="stylesheet" href="aresponsive.css">

    <style>
        /* Base styles for nav links */
        .nav-option a {
            color: #ffffff; /* Default color for all links */
            text-decoration: none;
        }

        /* Specific colors for each navigation link */
        .dashboard-link a {
            color: #000; /* Example color for Dashboard */
        }

        .shops-link a {
            color: #000; /* Example color for Shops */
        }

        .users-link a {
            color: #000; /* Example color for Users */
        }

        .complaints-link a {
            color: #000; /* Example color for Complaints */
        }

        .orders-link a {
            color: #000; /* Example color for Orders */
        }

        .logout-link a {
            color: #f44336; /* Example color for Logout */
        }

        /* Hover effect for nav links */
        .nav-option a:hover {
            text-decoration: underline;
        }

        /* Main CSS Here */

@import url(
"https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
}
:root {
  --background-color1: #fafaff;
  --background-color2: #ffffff;
  --background-color3: #ededed;
  --background-color4: #cad7fda4;
  --primary-color: #4b49ac;
  --secondary-color: #0c007d;
  --Border-color: #3f0097;
  --one-use-color: #3f0097;
  --two-use-color: #5500cb;
}
body {
  background-color: var(--background-color4);
  max-width: 100%;
  overflow-x: hidden;
}

header {
  height: 70px;
  width: 100vw;
  padding: 0 30px;
  background-color: var(--background-color1);
  position: fixed;
  z-index: 100;
  box-shadow: 1px 1px 15px rgba(161, 182, 253, 0.825);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  font-size: 27px;
  font-weight: 600;
  color: rgb(47, 141, 70);
}

.icn {
  height: 30px;
}
.menuicn {
  cursor: pointer;
}

.searchbar,
.message,
.logosec {
  display: flex;
  align-items: center;
  justify-content: center;
}

.searchbar2 {
  display: none;
}

.logosec {
  gap: 60px;
}

.searchbar input {
  width: 250px;
  height: 42px;
  border-radius: 50px 0 0 50px;
  background-color: var(--background-color3);
  padding: 0 20px;
  font-size: 15px;
  outline: none;
  border: none;
}
.searchbtn {
  width: 50px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 0px 50px 50px 0px;
  background-color: var(--secondary-color);
  cursor: pointer;
}

.message {
  gap: 40px;
  position: relative;
  cursor: pointer;
}
.circle {
  height: 7px;
  width: 7px;
  position: absolute;
  background-color: #fa7bb4;
  border-radius: 50%;
  left: 19px;
  top: 8px;
}
.dp {
  height: 40px;
  width: 40px;
  background-color: #626262;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}
.main-container {
  display: flex;
  width: 100vw;
  position: relative;
  top: 70px;
  z-index: 1;
}
.dpicn {
  height: 42px;
}

.main {
  height: calc(100vh - 70px);
  width: 100%;
  overflow-y: scroll;
  overflow-x: hidden;
  padding: 40px 30px 30px 30px;
}

.main::-webkit-scrollbar-thumb {
  background-image: 
        linear-gradient(to bottom, rgb(0, 0, 85), rgb(0, 0, 50));
}
.main::-webkit-scrollbar {
  width: 5px;
}
.main::-webkit-scrollbar-track {
  background-color: #9e9e9eb2;
}

.box-container {
  display: flex;
  justify-content: space-evenly;
  align-items: center;
  flex-wrap: wrap;
  gap: 50px;
}
.nav {
  min-height: 91vh;
  width: 250px;
  background-color: var(--background-color2);
  position: absolute;
  top: 0px;
  left: 00;
  box-shadow: 1px 1px 10px rgba(198, 189, 248, 0.825);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  overflow: hidden;
  padding: 30px 0 20px 10px;
}
.navcontainer {
  height: calc(100vh - 70px);
  width: 250px;
  position: relative;
  overflow-y: scroll;
  overflow-x: hidden;
  transition: all 0.5s ease-in-out;
}
.navcontainer::-webkit-scrollbar {
  display: none;
}
.navclose {
  width: 80px;
}
.nav-option {
  width: 250px;
  height: 60px;
  display: flex;
  align-items: center;
  padding: 0 30px 0 20px;
  gap: 20px;
  transition: all 0.1s ease-in-out;
}
.nav-option:hover {
  border-left: 5px solid #a2a2a2;
  background-color: #dadada;
  cursor: pointer;
}
.nav-img {
  height: 30px;
}

.nav-upper-options {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 30px;
}

.option1 {
  border-left: 5px solid #010058af;
  background-color: var(--Border-color);
  color: white;
  cursor: pointer;
}
.option1:hover {
  border-left: 5px solid #010058af;
  background-color: var(--Border-color);
}
.box {
  height: 130px;
  width: 230px;
  border-radius: 20px;
  box-shadow: 3px 3px 10px rgba(0, 30, 87, 0.751);
  padding: 20px;
  display: flex;
  align-items: center;
  justify-content: space-around;
  cursor: pointer;
  transition: transform 0.3s ease-in-out;
}
.box:hover {
  transform: scale(1.08);
}

.box:nth-child(1) {
  background-color: var(--one-use-color);
}
.box:nth-child(2) {
  background-color: var(--two-use-color);
}
.box:nth-child(3) {
  background-color: var(--one-use-color);
}
.box:nth-child(4) {
  background-color: var(--two-use-color);
}

.box img {
  height: 50px;
}
.box .text {
  color: white;
}
.topic {
  font-size: 13px;
  font-weight: 400;
  letter-spacing: 1px;
}

.topic-heading {
  font-size: 30px;
  letter-spacing: 3px;
}

.report-container {
  min-height: 300px;
  max-width: 1200px;
  margin: 70px auto 0px auto;
  background-color: #ffffff;
  border-radius: 30px;
  box-shadow: 3px 3px 10px rgb(188, 188, 188);
  padding: 0px 20px 20px 20px;
}
.report-header {
  height: 80px;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 20px 10px 20px;
  border-bottom: 2px solid rgba(0, 20, 151, 0.59);
}

.recent-Articles {
  font-size: 30px;
  font-weight: 600;
  color: #5500cb;
}

.view {
  height: 35px;
  width: 90px;
  border-radius: 8px;
  background-color: #5500cb;
  color: white;
  font-size: 15px;
  border: none;
  cursor: pointer;
}

.report-body {
  max-width: 1160px;
  overflow-x: auto;
  padding: 20px;
}
.report-topic-heading,
.item1 {
  width: 1120px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.t-op {
  font-size: 18px;
  letter-spacing: 0px;
}

.items {
  width: 1120px;
  margin-top: 15px;
}

.item1 {
  margin-top: 20px;
}
.t-op-nextlvl {
  font-size: 14px;
  letter-spacing: 0px;
  font-weight: 600;
}

.label-tag {
  width: 100px;
  text-align: center;
  background-color: rgb(0, 177, 0);
  color: white;
  border-radius: 4px;
}
    </style>
</head>
<body>

    <!-- Header section -->
    <header>
        <div class="logosec">
            <div class="logo">PRINTABLES</div>
        </div>

        <div class="message">
            <div class="dp">
                <img src="img/admin.png" class="dpicn" alt="dp">
            </div>
        </div>
    </header>

    <!-- Sidebar navigation -->
    <div class="main-container">
        <div class="navcontainer">
            <nav class="nav">
                <div class="nav-upper-options">
                    <div class="nav-option option1">
                        <h3 class="dashboard-link"><a href="admin.html">Dashboard</a></h3>
                    </div>

                    <div class="option2 nav-option">
                        <h3 class="shops-link"><a href="admin/shops.php">Shops</a></h3>
                    </div>

                    <div class="nav-option option3">
                        <h3 class="users-link"><a href="admin/users.php">Users</a></h3>
                    </div>

                    <div class="nav-option option4">
                        <h3 class="complaints-link"><a href="admin/complaints.php">Complaints</a></h3>
                    </div>

                    <div class="nav-option option5">
                        <h3 class="orders-link"><a href="admin/orders.php">Orders</a></h3>
                    </div>

                    <div class="nav-option logout">
                        <h3 class="logout-link"><a href="logout.php">Logout</a></h3>
                    </div>
                </div>
            </nav>
        </div>
        <!-- Main Content Area -->
        <div class="main">
            <div class="box-container">
                <div class="box box1">
                    <div class="text">
                        <h2 class="topic-heading" id="total-shops"><?php echo $total_shops; ?></h2>
                        <h2 class="topic">Total Shops</h2>
                    </div>
                </div>

                <div class="box box2">
                    <div class="text">
                        <h2 class="topic-heading" id="total-users"><?php echo $total_users; ?></h2>
                        <h2 class="topic">Total Users</h2>
                    </div>
                </div>

                <div class="box box3">
                    <div class="text">
                        <h2 class="topic-heading" id="total-complaints"><?php echo $total_complaints; ?></h2>
                        <h2 class="topic">Total Complaints</h2>
                    </div>
                </div>

                <div class="box box4">
                    <div class="text">
                        <h2 class="topic-heading" id="total-orders"><?php echo $total_orders; ?></h2>
                        <h2 class="topic">Total Orders</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="admin.js"></script>
</body>
</html>
