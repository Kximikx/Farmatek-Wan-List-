<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/html; charset=UTF-8');

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

// Include database connection ($conn = new mysqli(...))
require_once 'db_connect.php';

// Helper: HTML-escape for output
function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

// Ensure uploads dir exists
$uploadDir = realpath(__DIR__ . '/../uploads');
if ($uploadDir === false) {
    // try to create
    $tryCreate = @mkdir(__DIR__ . '/../uploads', 0755, true);
    $uploadDir = realpath(__DIR__ . '/../uploads');
}

// Create table and enforce single-row model with id=1
$createSql = "
CREATE TABLE IF NOT EXISTS welcome_section (
    id INT(11) NOT NULL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    text1 TEXT NOT NULL,
    text2 TEXT NOT NULL,
    button_text VARCHAR(50) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
if (!$conn->query($createSql)) {
    $error_message = "Помилка створення таблиці: " . $conn->error;
}

// Ensure there is at least a placeholder row with id=1
$ensureSql = "INSERT IGNORE INTO welcome_section (id, title, text1, text2, button_text, image_url) 
              VALUES (1, 'Ласкаво просимо до Farmatek',
                         'Ми піклуємося про ваше здоров\'я та пропонуємо широкий асортимент ліків, вітамінів та товарів для здоров\'я за найкращими цінами.',
                         'Наша команда професійних фармацевтів завжди готова надати вам кваліфіковану консультацію та допомогти з вибором необхідних препаратів.',
                         'Відвідайте нас',
                         'https://source.unsplash.com/random/600x400/?pharmacy')";
$conn->query($ensureSql);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Store raw values; escape only on output
    $title = trim($_POST['title'] ?? '');
    $text1 = trim($_POST['text1'] ?? '');
    $text2 = trim($_POST['text2'] ?? '');
    $button_text = trim($_POST['button_text'] ?? '');
    $image_url = trim($_POST['current_image'] ?? '');

    // Validate minimal fields
    if ($title === '' || $text1 === '' || $text2 === '' || $button_text === '') {
        $error_message = "Будь ласка, заповніть усі поля форми.";
    } else {
        // Handle image upload (optional)
        if (isset($_FILES['image']) && is_array($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmp = $_FILES['image']['tmp_name'];
            $size = $_FILES['image']['size'] ?? 0;

            // Basic limits: 5MB
            $maxSize = 5 * 1024 * 1024;
            if ($size > $maxSize) {
                $error_message = "Файл завеликий. Максимальний розмір 5 МБ.";
            } else {
                // Validate image real type
                $imgInfo = @getimagesize($tmp);
                $allowedMime = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
                if ($imgInfo === false || !isset($allowedMime[$imgInfo['mime']])) {
                    $error_message = "Невірний формат зображення. Дозволені: JPG, PNG, GIF, WEBP.";
                } else {
                    $ext = $allowedMime[$imgInfo['mime']];
                    $newName = bin2hex(random_bytes(8)) . '.' . $ext;

                    // Ensure uploads dir exists
                    $uploadsPath = __DIR__ . '/../uploads';
                    if (!is_dir($uploadsPath)) {
                        @mkdir($uploadsPath, 0755, true);
                    }
                    $dest = $uploadsPath . '/' . $newName;

                    if (move_uploaded_file($tmp, $dest)) {
                        // Store web-accessible relative path from site root, e.g. /uploads/...
                        $image_url = '/uploads/' . $newName;
                    } else {
                        $error_message = "Не вдалося зберегти файл зображення.";
                    }
                }
            }
        }

        // If no errors so far, update row id=1
        if (!isset($error_message)) {
            $sql = "UPDATE welcome_section
                    SET title = ?, text1 = ?, text2 = ?, button_text = ?, image_url = ?
                    WHERE id = 1";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sssss", $title, $text1, $text2, $button_text, $image_url);
                if ($stmt->execute()) {
                    $success_message = "Вітальну секцію успішно оновлено!";
                } else {
                    $error_message = "Помилка під час оновлення: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Помилка підготовки запиту: " . $conn->error;
            }
        }
    }
}

// Get current welcome section data (id=1)
$welcome_section = [
    'title' => 'Ласкаво просимо до Farmatek',
    'text1' => 'Ми піклуємося про ваше здоров\'я та пропонуємо широкий асортимент ліків, вітамінів та товарів для здоров\'я за найкращими цінами.',
    'text2' => 'Наша команда професійних фармацевтів завжди готова надати вам кваліфіковану консультацію та допомогти з вибором необхідних препаратів.',
    'button_text' => 'Відвідайте нас',
    'image_url' => 'https://source.unsplash.com/random/600x400/?pharmacy'
];

$res = $conn->query("SELECT title, text1, text2, button_text, image_url FROM welcome_section WHERE id = 1 LIMIT 1");
if ($res && $res->num_rows === 1) {
    $row = $res->fetch_assoc();
    $welcome_section = [
        'title' => $row['title'],
        'text1' => $row['text1'],
        'text2' => $row['text2'],
        'button_text' => $row['button_text'],
        'image_url' => $row['image_url']
    ];
}

// Helper: build image src without breaking absolute URLs
function img_src($path) {
    if (preg_match('~^https?://~i', $path)) {
        return $path; // absolute URL
    }
    // ensure leading slash for site-root based
    if ($path !== '' && $path[0] !== '/') {
        $path = '/' . $path;
    }
    return $path;
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
            <li class="active"><a href="welcome_section.php"><i class="fas fa-home"></i> Вітальна секція</a></li>
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
        <h1>Вітальна секція</h1>
        <div class="user-info">
            <span>Вітаємо, Адміністратор</span>
            <a href="dashboard.php?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Вийти</a>
        </div>
    </header>

    <?php if (!empty($success_message)): ?>
        <div class="alert success"><?= e($success_message) ?></div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alert error"><?= e($error_message) ?></div>
    <?php endif; ?>

    <div class="content-section">
        <div class="section-header">
            <h2>Редагувати вітальну секцію</h2>
            <p>Ця секція відображається у верхній частині головної сторінки</p>
        </div>

        <form method="POST" action="" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label for="title">Заголовок секції</label>
                <input type="text" id="title" name="title" value="<?= e($welcome_section['title']) ?>" required>
            </div>

            <div class="form-group">
                <label for="text1">Перший абзац</label>
                <textarea id="text1" name="text1" rows="3" required><?= e($welcome_section['text1']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="text2">Другий абзац</label>
                <textarea id="text2" name="text2" rows="3" required><?= e($welcome_section['text2']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="button_text">Текст кнопки</label>
                <input type="text" id="button_text" name="button_text" value="<?= e($welcome_section['button_text']) ?>" required>
            </div>

            <div class="form-group">
                <label for="image">Зображення секції</label>
                <?php if (!empty($welcome_section['image_url'])): ?>
                    <div class="current-image">
                        <img src="<?= e(img_src($welcome_section['image_url'])) ?>" alt="Поточне зображення" width="300">
                        <p>Поточне зображення</p>
                    </div>
                <?php endif; ?>
                <input type="hidden" name="current_image" value="<?= e($welcome_section['image_url']) ?>">
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
                    <h1><?= e($welcome_section['title']) ?></h1>
                    <p><?= e($welcome_section['text1']) ?></p>
                    <p><?= e($welcome_section['text2']) ?></p>
                    <button class="btn"><?= e($welcome_section['button_text']) ?></button>
                </div>
                <div class="welcome-image">
                    <img src="<?= e(img_src($welcome_section['image_url'])) ?>" alt="Вітальне зображення">
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
