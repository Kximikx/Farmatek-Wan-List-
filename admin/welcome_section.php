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
$text1 = htmlspecialchars($_POST['text1']);
$text2 = htmlspecialchars($_POST['text2']);
$button_text = htmlspecialchars($_POST['button_text']);

// Handle image upload
$image_url = $_POST['current_image']; // Keep existing image by default

if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $allowed = array('jpg', 'jpeg', 'png', 'gif');
    $filename = $_FILES['image']['name'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    
    if(in_array(strtolower($ext), $allowed)) {
        $new_filename = uniqid() . '.' . $ext;
        $upload_path = '../uploads/' . $new_filename;
        
        if(move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            $image_url = 'uploads/' . $new_filename;
        }
    }
}

// Check if welcome_section table exists, if not create it
$sql = "CREATE TABLE IF NOT EXISTS welcome_section (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    text1 TEXT NOT NULL,
    text2 TEXT NOT NULL,
    button_text VARCHAR(50) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    // Table created or already exists
    
    // Check if record exists
    $check_sql = "SELECT id FROM welcome_section LIMIT 1";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        // Update existing record
        $sql = "UPDATE welcome_section SET title = ?, text1 = ?, text2 = ?, button_text = ?, image_url = ? WHERE id = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $title, $text1, $text2, $button_text, $image_url);
    } else {
        // Insert new record
        $sql = "INSERT INTO welcome_section (title, text1, text2, button_text, image_url) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $title, $text1, $text2, $button_text, $image_url);
    }
    
    if ($stmt->execute()) {
        $success_message = "Вітальну секцію успішно оновлено!";
    } else {
        $error_message = "Помилка: " . $stmt->error;
    }
    
    $stmt->close();
} else {
    $error_message = "Помилка створення таблиці: " . $conn->error;
}
}

// Get current welcome section data
$welcome_section = [
  'title' => 'Ласкаво просимо до Farmatek',
  'text1' => 'Ми піклуємося про ваше здоров\'я та пропонуємо широкий асортимент ліків, вітамінів та товарів для здоров\'я за найкращими цінами.',
  'text2' => 'Наша команда професійних фармацевтів завжди готова надати вам кваліфіковану консультацію та допомогти з вибором необхідних препаратів.',
  'button_text' => 'Відвідайте нас',
  'image_url' => 'https://source.unsplash.com/random/600x400/?pharmacy'
];

// Check if welcome_section table exists
$table_exists = $conn->query("SHOW TABLES LIKE 'welcome_section'")->num_rows > 0;

if ($table_exists) {
  // Get data from database
  $sql = "SELECT * FROM welcome_section LIMIT 1";
  $result = $conn->query($sql);
  
  if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $welcome_section = [
          'title' => $row['title'],
          'text1' => $row['text1'],
          'text2' => $row['text2'],
          'button_text' => $row['button_text'],
          'image_url' => $row['image_url']
      ];
  }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Farmatek Адмін - Вітальна секція</title>
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
            <li class="active"><a href="welcome_section.php"><i class="fas fa-home"></i> Вітальна секція</a></li>
            <li><a href="seasonal_promo.php"><i class="fas fa-percentage"></i> Сезонна акція</a></li>
            <li><a href="subscribers.php"><i class="fas fa-users"></i> Підписники</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Налаштування</a></li>
            <li><a href="dashboard.php?logout=1"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
        </ul>
    </nav>
</div>

<div class="admin-content">
    <header>
        <h1>Вітальна секція</h1>
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
            <h2>Редагувати вітальну секцію</h2>
            <p>Ця секція відображається у верхній частині головної сторінки</p>
        </div>
        
        <form method="POST" action="" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label for="title">Заголовок секції</label>
                <input type="text" id="title" name="title" value="<?php echo $welcome_section['title']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="text1">Перший абзац</label>
                <textarea id="text1" name="text1" rows="3" required><?php echo $welcome_section['text1']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="text2">Другий абзац</label>
                <textarea id="text2" name="text2" rows="3" required><?php echo $welcome_section['text2']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="button_text">Текст кнопки</label>
                <input type="text" id="button_text" name="button_text" value="<?php echo $welcome_section['button_text']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="image">Зображення секції</label>
                <?php if (!empty($welcome_section['image_url'])): ?>
                    <div class="current-image">
                        <img src="../<?php echo $welcome_section['image_url']; ?>" alt="Поточне зображення" width="300">
                        <p>Поточне зображення</p>
                    </div>
                <?php endif; ?>
                <input type="hidden" name="current_image" value="<?php echo $welcome_section['image_url']; ?>">
                <input type="file" id="image" name="image" accept="image/*">
                <small>Залиште порожнім, щоб зберегти поточне зображення. Рекомендований розмір: 600x400 пікселів</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Оновити вітальну секцію</button>
                <a href="dashboard.php" class="btn btn-secondary">Скасувати</a>
            </div>
        </form>
    </div>
    
    <div class="content-section">
        <div class="section-header">
            <h2>Попередній перегляд</h2>
        </div>
        
        <div class="preview-container">
            <div class="welcome-preview">
                <div class="welcome-text">
                    <h1><?php echo $welcome_section['title']; ?></h1>
                    <p><?php echo $welcome_section['text1']; ?></p>
                    <p><?php echo $welcome_section['text2']; ?></p>
                    <button class="btn"><?php echo $welcome_section['button_text']; ?></button>
                </div>
                <div class="welcome-image">
                    <img src="../<?php echo $welcome_section['image_url']; ?>" alt="Вітальне зображення">
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
    
    .welcome-preview {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        align-items: center;
    }
    
    .welcome-text h1 {
        color: #2c7873;
        font-size: 2rem;
        margin-bottom: 15px;
    }
    
    .welcome-text p {
        margin-bottom: 15px;
    }
    
    .welcome-image img {
        width: 100%;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    @media (max-width: 768px) {
        .welcome-preview {
            grid-template-columns: 1fr;
        }
    }
</style>
</body>
</html>

