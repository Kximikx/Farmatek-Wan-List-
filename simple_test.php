<?php
// Простий тест для перевірки PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Простий тест PHP</h1>";
echo "PHP працює правильно!<br>";
echo "Версія PHP: " . phpversion() . "<br>";
echo "Поточний час: " . date('Y-m-d H:i:s') . "<br>";

// Тест сесії
session_start();
echo "Сесія працює<br>";

// Тест підключення до бази даних
echo "<h2>Тест бази даних</h2>";
try {
    $servername = "sql312.infinityfree.com";
    $username = "if0_38649041";
    $password = "nhGCo8Rq2AsmRh";
    $dbname = "if0_38649041_farmatek";

    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8");

    if ($conn->connect_error) {
        echo "Помилка підключення: " . $conn->connect_error . "<br>";
    } else {
        echo "Підключення до бази даних успішне!<br>";
        
        // Перевірка таблиць
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            echo "Таблиці в базі даних:<br>";
            while ($row = $result->fetch_row()) {
                echo "- " . $row[0] . "<br>";
            }
        }
    }
} catch (Exception $e) {
    echo "Помилка: " . $e->getMessage() . "<br>";
}

echo "<h2>Тест файлів</h2>";
$files = [
    'admin/welcome_section.php',
    'admin/dashboard.php',
    'admin/db_connect.php',
    'admin/admin-style.css'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✓ Файл $file існує<br>";
    } else {
        echo "✗ Файл $file НЕ існує<br>";
    }
}

echo "<h2>Посилання для тестування</h2>";
echo '<a href="admin/index.php">Вхід в адмін-панель</a><br>';
echo '<a href="index.php">Головна сторінка</a><br>';
?>
