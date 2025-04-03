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
    // Handle logo upload
    $logo_path = isset($_POST['current_logo']) ? $_POST['current_logo'] : 'default';

    if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $allowed = array('jpg', 'jpeg', 'png', 'gif', 'svg');
        $filename = $_FILES['logo']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if(in_array(strtolower($ext), $allowed)) {
            $new_filename = 'logo_' . uniqid() . '.' . $ext;
            
            // Create uploads directory if it doesn't exist
            if (!file_exists('../uploads')) {
                mkdir('../uploads', 0777, true);
            }
            
            $upload_path = '../uploads/' . $new_filename;
            
            if(move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                // Delete old logo if it exists and is not the default
                if (!empty($logo_path) && $logo_path != 'default' && file_exists('../' . $logo_path)) {
                    unlink('../' . $logo_path);
                }
                
                $logo_path = 'uploads/' . $new_filename;
            }
        }
    }

    // Check if settings table exists, if not create it
    $sql = "CREATE TABLE IF NOT EXISTS settings (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        setting_name VARCHAR(50) NOT NULL UNIQUE,
        setting_value TEXT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql) === TRUE) {
        // Table created or already exists
        
        // Update or insert logo setting
        $sql = "INSERT INTO settings (setting_name, setting_value) 
                VALUES ('logo_path', ?) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $logo_path);
        
        if ($stmt->execute()) {
            $success_message = "Логотип успішно оновлено!";
        } else {
            $error_message = "Помилка: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        $error_message = "Помилка ство��ення таблиці налаштувань: " . $conn->error;
    }
}

// Get current logo setting
$logo_path = 'default';

// Check if settings table exists
$table_exists = $conn->query("SHOW TABLES LIKE 'settings'")->num_rows > 0;

if ($table_exists) {
    // Get logo from database
    $sql = "SELECT setting_value FROM settings WHERE setting_name = 'logo_path'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $logo_path = $row['setting_value'];
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmatek Адмін - Налаштування логотипу</title>
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
            <li><a href="seasonal_promo.php"><i class="fas fa-percentage"></i> Сезонна акція</a></li>
            <li><a href="store_info.php"><i class="fas fa-store"></i> Інформація про магазин</a></li>
            <li><a href="subscribers.php"><i class="fas fa-users"></i> Підписники</a></li>
            <li class="active"><a href="logo_settings.php"><i class="fas fa-image"></i> Логотип</a></li>
            <li><a href="dashboard.php?logout=1"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
        </ul>
    </nav>
</div>

<div class="admin-content">
    <header>
        <h1>Налаштування логотипу</h1>
        <div class="user-info">
            <span>Вітаємо, Адміністратор</span>
            <a href="dashboard.php?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Вийти</a>
        </div>
    </header>
    
    <div class="content-section">
        <div class="section-header">
            <h2>Завантаження логотипу</h2>
            <p>Завантажте зображення логотипу для вашого сайту</p>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="alert success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label for="logo">Логотип магазину</label>
                <?php if (!empty($logo_path) && $logo_path != 'default'): ?>
                    <div class="current-image">
                        <img src="../<?php echo $logo_path; ?>" alt="Поточний логотип" style="max-height: 100px;">
                        <p>Поточний логотип</p>
                    </div>
                <?php else: ?>
                    <div class="current-image">
                        <div style="font-size: 50px; color: #2c7873;"><i class="fas fa-mortar-pestle"></i></div>
                        <p>Стандартний логотип (іконка)</p>
                    </div>
                <?php endif; ?>
                <input type="hidden" name="current_logo" value="<?php echo $logo_path; ?>">
                <input type="file" id="logo" name="logo" accept="image/*" required>
                <small>Завантажте зображення логотипу. Рекомендований розмір: 200x80 пікселів. Підтримувані формати: JPG, PNG, GIF, SVG.</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Оновити логотип</button>
                <a href="dashboard.php" class="btn btn-secondary">Повернутися до панелі керування</a>
            </div>
        </form>
    </div>
    
    <div class="content-section">
        <div class="section-header">
            <h2>Попередній перегляд</h2>
            <p>Так ваш логотип буде відображатися на сайті</p>
        </div>
        
        <div class="preview-container">
            <div class="header-preview">
                <h3>Шапка сайту</h3>
                <div class="logo-preview">
                    <?php if (!empty($logo_path) && $logo_path != 'default'): ?>
                        <img src="../<?php echo $logo_path; ?>" alt="Логотип" style="max-height: 60px; max-width: 200px;">
                    <?php else: ?>
                        <div style="font-size: 30px; color: #2c7873;"><i class="fas fa-mortar-pestle"></i> Farmatek</div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="footer-preview">
                <h3>Підвал сайту</h3>
                <div class="logo-preview">
                    <?php if (!empty($logo_path) && $logo_path != 'default'): ?>
                        <img src="../<?php echo $logo_path; ?>" alt="Логотип" style="max-height: 80px; max-width: 200px;">
                    <?php else: ?>
                        <div style="font-size: 30px; color: #fff;"><i class="fas fa-mortar-pestle"></i> Farmatek</div>
                    <?php endif; ?>
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
    
    .header-preview {
        background-color: white;
        padding: 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .footer-preview {
        background-color: #2c7873;
        color: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .logo-preview {
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
</body>
</html>

