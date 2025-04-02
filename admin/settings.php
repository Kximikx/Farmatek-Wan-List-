<?php
session_start();

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
header("Location: index.php");
exit;
}

// Include database connection
require_once 'db_connect.php';

// Handle form submission for changing password
if (isset($_POST['change_password'])) {
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

// Check if current password is correct (hardcoded for simplicity)
// In a real application, this would be stored in a database with proper hashing
if ($current_password === "farmatek123") {
    if ($new_password === $confirm_password) {
        // In a real application, you would update the password in the database
        // For this example, we'll just show a success message
        $password_success = "Пароль успішно змінено! Будь ласка, оновіть пароль у файлі index.php.";
        
        // Instructions for manual update
        $update_instructions = "Щоб завершити зміну пароля, будь ласка, вручну оновіть пароль у файлі admin/index.php, рядок 11.";
    } else {
        $password_error = "Нові паролі не співпадають.";
    }
} else {
    $password_error = "Поточний пароль невірний.";
}
}

// Handle form submission for store information
if (isset($_POST['update_store'])) {
$store_name = htmlspecialchars($_POST['store_name']);
$store_address = htmlspecialchars($_POST['store_address']);
$store_phone = htmlspecialchars($_POST['store_phone']);
$store_email = htmlspecialchars($_POST['store_email']);
$working_hours = htmlspecialchars($_POST['working_hours']);

// Handle logo upload
$logo_path = isset($_POST['current_logo']) ? $_POST['current_logo'] : '';

if(isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
    $allowed = array('jpg', 'jpeg', 'png', 'gif', 'svg');
    $filename = $_FILES['logo']['name'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    
    if(in_array(strtolower($ext), $allowed)) {
        $new_filename = 'logo_' . uniqid() . '.' . $ext;
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
    
    // Update or insert store settings
    $settings = [
        'store_name' => $store_name,
        'store_address' => $store_address,
        'store_phone' => $store_phone,
        'store_email' => $store_email,
        'working_hours' => $working_hours,
        'logo_path' => $logo_path
    ];
    
    foreach ($settings as $name => $value) {
        $sql = "INSERT INTO settings (setting_name, setting_value) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $name, $value);
        $stmt->execute();
    }
    
    $store_success = "Інформацію про магазин успішно оновлено!";
} else {
    $store_error = "Помилка створення таблиці налаштувань: " . $conn->error;
}
}

// Get current store settings
$store_settings = [
'store_name' => 'Farmatek',
'store_address' => 'вул. Здоров\'я 123, Медичний район, Місто, Країна, 12345',
'store_phone' => '+1 (123) 456-7890',
'store_email' => 'info@farmatek.com',
'working_hours' => 'Понеділок - П\'ятниця: 8:00 - 20:00
Субота: 9:00 - 18:00
Неділя: 10:00 - 16:00',
'logo_path' => 'default'
];

// Check if settings table exists
$table_exists = $conn->query("SHOW TABLES LIKE 'settings'")->num_rows > 0;

if ($table_exists) {
// Get settings from database
$sql = "SELECT * FROM settings";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $store_settings[$row['setting_name']] = $row['setting_value'];
    }
}
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Farmatek Адмін - Налаштування</title>
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
            <li class="active"><a href="settings.php"><i class="fas fa-cog"></i> Налаштування</a></li>
            <li><a href="dashboard.php?logout=1"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
        </ul>
    </nav>
</div>

<div class="admin-content">
    <header>
        <h1>Нала��тування</h1>
        <div class="user-info">
            <span>Вітаємо, Адміністратор</span>
            <a href="dashboard.php?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Вийти</a>
        </div>
    </header>
    
    <div class="content-section">
        <div class="section-header">
            <h2>Зміна пароля</h2>
        </div>
        
        <?php if (isset($password_success)): ?>
            <div class="alert success"><?php echo $password_success; ?></div>
            <div class="alert info"><?php echo $update_instructions; ?></div>
        <?php endif; ?>
        
        <?php if (isset($password_error)): ?>
            <div class="alert error"><?php echo $password_error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" class="admin-form">
            <div class="form-group">
                <label for="current_password">Поточний пароль</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">Новий пароль</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Підтвердіть новий пароль</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="change_password" class="btn">Змінити пароль</button>
            </div>
        </form>
    </div>
    
    <div class="content-section">
        <div class="section-header">
            <h2>Інформація про магазин</h2>
        </div>
        
        <?php if (isset($store_success)): ?>
            <div class="alert success"><?php echo $store_success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($store_error)): ?>
            <div class="alert error"><?php echo $store_error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label for="store_name">Назва магазину</label>
                <input type="text" id="store_name" name="store_name" value="<?php echo $store_settings['store_name']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="logo">Логотип магазину</label>
                <?php if (!empty($store_settings['logo_path']) && $store_settings['logo_path'] != 'default'): ?>
                    <div class="current-image">
                        <img src="../<?php echo $store_settings['logo_path']; ?>" alt="Поточний логотип" style="max-height: 100px;">
                        <p>Поточний логотип</p>
                    </div>
                <?php else: ?>
                    <div class="current-image">
                        <div style="font-size: 50px; color: #2c7873;"><i class="fas fa-mortar-pestle"></i></div>
                        <p>Стандартний логотип (іконка)</p>
                    </div>
                <?php endif; ?>
                <input type="hidden" name="current_logo" value="<?php echo $store_settings['logo_path']; ?>">
                <input type="file" id="logo" name="logo" accept="image/*">
                <small>Завантажте зображення логотипу. Рекомендований розмір: 200x80 пікселів. Підтримувані формати: JPG, PNG, GIF, SVG.</small>
            </div>
            
            <div class="form-group">
                <label for="store_address">Адреса магазину</label>
                <input type="text" id="store_address" name="store_address" value="<?php echo $store_settings['store_address']; ?>" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="store_phone">Номер телефону</label>
                    <input type="text" id="store_phone" name="store_phone" value="<?php echo $store_settings['store_phone']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="store_email">Email адреса</label>
                    <input type="email" id="store_email" name="store_email" value="<?php echo $store_settings['store_email']; ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="working_hours">Години роботи</label>
                <textarea id="working_hours" name="working_hours" rows="4" required><?php echo $store_settings['working_hours']; ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="update_store" class="btn">Оновити інформацію про магазин</button>
            </div>
        </form>
    </div>
    
    <div class="content-section">
        <div class="section-header">
            <h2>Інформація про базу даних</h2>
        </div>
        
        <div class="info-grid">
            <div class="info-item">
                <h3>Назва бази даних</h3>
                <p>farmatek</p>
            </div>
            
            <div class="info-item">
                <h3>Таблиці</h3>
                <ul>
                    <?php
                    $tables_result = $conn->query("SHOW TABLES");
                    if ($tables_result->num_rows > 0) {
                        while($table = $tables_result->fetch_array()) {
                            echo "<li>{$table[0]}</li>";
                        }
                    } else {
                        echo "<li>Таблиці не знайдено</li>";
                    }
                    ?>
                </ul>
            </div>
            
            <div class="info-item">
                <h3>Акції</h3>
                <p>
                    <?php
                    $count = $conn->query("SELECT COUNT(*) as total FROM promotions")->fetch_assoc();
                    echo $count['total'] ?? 0;
                    ?> записів
                </p>
            </div>
            
            <div class="info-item">
                <h3>Запити</h3>
                <p>
                    <?php
                    $count = $conn->query("SELECT COUNT(*) as total FROM inquiries")->fetch_assoc();
                    echo $count['total'] ?? 0;
                    ?> записів
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }
    
    .info-item {
        background-color: #f9f9f9;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .info-item h3 {
        color: #2c7873;
        margin-bottom: 10px;
        font-size: 1.1rem;
    }
    
    .info-item ul {
        list-style: none;
        padding-left: 0;
    }
    
    .info-item ul li {
        padding: 5px 0;
        border-bottom: 1px solid #eee;
    }
    
    .info-item ul li:last-child {
        border-bottom: none;
    }
    
    .alert.info {
        background-color: #d1ecf1;
        color: #0c5460;
    }
</style>
</body>
</html>

