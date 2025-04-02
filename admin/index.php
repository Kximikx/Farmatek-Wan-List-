<?php
session_start();

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
  header("Location: dashboard.php");
  exit;
}

// Check login credentials
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = "admin"; // Change this to your desired username
  $password = "farmatek123"; // Change this to your desired password
  
  if ($_POST['username'] === $username && $_POST['password'] === $password) {
      $_SESSION['admin_logged_in'] = true;
      header("Location: dashboard.php");
      exit;
  } else {
      $error_message = "Невірне ім'я користувача або пароль";
  }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Farmatek Адмін - Вхід</title>
  <link rel="shortcut icon" href="img/link_logo.png" type="image/png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="admin-style.css">
</head>
<body>
  <div class="admin-container">
      <div class="login-form">
          <div class="logo">
              <i class="fas fa-mortar-pestle"></i>
              <h1>Farmatek Адмін</h1>
          </div>
          
          <?php if (isset($error_message)): ?>
              <div class="error-message"><?php echo $error_message; ?></div>
          <?php endif; ?>
          
          <form method="POST" action="">
              <div class="form-group">
                  <label for="username">Ім'я користувача</label>
                  <input type="text" id="username" name="username" required>
              </div>
              <div class="form-group">
                  <label for="password">Пароль</label>
                  <input type="password" id="password" name="password" required>
              </div>
              <button type="submit" class="btn">Увійти</button>
          </form>
          <div class="back-link">
              <a href="../index.php"><i class="fas fa-arrow-left"></i> Повернутися на сайт</a>
          </div>
      </div>
  </div>
</body>
</html>

