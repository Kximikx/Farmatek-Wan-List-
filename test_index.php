<?php
// Включити відображення помилок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Базова HTML-структура
echo "<!DOCTYPE html>
<html>
<head>
    <title>Тест Farmatek</title>
    <meta charset='UTF-8'>
</head>
<body>
    <h1>Тестова сторінка Farmatek</h1>
    <p>Якщо ви бачите цей текст, PHP працює правильно.</p>";

// Спроба підключення до бази даних
try {
    $servername = "localhost";
    $username = "if0_38649041";
    $password = "nhGCo8Rq2AsmRh";
    $dbname = "if0_38649041_farmatek";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo "<p>Помилка підключення до бази даних: " . $conn->connect_error . "</p>";
    } else {
        echo "<p>Підключення до бази даних успішне!</p>";
    }
} catch (Exception $e) {
    echo "<p>Виникла помилка: " . $e->getMessage() . "</p>";
}

echo "</body>
</html>";
?>

