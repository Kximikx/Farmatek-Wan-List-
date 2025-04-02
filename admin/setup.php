<?php
// Database configuration
$servername = "localhost";
$username = "root";  // Change to your database username
$password = "";      // Change to your database password

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS farmatek";
if ($conn->query($sql) === TRUE) {
echo "База даних успішно створена<br>";
} else {
echo "Помилка створення бази даних: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db("farmatek");

// Create promotions table
$sql = "CREATE TABLE IF NOT EXISTS promotions (
id INT(11) AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(255) NOT NULL,
description TEXT NOT NULL,
discount INT(3) NOT NULL,
old_price DECIMAL(10,2) NOT NULL,
new_price DECIMAL(10,2) NOT NULL,
image_path VARCHAR(255),
valid_until DATE NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
echo "Таблиця акцій успішно створена<br>";
} else {
echo "Помилка створення таблиці акцій: " . $conn->error . "<br>";
}

// Create inquiries table
$sql = "CREATE TABLE IF NOT EXISTS inquiries (
id INT(11) AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(255) NOT NULL,
email VARCHAR(255) NOT NULL,
phone VARCHAR(20),
inquiry_type VARCHAR(50) NOT NULL,
message TEXT NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
echo "Таблиця запитів успішно створена<br>";
} else {
echo "Помилка створення таблиці запитів: " . $conn->error . "<br>";
}

// Create subscribers table
$sql = "CREATE TABLE IF NOT EXISTS subscribers (
id INT(11) AUTO_INCREMENT PRIMARY KEY,
email VARCHAR(255) NOT NULL UNIQUE,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
echo "Таблиця підписників успішно створена<br>";
} else {
echo "Помилка створення таблиці підписників: " . $conn->error . "<br>";
}

// Create settings table
$sql = "CREATE TABLE IF NOT EXISTS settings (
id INT(11) AUTO_INCREMENT PRIMARY KEY,
setting_name VARCHAR(50) NOT NULL UNIQUE,
setting_value TEXT NOT NULL,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
echo "Таблиця налаштувань успішно створена<br>";

// Insert default settings
$default_settings = [
  ['store_name', 'Farmatek'],
  ['store_address', 'вул. Здоров\'я 123, Медичний район, Місто, Країна, 12345'],
  ['store_phone', '+1 (123) 456-7890'],
  ['store_email', 'info@farmatek.com'],
  ['working_hours', "Понеділок - П'ятниця: 8:00 - 20:00
Субота: 9:00 - 18:00
Неділя: 10:00 - 16:00"],
  ['logo_path', 'default']
];

$stmt = $conn->prepare("INSERT IGNORE INTO settings (setting_name, setting_value) VALUES (?, ?)");
foreach ($default_settings as $setting) {
  $stmt->bind_param("ss", $setting[0], $setting[1]);
  $stmt->execute();
}
$stmt->close();

} else {
echo "Помилка створення таблиці налаштувань: " . $conn->error . "<br>";
}

// Create store_info table
$sql = "CREATE TABLE IF NOT EXISTS store_info (
id INT(11) AUTO_INCREMENT PRIMARY KEY,
section_title VARCHAR(255) NOT NULL,
section_subtitle VARCHAR(255) NOT NULL,
map_embed TEXT NOT NULL,
location_title VARCHAR(255) NOT NULL,
location_address TEXT NOT NULL,
hours_title VARCHAR(255) NOT NULL,
working_hours TEXT NOT NULL,
contact_title VARCHAR(255) NOT NULL,
contact_phone VARCHAR(50) NOT NULL,
contact_email VARCHAR(255) NOT NULL,
details_title VARCHAR(255) NOT NULL,
details_text TEXT NOT NULL,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
echo "Таблиця інформації про магазин успішно створена<br>";

// Insert default store info
$sql = "INSERT IGNORE INTO store_info (
    id, 
    section_title, 
    section_subtitle, 
    map_embed, 
    location_title, 
    location_address, 
    hours_title, 
    working_hours, 
    contact_title, 
    contact_phone, 
    contact_email, 
    details_title, 
    details_text
) VALUES (
    1, 
    'Відвідайте наш магазин', 
    'Приходьте до нашої аптеки, щоб скористатися цими акціями', 
    '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3000!2d-73.9857!3d40.7484!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDQ0JzU0LjIiTiA3M8KwNTknMDguNSJX!5e0!3m2!1sen!2sus!4v1620000000000!5m2!1sen!2sus\" width=\"100%\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\"></iframe>', 
    'Наше розташування', 
    'вул. Здоров''я 123, Медичний район<br>Місто, Країна, 12345', 
    'Години роботи', 
    'Понеділок - П''ятниця: 8:00 - 20:00<br>Субота: 9:00 - 18:00<br>Неділя: 10:00 - 16:00', 
    'Зв''яжіться з нами', 
    '+1 (123) 456-7890', 
    'info@farmatek.com', 
    'Деталі акцій', 
    'Всі акції дійсні тільки в магазині.<br>Пропозиції не можуть поєднуватися, якщо не вказано інше.<br>Поки товар є в наявності.'
)";

if ($conn->query($sql) === TRUE) {
  echo "Додано типову інформацію про магазин<br>";
} else {
  echo "Помилка додавання типової інформації про магазин: " . $conn->error . "<br>";
}

} else {
echo "Помилка створення таблиці інформації про магазин: " . $conn->error . "<br>";
}

// Create weekly_deals table
$sql = "CREATE TABLE IF NOT EXISTS weekly_deals (
id INT(11) AUTO_INCREMENT PRIMARY KEY,
day VARCHAR(20) NOT NULL,
title VARCHAR(255) NOT NULL,
description TEXT NOT NULL,
note TEXT,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
UNIQUE KEY (day)
)";

if ($conn->query($sql) === TRUE) {
echo "Таблиця тижневих знижок успішно створена<br>";

// Insert default weekly deals
$default_deals = [
  ['Monday', 'День для пенсіонерів', '15% знижки для клієнтів старше 60 років', 'Потрібне посвідчення для підтвердження віку'],
  ['Wednesday', 'Сімейний день', '10% знижки на дитячі товари', 'Поширюється на всі товари для догляду за дитиною'],
  ['Friday', 'Готові до вихідних', 'Купи 1 отримай 50% знижки на другий вітамін', 'Другий товар повинен бути рівної або меншої вартості'],
  ['Saturday', 'День краси', '20% знижки на всю косметику', 'Включає всі товари для краси та догляду за шкірою']
];

$stmt = $conn->prepare("INSERT IGNORE INTO weekly_deals (day, title, description, note) VALUES (?, ?, ?, ?)");
foreach ($default_deals as $deal) {
  $stmt->bind_param("ssss", $deal[0], $deal[1], $deal[2], $deal[3]);
  $stmt->execute();
}
$stmt->close();

} else {
echo "Помилка створення таблиці тижневих знижок: " . $conn->error . "<br>";
}

// Create welcome_section table
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
echo "Таблиця вітальної секції успішно створена<br>";

// Insert default welcome section
$sql = "INSERT IGNORE INTO welcome_section (id, title, text1, text2, button_text, image_url) 
      VALUES (1, 'Ласкаво просимо до Farmatek', 
      'Ми піклуємося про ваше здоров''я та пропонуємо широкий асортимент ліків, вітамінів та товарів для здоров''я за найкращими цінами.', 
      'Наша команда професійних фармацевтів завжди готова надати вам кваліфіковану консультацію та допомогти з вибором необхідних препаратів.', 
      'Відвідайте нас', 
      'https://source.unsplash.com/random/600x400/?pharmacy')";

if ($conn->query($sql) === TRUE) {
  echo "Додано типову вітальну секцію<br>";
} else {
  echo "Помилка додавання типової вітальної секції: " . $conn->error . "<br>";
}

} else {
echo "Помилка створення таблиці вітальної секції: " . $conn->error . "<br>";
}

// Create seasonal_promo table
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
echo "Таблиця сезонних акцій успішно створена<br>";

// Insert default seasonal promo
$sql = "INSERT IGNORE INTO seasonal_promo (id, title, subtitle, description, note, button_text, background_image) 
      VALUES (1, 'Сезонне полегшення алергії', 
      'Купи 2 отримай 1 БЕЗКОШТОВНО на всі протиалергічні препарати', 
      'Не дозволяйте сезонній алергії сповільнювати вас. Відвідайте нашу аптеку, щоб скористатися цією спеціальною пропозицією!', 
      'Пропозиція дійсна тільки в магазині. Не може поєднуватися з іншими знижками.', 
      'Знайти наше розташування', 
      'https://source.unsplash.com/random/1600x900/?spring')";

if ($conn->query($sql) === TRUE) {
  echo "Додано типову сезонну акцію<br>";
} else {
  echo "Помилка додавання типової сезонної акції: " . $conn->error . "<br>";
}

} else {
echo "Помилка створення таблиці сезонних акцій: " . $conn->error . "<br>";
}

// Create uploads directory if it doesn't exist
if (!file_exists('../uploads')) {
mkdir('../uploads', 0777, true);
echo "Директорія для завантажень успішно створена<br>";
}

// Add default promotions
$default_promotions = [
[
  'title' => 'Вітамінний комплекс',
  'description' => 'Підвищіть свій імунітет за допомогою нашого преміум вітамінного комплексу. Ідеально підходить для сезонного захисту.',
  'discount' => 30,
  'old_price' => 899,
  'new_price' => 629,
  'image_path' => 'https://source.unsplash.com/random/300x200/?vitamins',
  'valid_until' => '2023-05-15'
],
[
  'title' => 'Набір для догляду за шкірою',
  'description' => 'Повний набір для догляду за шкірою з очищувальним засобом, тоніком та зволожуючим кремом для всіх типів шкіри.',
  'discount' => 25,
  'old_price' => 1399,
  'new_price' => 1049,
  'image_path' => 'https://source.unsplash.com/random/300x200/?skincare',
  'valid_until' => '2023-05-20'
],
[
  'title' => 'Набір знеболюючих',
  'description' => 'Швидкодіючі знеболюючі препарати від головного болю, м\'язового болю та дискомфорту в суглобах.',
  'discount' => 40,
  'old_price' => 599,
  'new_price' => 359,
  'image_path' => 'https://source.unsplash.com/random/300x200/?medicine',
  'valid_until' => '2023-05-30'
]
];

// Check if promotions table is empty
$result = $conn->query("SELECT COUNT(*) as count FROM promotions");
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
// Insert default promotions
$stmt = $conn->prepare("INSERT INTO promotions (title, description, discount, old_price, new_price, image_path, valid_until) VALUES (?, ?, ?, ?, ?, ?, ?)");

foreach ($default_promotions as $promo) {
  $stmt->bind_param("ssiddss", 
    $promo['title'], 
    $promo['description'], 
    $promo['discount'], 
    $promo['old_price'], 
    $promo['new_price'], 
    $promo['image_path'], 
    $promo['valid_until']
  );
  $stmt->execute();
}

$stmt->close();

echo "Додано типові акції<br>";
}

echo "<br>Налаштування завершено! <a href='index.php'>Перейти до входу в адмін-панель</a>";

$conn->close();
?>

