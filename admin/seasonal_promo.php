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
$subtitle = htmlspecialchars($_POST['subtitle']);
$description = htmlspecialchars($_POST['description']);
$note = htmlspecialchars($_POST['note']);
$button_text = htmlspecialchars($_POST['button_text']);

// Handle image upload
$background_image = $_POST['current_image']; // Keep existing image by default

if(isset($_FILES['background_image']) && $_FILES['background_image']['error'] == 0) {
    $allowed = array('jpg', 'jpeg', 'png', 'gif');
    $filename = $_FILES['background_image']['name'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    
    if(in_array(strtolower($ext), $allowed)) {
        $new_filename = uniqid() . '.' . $ext;
        $upload_path = '../uploads/' . $new_filename;
        
        if(move_uploaded_file($_FILES['background_image']['tmp_name'], $upload_path)) {
            $background_image = 'uploads/' . $new_filename;
        }
    }
}

// Check if seasonal_promo table exists, if not create it
$sql = "CREATE TABLE IF NOT EXISTS seasonal_promo (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    subtitle VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    note TEXT NOT NULL,
    button_text VARCHAR(50) NOT NULL,
    background_image VARCHAR(255) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    // Table created or already exists
    
    // Check if record exists
    $check_sql = "SELECT id FROM seasonal_promo LIMIT 1";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        // Update existing record
        $sql = "UPDATE seasonal_promo SET title = ?, subtitle = ?, description = ?, note = ?, button_text = ?, background_image = ? WHERE id = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $title, $subtitle, $description, $note, $button_text, $background_image);
    } else {
        // Insert new record
        $sql = "INSERT INTO seasonal_promo (title, subtitle, description, note, button_text, background_image) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $title, $subtitle, $description, $note, $button_text, $background_image);
    }
    
    if ($stmt->execute()) {
        $success_message = "Сезонну акцію успішно оновлено!";
    } else {
        $error_message = "Помилка: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    $error_message = "Помилка створення таблиці: " . $conn->error;
}
}

// Get current seasonal promo data
$seasonal_promo = [
  'title' => 'Сезонне полегшення алергії',
  'subtitle' => 'Купи 2 отримай 1 БЕЗКОШТОВНО на всі протиалергічні препарати',
  'description' => 'Не дозволяйте сезонній алерг��ї сповільнювати вас. Відвідайте нашу аптеку, щоб скористатися цією спеціальною пропозицією!',
  'note' => 'Пропозиція дійсна тільки в магазині. Не може поєднуватися з іншими знижками.',
  'button_text' => 'Знайти наше розташування',
  'background_image' => 'https://source.unsplash.com/random/1600x900/?spring'
];

// Check if seasonal_promo table exists
$table_exists = $conn->query("SHOW TABLES LIKE 'seasonal_promo'")->num_rows > 0;

if ($table_exists) {
  // Get data from database
  $sql = "SELECT * FROM seasonal_promo LIMIT 1";
  $result = $conn->query($sql);
  
  if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $seasonal_promo = [
          'title' => $row['title'],
          'subtitle' => $row['subtitle'],
          'description' => $row['description'],
          'note' => $row['note'],
          'button_text' => $row['button_text'],
          'background_image' => $row['background_image']
      ];
  }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Farmatek Адмін - Сезонна акція</title>
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
            <li><a href="weekly_deals.php"><i class="fas fa-calendar-week"></i> Тижневі знижки</a></li>
            <li><a href="welcome_section.php"><i class="fas fa-home"></i> Вітальна секція</a></li>
            <li class="active"><a href="seasonal_promo.php"><i class="fas fa-percentage"></i> Сезонна акція</a></li>
            <li><a href="store_info.php"><i class="fas fa-store"></i> Інформація про магазин</a></li>
            <li><a href="subscribers.php"><i class="fas fa-users"></i> Підписники</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Налаштування</a></li>
            <li><a href="dashboard.php?logout=1"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
        </ul>
    </nav>
</div>

<div class="admin-content">
    <header>
        <h1>Сезонна акція</h1>
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
            <h2>Редагувати сезонну акцію</h2>
            <p>Ця секція відображається в середині головної сторінки</p>
        </div>
        
        <form method="POST" action="" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label for="title">Головний заголовок</label>
                <input type="text" id="title" name="title" value="<?php echo $seasonal_promo['title']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="subtitle">Підзаголовок</label>
                <input type="text" id="subtitle" name="subtitle" value="<?php echo $seasonal_promo['subtitle']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Опис</label>
                <textarea id="description" name="description" rows="3" required><?php echo $seasonal_promo['description']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="note">Примітка</label>
                <textarea id="note" name="note" rows="2" required><?php echo $seasonal_promo['note']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="button_text">Текст кнопки</label>
                <input type="text" id="button_text" name="button_text" value="<?php echo $seasonal_promo['button_text']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="background_image">Фонове зображення</label>
                <?php if (!empty($seasonal_promo['background_image'])): ?>
                    <div class="current-image">
                        <img src="../<?php echo $seasonal_promo['background_image']; ?>" alt="Поточний фон" width="300">
                        <p>Поточне фонове зображення</p>
                    </div>
                <?php endif; ?>
                <input type="hidden" name="current_image" value="<?php echo $seasonal_promo['background_image']; ?>">
                <input type="file" id="background_image" name="background_image" accept="image/*">
                <small>Залиште порожнім, щоб зберегти поточне зображення. Рекомендований розмір: 1600x900 пікселів</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Оновити сезонну акцію</button>
                <a href="dashboard.php" class="btn btn-secondary">Скасувати</a>
            </div>
        </form>
    </div>
    
    <div class="content-section">
        <div class="section-header">
            <h2>Попередній перегляд</h2>
        </div>
        
        <div class="preview-container">
            <div class="seasonal-preview" style="background-image: url('../<?php echo $seasonal_promo['background_image']; ?>')">
                <div class="seasonal-content">
                    <h2><?php echo $seasonal_promo['title']; ?></h2>
                    <h3><?php echo $seasonal_promo['subtitle']; ?></h3>
                    <p><?php echo $seasonal_promo['description']; ?></p>
                    <p class="seasonal-note"><?php echo $seasonal_promo['note']; ?></p>
                    <button class="btn"><?php echo $seasonal_promo['button_text']; ?></button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .preview-container {
        background-color: #f9f9f9;
        border-radius: 10px;
        padding: 30px;
    }
    
    .seasonal-preview {
        background: linear-gradient(rgba(44, 120, 115, 0.9), rgba(44, 120, 115, 0.9)), url('../<?php echo $seasonal_promo['background_image']; ?>') no-repeat center center / cover;
        color: white;
        text-align: center;
        padding: 60px 30px;
        border-radius: 10px;
    }
    
    .seasonal-content {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .seasonal-preview h2 {
        font-size: 2rem;
        margin-bottom: 10px;
    }
    
    .seasonal-preview h3 {
        font-size: 1.5rem;
        margin-bottom: 15px;
        color: #52de97;
    }
    
    .seasonal-preview p {
        margin-bottom: 15px;
    }
    
    .seasonal-note {
        font-size: 0.9rem;
        margin-bottom: 20px;
        opacity: 0.8;
    }
</style>
</body>
</html>

