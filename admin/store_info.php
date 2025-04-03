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
  $section_title = htmlspecialchars($_POST['section_title']);
  $section_subtitle = htmlspecialchars($_POST['section_subtitle']);
  $map_embed = $_POST['map_embed'];
  $location_title = htmlspecialchars($_POST['location_title']);
  $location_address = htmlspecialchars($_POST['location_address']);
  $hours_title = htmlspecialchars($_POST['hours_title']);
  $working_hours = htmlspecialchars($_POST['working_hours']);
  $contact_title = htmlspecialchars($_POST['contact_title']);
  $contact_phone = htmlspecialchars($_POST['contact_phone']);
  $contact_email = htmlspecialchars($_POST['contact_email']);
  $details_title = htmlspecialchars($_POST['details_title']);
  $details_text = htmlspecialchars($_POST['details_text']);

  // Check if store_info table exists, if not create it
  $sql = "CREATE TABLE IF NOT EXISTS store_info (
      id INT(11) AUTO_INCREMENT PRIMARY KEY,
      section_title VARCHAR(255) NOT NULL,
      section_subtitle VARCHAR(255) NOT NULL,
      map_embed TEXT NOT NULL,
      location_title VARCHAR(255) NOT NULL,
      location_address TEXT NOT NULL,
      hours_title VARCHAR(255) NOT NULL,
      working_hours TEXT NOT NULL,
      contact_title VARCHAR(255) NOT NULL,
      contact_phone VARCHAR(50) NOT NULL,
      contact_email VARCHAR(255) NOT NULL,
      details_title VARCHAR(255) NOT NULL,
      details_text TEXT NOT NULL,
      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  )";

  if ($conn->query($sql) === TRUE) {
      // Table created or already exists
      
      // Check if record exists
      $check_sql = "SELECT id FROM store_info LIMIT 1";
      $check_result = $conn->query($check_sql);
      
      if ($check_result->num_rows > 0) {
          // Update existing record
          $sql = "UPDATE store_info SET 
              section_title = ?, 
              section_subtitle = ?, 
              map_embed = ?, 
              location_title = ?, 
              location_address = ?, 
              hours_title = ?, 
              working_hours = ?, 
              contact_title = ?, 
              contact_phone = ?, 
              contact_email = ?, 
              details_title = ?, 
              details_text = ? 
              WHERE id = 1";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("ssssssssssss", 
              $section_title, 
              $section_subtitle, 
              $map_embed, 
              $location_title, 
              $location_address, 
              $hours_title, 
              $working_hours, 
              $contact_title, 
              $contact_phone, 
              $contact_email, 
              $details_title, 
              $details_text
          );
      } else {
          // Insert new record
          $sql = "INSERT INTO store_info (
              section_title, 
              section_subtitle, 
              map_embed, 
              location_title, 
              location_address, 
              hours_title, 
              working_hours, 
              contact_title, 
              contact_phone, 
              contact_email, 
              details_title, 
              details_text
          ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("ssssssssssss", 
              $section_title, 
              $section_subtitle, 
              $map_embed, 
              $location_title, 
              $location_address, 
              $hours_title, 
              $working_hours, 
              $contact_title, 
              $contact_phone, 
              $contact_email, 
              $details_title, 
              $details_text
          );
      }
      
      if ($stmt->execute()) {
          $success_message = "Інформацію про магазин успішно оновлено!";
      } else {
          $error_message = "Помилка: " . $stmt->error;
      }
      
      $stmt->close();
  } else {
      $error_message = "Помилка створення таблиці: " . $conn->error;
  }
}

// Get current store info
$store_info = [
  'section_title' => 'Відвідайте наш магазин',
  'section_subtitle' => 'Приходьте до нашої апте��и, щоб скористатися цими акціями',
  'map_embed' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3000!2d-73.9857!3d40.7484!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDQ0JzU0LjIiTiA3M8KwNTknMDguNSJX!5e0!3m2!1sen!2sus!4v1620000000000!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
  'location_title' => 'Наше розташування',
  'location_address' => 'вул. Здоров\'я 123, Медичний район<br>Місто, Країна, 12345',
  'hours_title' => 'Години роботи',
  'working_hours' => 'Понеділок - П\'ятниця: 8:00 - 20:00<br>Субота: 9:00 - 18:00<br>Неділя: 10:00 - 16:00',
  'contact_title' => 'Зв\'яжіться з нами',
  'contact_phone' => '+1 (123) 456-7890',
  'contact_email' => 'info@farmatek.com',
  'details_title' => 'Деталі акцій',
  'details_text' => 'Всі акції дійсні тільки в магазині.<br>Пропозиції не можуть поєднуватися, якщо не вказано інше.<br>Поки товар є в наявності.'
];

// Check if store_info table exists
$table_exists = $conn->query("SHOW TABLES LIKE 'store_info'")->num_rows > 0;

if ($table_exists) {
  // Get data from database
  $sql = "SELECT * FROM store_info LIMIT 1";
  $result = $conn->query($sql);
  
  if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $store_info = [
          'section_title' => $row['section_title'],
          'section_subtitle' => $row['section_subtitle'],
          'map_embed' => $row['map_embed'],
          'location_title' => $row['location_title'],
          'location_address' => $row['location_address'],
          'hours_title' => $row['hours_title'],
          'working_hours' => $row['working_hours'],
          'contact_title' => $row['contact_title'],
          'contact_phone' => $row['contact_phone'],
          'contact_email' => $row['contact_email'],
          'details_title' => $row['details_title'],
          'details_text' => $row['details_text']
      ];
  }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Farmatek Адмін - Інформація про магазин</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="admin-style.css">
  <link rel="stylesheet" href="admin-pages.css">
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
          <li><a href="add_promotion.php"><i class="fas fa-plus-circle"></i> Додати акцію</a></li>
          <li><a href="weekly_deals.php"><i class="fas fa-calendar-week"></i> Тижневі знижки</a></li>
          <li><a href="welcome_section.php"><i class="fas fa-home"></i> Вітальна секція</a></li>
          <li><a href="seasonal_promo.php"><i class="fas fa-percentage"></i> Сезонна акція</a></li>
          <li class="active"><a href="store_info.php"><i class="fas fa-store"></i> Інформація про магазин</a></li>
          <li><a href="subscribers.php"><i class="fas fa-users"></i> Підписники</a></li>
          <li><a href="logo_settings.php"><i class="fas fa-image"></i> Логотип</a></li>
          <li><a href="dashboard.php?logout=1"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
      </ul>
  </nav>
</div>

<div class="admin-content">
  <header>
      <h1>Інформація про магазин</h1>
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
      <div class="section-header">
          <h2>Редагувати інформацію про магазин</h2>
          <p>Ця секція відображається в нижній частині головної сторінки</p>
      </div>
      
      <form method="POST" action="" class="admin-form">
          <div class="form-group">
              <label for="section_title">Заголовок секції</label>
              <input type="text" id="section_title" name="section_title" value="<?php echo $store_info['section_title']; ?>" required>
          </div>
          
          <div class="form-group">
              <label for="section_subtitle">Підзаголовок секції</label>
              <input type="text" id="section_subtitle" name="section_subtitle" value="<?php echo $store_info['section_subtitle']; ?>" required>
          </div>
          
          <div class="form-group">
              <label for="map_embed">Код вбудованої карти Google Maps</label>
              <textarea id="map_embed" name="map_embed" rows="4" required><?php echo htmlspecialchars($store_info['map_embed']); ?></textarea>
              <small>Вставте HTML-код iframe з Google Maps. Щоб отримати код, перейдіть на <a href="https://www.google.com/maps" target="_blank">Google Maps</a>, знайдіть своє місце, натисніть "Поділитися" і виберіть "Вбудувати карту".</small>
          </div>
          
          <h3 class="form-section-title">Блок розташування</h3>
          
          <div class="form-group">
              <label for="location_title">Заголовок блоку розташування</label>
              <input type="text" id="location_title" name="location_title" value="<?php echo $store_info['location_title']; ?>" required>
          </div>
          
          <div class="form-group">
              <label for="location_address">Адреса (можна використовувати HTML-теги)</label>
              <textarea id="location_address" name="location_address" rows="3" required><?php echo htmlspecialchars($store_info['location_address']); ?></textarea>
          </div>
          
          <h3 class="form-section-title">Блок годин роботи</h3>
          
          <div class="form-group">
              <label for="hours_title">Заголовок блоку годин роботи</label>
              <input type="text" id="hours_title" name="hours_title" value="<?php echo $store_info['hours_title']; ?>" required>
          </div>
          
          <div class="form-group">
              <label for="working_hours">Години роботи (можна використовувати HTML-теги)</label>
              <textarea id="working_hours" name="working_hours" rows="3" required><?php echo htmlspecialchars($store_info['working_hours']); ?></textarea>
          </div>
          
          <h3 class="form-section-title">Блок контактів</h3>
          
          <div class="form-group">
              <label for="contact_title">Заголовок блоку контактів</label>
              <input type="text" id="contact_title" name="contact_title" value="<?php echo $store_info['contact_title']; ?>" required>
          </div>
          
          <div class="form-group">
              <label for="contact_phone">Номер телефону</label>
              <input type="text" id="contact_phone" name="contact_phone" value="<?php echo $store_info['contact_phone']; ?>" required>
          </div>
          
          <div class="form-group">
              <label for="contact_email">Email адреса</label>
              <input type="email" id="contact_email" name="contact_email" value="<?php echo $store_info['contact_email']; ?>" required>
          </div>
          
          <h3 class="form-section-title">Блок деталей акцій</h3>
          
          <div class="form-group">
              <label for="details_title">Заголовок блоку деталей акцій</label>
              <input type="text" id="details_title" name="details_title" value="<?php echo $store_info['details_title']; ?>" required>
          </div>
          
          <div class="form-group">
              <label for="details_text">Текст деталей акцій (можна використовувати HTML-теги)</label>
              <textarea id="details_text" name="details_text" rows="3" required><?php echo htmlspecialchars($store_info['details_text']); ?></textarea>
          </div>
          
          <div class="form-actions">
              <button type="submit" class="btn">Оновити інформацію про магазин</button>
              <a href="dashboard.php" class="btn btn-secondary">Скасувати</a>
          </div>
      </form>
  </div>
  
  <div class="content-section">
      <div class="section-header">
          <h2>Попередній перегляд</h2>
      </div>
      
      <div class="preview-container">
          <h2 class="preview-title"><?php echo $store_info['section_title']; ?></h2>
          <p class="preview-subtitle"><?php echo $store_info['section_subtitle']; ?></p>
          
          <div class="store-preview">
              <div class="map-preview">
                  <div class="map-placeholder">
                      <p>Тут буде відображатися карта Google Maps</p>
                      <small>Для безпеки iframe не відображається в попередньому перегляді</small>
                  </div>
              </div>
              <div class="info-preview">
                  <div class="info-card-preview">
                      <i class="fas fa-map-marker-alt"></i>
                      <h3><?php echo $store_info['location_title']; ?></h3>
                      <p><?php echo $store_info['location_address']; ?></p>
                  </div>
                  <div class="info-card-preview">
                      <i class="fas fa-clock"></i>
                      <h3><?php echo $store_info['hours_title']; ?></h3>
                      <p><?php echo $store_info['working_hours']; ?></p>
                  </div>
                  <div class="info-card-preview">
                      <i class="fas fa-phone"></i>
                      <h3><?php echo $store_info['contact_title']; ?></h3>
                      <p>Телефон: <?php echo $store_info['contact_phone']; ?><br>
                      Email: <?php echo $store_info['contact_email']; ?></p>
                  </div>
                  <div class="info-card-preview">
                      <i class="fas fa-info-circle"></i>
                      <h3><?php echo $store_info['details_title']; ?></h3>
                      <p><?php echo $store_info['details_text']; ?></p>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>
</body>
</html>

