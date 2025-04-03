<?php
// Включити відображення помилок безпосередньо в скрипті
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Перевірка PHP
echo "PHP працює!<br>";

// Перевірка підключення до бази даних
try {
    $servername = "localhost";
    $username = "if0_38649041";
    $password = "nhGCo8Rq2AsmRh";
    $dbname = "if0_38649041_farmatek";

    // Використання PDO для більш детальних помилок
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    // Встановлення режиму помилок PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Підключення до бази даних успішне!<br>";
    
    // Перевірка таблиць
    $tables = array("promotions", "inquiries", "subscribers", "settings", "store_info", "weekly_deals", "welcome_section", "seasonal_promo");
    
    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "Таблиця '$table' існує.<br>";
        } else {
            echo "Таблиця '$table' НЕ існує!<br>";
        }
    }
} catch(PDOException $e) {
    echo "Помилка підключення до бази даних: " . $e->getMessage() . "<br>";
}
?>

