<?php
session_start();

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
header("Location: index.php");
exit;
}

// Include database connection
require_once 'db_connect.php';

// Handle form submission for updating weekly deals
if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Update Monday deal
$monday_title = htmlspecialchars($_POST['monday_title']);
$monday_description = htmlspecialchars($_POST['monday_description']);
$monday_note = htmlspecialchars($_POST['monday_note']);

// Update Wednesday deal
$wednesday_title = htmlspecialchars($_POST['wednesday_title']);
$wednesday_description = htmlspecialchars($_POST['wednesday_description']);
$wednesday_note = htmlspecialchars($_POST['wednesday_note']);

// Update Friday deal
$friday_title = htmlspecialchars($_POST['friday_title']);
$friday_description = htmlspecialchars($_POST['friday_description']);
$friday_note = htmlspecialchars($_POST['friday_note']);

// Update Saturday deal
$saturday_title = htmlspecialchars($_POST['saturday_title']);
$saturday_description = htmlspecialchars($_POST['saturday_description']);
$saturday_note = htmlspecialchars($_POST['saturday_note']);

// Check if weekly_deals table exists, if not create it
$sql = "CREATE TABLE IF NOT EXISTS weekly_deals (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  day VARCHAR(20) NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  note TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
  // Table created or already exists
  
  // Update or insert Monday deal
  $sql = "INSERT INTO weekly_deals (day, title, description, note) 
          VALUES ('Monday', ?, ?, ?) 
          ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description), note = VALUES(note)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sss", $monday_title, $monday_description, $monday_note);
  $stmt->execute();
  
  // Update or insert Wednesday deal
  $sql = "INSERT INTO weekly_deals (day, title, description, note) 
          VALUES ('Wednesday', ?, ?, ?) 
          ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description), note = VALUES(note)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sss", $wednesday_title, $wednesday_description, $wednesday_note);
  $stmt->execute();
  
  // Update or insert Friday deal
  $sql = "INSERT INTO weekly_deals (day, title, description, note) 
          VALUES ('Friday', ?, ?, ?) 
          ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description), note = VALUES(note)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sss", $friday_title, $friday_description, $friday_note);
  $stmt->execute();
  
  // Update or insert Saturday deal
  $sql = "INSERT INTO weekly_deals (day, title, description, note) 
          VALUES ('Saturday', ?, ?, ?) 
          ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description), note = VALUES(note)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sss", $saturday_title, $saturday_description, $saturday_note);
  $stmt->execute();
  
  $success_message = "Тижневі знижки успішно оновлено!";
} else {
  $error_message = "Помилка створення таблиці: " . $conn->error;
}
}

// Get current weekly deals
$deals = [
'Monday' => ['title' => 'День для пенсіонерів', 'description' => '15% знижки для клієнтів старше 60 років', 'note' => 'Потрібне посвідчення для підтвердження віку'],
'Wednesday' => ['title' => 'Сімейний день', 'description' => '10% знижки на дитячі товари', 'note' => 'Поширюється на всі товари для догляду за дитиною'],
'Friday' => ['title' => 'Готові до вихідних', 'description' => 'Купи 1 отримай 50% знижки на другий вітамін', 'note' => 'Другий товар повинен бути рівної або меншої вартості'],
'Saturday' => ['title' => 'День краси', 'description' => '20% знижки на всю косметику', 'note' => 'Включає всі товари для краси та догляду за шкірою']
];

// Check if weekly_deals table exists
$table_exists = $conn->query("SHOW TABLES LIKE 'weekly_deals'")->num_rows > 0;

if ($table_exists) {
// Get deals from database
$sql = "SELECT * FROM weekly_deals";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
      $deals[$row['day']] = [
          'title' => $row['title'],
          'description' => $row['description'],
          'note' => $row['note']
      ];
  }
}
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Farmatek Адмін - Тижневі знижки</title>
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
          <li><a href="add_promotion.php"><i class="fas fa-plus-circle"></i> Додати акцію</a></li>
          <li class="active"><a href="weekly_deals.php"><i class="fas fa-calendar-week"></i> Тижневі знижки</a></li>
          <li><a href="welcome_section.php"><i class="fas fa-home"></i> Вітальна секція</a></li>
          <li><a href="seasonal_promo.php"><i class="fas fa-percentage"></i> Сезонна акція</a></li>
          <li><a href="store_info.php"><i class="fas fa-store"></i> Інформація про магазин</a></li>
          <li><a href="subscribers.php"><i class="fas fa-users"></i> Підписники</a></li>
          <li><a href="logo_settings.php"><i class="fas fa-image"></i> Логотип</a></li>
          <li><a href="dashboard.php?logout=1"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
      </ul>
  </nav>
</div>

<div class="admin-content">
  <header>
      <h1>Тижневі знижки</h1>
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
          <h2>Керування тижневими знижками</h2>
          <p>Встановіть спеціальні знижки для різних днів тижня</p>
      </div>
      
      <form method="POST" action="" class="admin-form">
          <div class="weekly-deals-grid">
              <!-- Monday Deal -->
              <div class="deal-edit-card">
                  <h3>Понеділок</h3>
                  <div class="form-group">
                      <label for="monday_title">Назва знижки</label>
                      <input type="text" id="monday_title" name="monday_title" value="<?php echo $deals['Monday']['title']; ?>" required>
                  </div>
                  <div class="form-group">
                      <label for="monday_description">Опис</label>
                      <input type="text" id="monday_description" name="monday_description" value="<?php echo $deals['Monday']['description']; ?>" required>
                  </div>
                  <div class="form-group">
                      <label for="monday_note">Примітка (необов'язково)</label>
                      <input type="text" id="monday_note" name="monday_note" value="<?php echo $deals['Monday']['note']; ?>">
                  </div>
              </div>
              
              <!-- Wednesday Deal -->
              <div class="deal-edit-card">
                  <h3>Середа</h3>
                  <div class="form-group">
                      <label for="wednesday_title">Назва знижки</label>
                      <input type="text" id="wednesday_title" name="wednesday_title" value="<?php echo $deals['Wednesday']['title']; ?>" required>
                  </div>
                  <div class="form-group">
                      <label for="wednesday_description">Опис</label>
                      <input type="text" id="wednesday_description" name="wednesday_description" value="<?php echo $deals['Wednesday']['description']; ?>" required>
                  </div>
                  <div class="form-group">
                      <label for="wednesday_note">Примітка (необов'язково)</label>
                      <input type="text" id="wednesday_note" name="wednesday_note" value="<?php echo $deals['Wednesday']['note']; ?>">
                  </div>
              </div>
              
              <!-- Friday Deal -->
              <div class="deal-edit-card">
                  <h3>П'ятниця</h3>
                  <div class="form-group">
                      <label for="friday_title">Назва знижки</label>
                      <input type="text" id="friday_title" name="friday_title" value="<?php echo $deals['Friday']['title']; ?>" required>
                  </div>
                  <div class="form-group">
                      <label for="friday_description">Опис</label>
                      <input type="text" id="friday_description" name="friday_description" value="<?php echo $deals['Friday']['description']; ?>" required>
                  </div>
                  <div class="form-group">
                      <label for="friday_note">Примітка (необов'язково)</label>
                      <input type="text" id="friday_note" name="friday_note" value="<?php echo $deals['Friday']['note']; ?>">
                  </div>
              </div>
              
              <!-- Saturday Deal -->
              <div class="deal-edit-card">
                  <h3>Субота</h3>
                  <div class="form-group">
                      <label for="saturday_title">Назва знижки</label>
                      <input type="text" id="saturday_title" name="saturday_title" value="<?php echo $deals['Saturday']['title']; ?>" required>
                  </div>
                  <div class="form-group">
                      <label for="saturday_description">Опис</label>
                      <input type="text" id="saturday_description" name="saturday_description" value="<?php echo $deals['Saturday']['description']; ?>" required>
                  </div>
                  <div class="form-group">
                      <label for="saturday_note">Примітка (необов'язково)</label>
                      <input type="text" id="saturday_note" name="saturday_note" value="<?php echo $deals['Saturday']['note']; ?>">
                  </div>
              </div>
          </div>
          
          <div class="form-actions">
              <button type="submit" class="btn">Зберегти тижневі знижки</button>
          </div>
      </form>
  </div>
  
  <div class="content-section">
      <div class="section-header">
          <h2>Попередній перегляд</h2>
          <p>Так ваші тижневі знижки будуть відображатися на сайті</p>
      </div>
      
      <div class="deals-preview">
          <div class="deals-grid">
              <div class="deal-card">
                  <div class="deal-day">Понеділок</div>
                  <h3><?php echo $deals['Monday']['title']; ?></h3>
                  <p><?php echo $deals['Monday']['description']; ?></p>
                  <?php if (!empty($deals['Monday']['note'])): ?>
                      <p class="deal-note"><?php echo $deals['Monday']['note']; ?></p>
                  <?php endif; ?>
              </div>
              
              <div class="deal-card">
                  <div class="deal-day">Середа</div>
                  <h3><?php echo $deals['Wednesday']['title']; ?></h3>
                  <p><?php echo $deals['Wednesday']['description']; ?></p>
                  <?php if (!empty($deals['Wednesday']['note'])): ?>
                      <p class="deal-note"><?php echo $deals['Wednesday']['note']; ?></p>
                  <?php endif; ?>
              </div>
              
              <div class="deal-card">
                  <div class="deal-day">П'ятниця</div>
                  <h3><?php echo $deals['Friday']['title']; ?></h3>
                  <p><?php echo $deals['Friday']['description']; ?></p>
                  <?php if (!empty($deals['Friday']['note'])): ?>
                      <p class="deal-note"><?php echo $deals['Friday']['note']; ?></p>
                  <?php endif; ?>
              </div>
              
              <div class="deal-card">
                  <div class="deal-day">Субота</div>
                  <h3><?php echo $deals['Saturday']['title']; ?></h3>
                  <p><?php echo $deals['Saturday']['description']; ?></p>
                  <?php if (!empty($deals['Saturday']['note'])): ?>
                      <p class="deal-note"><?php echo $deals['Saturday']['note']; ?></p>
                  <?php endif; ?>
              </div>
          </div>
      </div>
  </div>
</div>

<style>
  .weekly-deals-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
  }
  
  .deal-edit-card {
      background-color: #f9f9f9;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  }
  
  .deal-edit-card h3 {
      color: #2c7873;
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
  }
  
  .deals-preview {
      background-color: #f9f9f9;
      border-radius: 10px;
      padding: 30px;
  }
  
  .deals-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
  }
  
  .deal-card {
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      padding: 20px;
      text-align: center;
  }
  
  .deal-day {
      display: inline-block;
      background-color: #2c7873;
      color: white;
      padding: 8px 20px;
      border-radius: 20px;
      margin-bottom: 15px;
  }
  
  .deal-note {
      font-size: 0.9rem;
      color: #666;
      margin-top: 15px;
  }
</style>
</body>
</html>

