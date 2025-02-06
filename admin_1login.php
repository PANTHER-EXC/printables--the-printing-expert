<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <link rel="stylesheet" type="text/css" href="./style.css" />
  <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
  <style>
    .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.4);
      padding-top: 60px;
    }

    .modal-content {
      background-color: #fefefe;
      margin: 5% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 400px;
      border-radius: 5px;
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }

    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="forms-container">
      <div class="signin-signup">
        <form action="admin_login.php" method="POST" class="sign-in-form">
          <h2 class="title">Admin Login</h2>

          <div class="input-field">
            <i class="fas fa-user"></i>
            <input type="text" placeholder="Username" name="username" required />
          </div>
          <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="password" placeholder="Password" name="password" required />
          </div>
          <input type="submit" value="Login" class="btn solid" />
        </form>
      </div>
    </div>
    <div class="panels-container">
      <div class="panel left-panel">
        <div class="content">
          <h3>Welcome Admin!</h3>
          <p>Login to manage your printing platform and ensure smooth operations.</p>
        </div>
        <img src="./img/admin.png" class="image" alt="Admin">
      </div>
    </div>
  </div>

  <!-- Modal for Error Message -->
  <div id="errorModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <p id="errorMessage"></p>
    </div>
  </div>

  <script>
    window.onload = function() {
      <?php if (!empty($_SESSION['error_message'])): ?>
        document.getElementById('errorMessage').innerText = "<?php echo $_SESSION['error_message']; ?>";
        document.getElementById('errorModal').style.display = "block";
        <?php unset($_SESSION['error_message']); ?>
      <?php endif; ?>

      var span = document.getElementsByClassName("close")[0];
      span.onclick = function() {
        document.getElementById('errorModal').style.display = "none";
      }

      window.onclick = function(event) {
        if (event.target == document.getElementById('errorModal')) {
          document.getElementById('errorModal').style.display = "none";
        }
      }
    }
  </script>

  <script src="./app.js"></script>
</body>
</html>
