<?php
// Файл для діагностики проблем з вітальною секцією
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Діагностика вітальної секції</h1>";

// Перевірка сесії
session_start();
echo "<h2>Сесія</h2>";
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    echo "Користувач авторизований<br>";
} else {
    echo "Користувач НЕ авторизований<br>";
}

// Перевірка підключення до бази даних
echo "<h2>База даних</h2>";
try {
    require_once 'admin/db_connect.php';
    echo "Підключення до бази даних успішне<br>";
    
    // Перевірка таблиці welcome_section
    $table_exists = $conn->query("SHOW TABLES LIKE 'welcome_section'")->num_rows > 0;
    if ($table_exists) {
        echo "Таблиця welcome_section існує<br>";
        
        // Показати дані з таблиці
        $sql = "SELECT * FROM welcome_section LIMIT 1";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            echo "Дані в таблиці знайдено:<br>";
            $row = $result->fetch_assoc();
            echo "<pre>";
            print_r($row);
            echo "</pre>";
        } else {
            echo "Таблиця welcome_section порожня<br>";
        }
    } else {
        echo "Таблиця welcome_section НЕ існує<br>";
    }
    
} catch (Exception $e) {
    echo "Помилка: " . $e->getMessage() . "<br>";
}

// Перевірка файлів
echo "<h2>Файли</h2>";
$files_to_check = [
    'admin/welcome_section.php',
    'admin/admin-style.css',
    'admin/admin-pages.css',
    'admin/db_connect.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "Файл $file існує<br>";
    } else {
        echo "Файл $file НЕ існує!<br>";
    }
}

// Перевірка директорії uploads
echo "<h2>Директорія uploads</h2>";
if (is_dir('uploads')) {
    echo "Директорія uploads існує<br>";
    if (is_writable('uploads')) {
        echo "Директорія uploads доступна для запису<br>";
    } else {
        echo "Директорія uploads НЕ доступна для запису!<br>";
    }
} else {
    echo "Директорія uploads НЕ існує!<br>";
    // Спробувати створити
    if (mkdir('uploads', 0777, true)) {
        echo "Директорію uploads створено<br>";
    } else {
        echo "Не вдалося створити директорію uploads<br>";
    }
}
?>
