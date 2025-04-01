<?php
session_start();

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
  header("Location: index.php");
  exit;
}

// Include database connection
require_once 'db_connect.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $title = htmlspecialchars($_POST['title']);
  $description = htmlspecialchars($_POST['description']);
  $discount = (int)$_POST['discount'];
  $old_price = (float)$_POST['old_price'];
  $new_price = (float)$_POST['new_price'];
  $valid_until = $_POST['valid_until'];
  
  // Handle image upload
  $image_path = '';
  if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
      $allowed = array('jpg', 'jpeg', 'png', 'gif');
      $filename = $_FILES['image']['name'];
      $ext = pathinfo($filename, PATHINFO_EXTENSION);
      
      if(in_array(strtolower($ext), $allowed)) {
          $new_filename = uniqid() . '.' . $ext;
          $upload_path = '../uploads/' . $new_filename;
          
          if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
              $image_path = 'uploads/' . $new_filename;
          }
      }
  }
  
  // Insert into database
  $sql = "INSERT INTO promotions (title, description, discount, old_price, new_price, image_path, valid_until) 
          VALUES (?, ?, ?, ?, ?, ?, ?)";
  
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssiddss", $title, $description, $discount, $old_price, $new_price, $image_path, $valid_until);
  
  if ($stmt->execute()) {
      $success_message = "Акцію успішно додано!";
  } else {
      $error_message = "Помилка: " . $stmt->error;
  }
  
  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Farmatek Адмін - Додати акцію</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="admin-style.css">
</head>
<body>
  <div class="admin-sidebar">
      <div class="logo">
          <i class="fas fa-mortar-pestle"></i>
          <h2>Farmatek</h2>
      </div>
      <nav>
          <ul>
              <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Панель керування</a></li>
              <li class="active"><a href="add_promotion.php"><i class="fas fa-plus-circle"></i> Додати акцію</a></li>
              <li><a href="weekly_deals.php"><i class="fas fa-calendar-week"></i> Тижневі знижки</a></li>
              <li><a href="welcome_section.php"><i class="fas fa-home"></i> Вітальна секція</a></li>
              <li><a href="seasonal_promo.php"><i class="fas fa-percentage"></i> Сезонна акція</a></li>
              <li><a href="subscribers.php"><i class="fas fa-users"></i> Підписники</a></li>
              <li><a href="settings.php"><i class="fas fa-cog"></i> Налаштування</a></li>
              <li><a href="dashboard.php?logout=1"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
          </ul>
      </nav>
  </div>
  
  <div class="admin-content">
      <header>
          <h1>Додати нову акцію</h1>
          <div class="user-info">
              <span>Вітаємо, Адміністратор</span>
              <a href="dashboard.php?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Вийти</a>
          </div>
      </header>
      
      <?php if (isset($success_message)): ?>
          <div class="alert success"><?php echo $success_message; ?></div>
      <?php endif; ?>
      
      <?php if (isset($error_message)): ?>
          <div class="alert error"><?php echo $error_message; ?></div>
      <?php endif; ?>
      
      <div class="content-section">
          <form method="POST" action="" enctype="multipart/form-data" class="admin-form">
              <div class="form-group">
                  <label for="title">Назва акції</label>
                  <input type="text" id="title" name="title" required>
              </div>
              
              <div class="form-group">
                  <label for="description">Опис</label>
                  <textarea id="description" name="description" rows="4" required></textarea>
              </div>
              
              <div class="form-row">
                  <div class="form-group">
                      <label for="discount">Відсоток знижки</label>
                      <input type="number" id="discount" name="discount" min="0" max="100" required>
                  </div>
                  
                  <div class="form-group">
                      <label for="old_price">Стара ціна</label>
                      <input type="number" id="old_price" name="old_price" min="0" step="0.01" required>
                  </div>
                  
                  <div class="form-group">
                      <label for="new_price">Нова ціна</label>
                      <input type="number" id="new_price" name="new_price" min="0" step="0.01" required>
                  </div>
              </div>
              
              <div class="form-group">
                  <label for="image">Зображення товару</label>
                  <input type="file" id="image" name="image" accept="image/*">
                  <small>Рекомендований розмір: 600x400 пікселів</small>
              </div>
              
              <div class="form-group">
                  <label for="valid_until">Діє до</label>
                  <input type="date" id="valid_until" name="valid_until" required>
              </div>
              
              <div class="form-actions">
                  <button type="submit" class="btn">Додати акцію</button>
                  <a href="dashboard.php" class="btn btn-secondary">Скасувати</a>
              </div>
          </form>
      </div>
  </div>
</body>
</html>

