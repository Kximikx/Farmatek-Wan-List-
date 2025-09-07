<?php
// Очищення кешу та перезапуск сесії
session_start();
session_destroy();
session_start();

// Очищення буферу виводу
if (ob_get_level()) {
    ob_end_clean();
}

// Встановлення заголовків для запобігання кешуванню
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
header('Content-Type: text/html; charset=UTF-8');

echo "<!DOCTYPE html>
<html lang='uk'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Очищення кешу</title>
</head>
<body>
    <h1>Кеш очищено</h1>
    <p>Сесія перезапущена, кеш браузера очищено.</p>
    <p><a href='index.php'>Повернутися до входу</a></p>
    <p><a href='dashboard.php'>Перейти до панелі керування</a></p>
    
    <script>
        // Очищення кешу JavaScript
        if ('caches' in window) {
            caches.keys().then(function(names) {
                names.forEach(function(name) {
                    caches.delete(name);
                });
            });
        }
        
        // Перезавантаження сторінки без кешу
        setTimeout(function() {
            window.location.reload(true);
        }, 2000);
    </script>
</body>
</html>";
?>
