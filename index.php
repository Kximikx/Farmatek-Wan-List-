<?php
// Include database connection
require_once 'admin/db_connect.php';

// Отримання даних про магазин з бази даних
$store_settings = [
'store_name' => 'Farmatek',
'store_address' => 'вул. Здоров\'я 123, Медичний район',
'store_phone' => '+1 (123) 456-7890',
'store_email' => 'info@farmatek.com',
'working_hours' => 'Понеділок - П\'ятниця: 8:00 - 20:00
Субота: 9:00 - 18:00
Неділя: 10:00 - 16:00',
'logo_path' => 'default'
];

// Перевірка, чи існує таблиця налаштувань
$table_exists = $conn->query("SHOW TABLES LIKE 'settings'")->num_rows > 0;

if ($table_exists) {
// Отримання налаштувань з бази даних
$sql = "SELECT * FROM settings";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $store_settings[$row['setting_name']] = $row['setting_value'];
    }
}
}

// Отримання вітальної секції
$welcome_section = [
'title' => 'Ласкаво просимо до Farmatek',
'text1' => 'Ми піклуємося про ваше здоров\'я та пропонуємо широкий асортимент ліків, вітамінів та товарів для здоров\'я за найкращими цінами.',
'text2' => 'Наша команда професійних фармацевтів завжди готова надати вам кваліфіковану консультацію та допомогти з вибором необхідних препаратів.',
'button_text' => 'Відвідайте нас',
'image_url' => 'https://source.unsplash.com/random/600x400/?pharmacy'
];

// Перевірка, чи існує таблиця welcome_section
$table_exists = $conn->query("SHOW TABLES LIKE 'welcome_section'")->num_rows > 0;

if ($table_exists) {
// Отримання даних з бази даних
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

// Отримання поточних акцій
$promotions = [];

// Перевірка, чи існує таблиця promotions
$table_exists = $conn->query("SHOW TABLES LIKE 'promotions'")->num_rows > 0;

if ($table_exists) {
// Отримання акцій з бази даних
$sql = "SELECT * FROM promotions WHERE valid_until >= CURDATE() ORDER BY id DESC LIMIT 3";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $promotions[] = $row;
    }
}
}

// Якщо немає акцій в базі даних, використовуємо тестові дані
if (empty($promotions)) {
$promotions = [
    [
        'title' => 'Вітамінний комплекс',
        'description' => 'Підвищіть свій імунітет за допомогою нашого преміум вітамінного комплексу. Ідеально підходить для сезонного захисту.',
        'discount' => 30,
        'old_price' => 899,
        'new_price' => 629,
        'valid_until' => '2023-05-15',
        'image_path' => 'https://source.unsplash.com/random/300x200/?vitamins'
    ],
    [
        'title' => 'Набір для догляду за шкірою',
        'description' => 'Повний набір для догляду за шкірою з очищувальним засобом, тоніком та зволожуючим кремом для всіх типів шкіри.',
        'discount' => 25,
        'old_price' => 1399,
        'new_price' => 1049,
        'valid_until' => '2023-05-20',
        'image_path' => 'https://source.unsplash.com/random/300x200/?skincare'
    ],
    [
        'title' => 'Набір знеболюючих',
        'description' => 'Швидкодіючі знеболюючі препарати від головного болю, м\'язового болю та дискомфорту в суглобах.',
        'discount' => 40,
        'old_price' => 599,
        'new_price' => 359,
        'valid_until' => '2023-05-30',
        'image_path' => 'https://source.unsplash.com/random/300x200/?medicine'
    ]
];
}

// Отримання сезонної акції
$seasonal_promo = [
'title' => 'Сезонне полегшення алергії',
'subtitle' => 'Купи 2 отримай 1 БЕЗКОШТОВНО на всі протиалергічні препарати',
'description' => 'Не дозволяйте сезонній алергії сповільнювати вас. Відвідайте нашу аптеку, щоб скористатися цією спеціальною пропозицією!',
'note' => 'Пропозиція дійсна тільки в магазині. Не може поєднуватися з іншими знижками.',
'button_text' => 'Знайти наше розташування',
'background_image' => 'https://source.unsplash.com/random/1600x900/?spring'
];

// Перевірка, чи існує таблиця seasonal_promo
$table_exists = $conn->query("SHOW TABLES LIKE 'seasonal_promo'")->num_rows > 0;

if ($table_exists) {
// Отримання даних з бази даних
$sql = "SELECT * FROM seasonal_promo LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $seasonal_promo = [
        'title' => $row['title'],
        'subtitle' => $row['subtitle'],
        'description' => $row['description'],
        'note' => $row['note'],
        'button_text' => $row['button_text'],
        'background_image' => $row['background_image']
    ];
}
}

// Отримання тижневих знижок
$weekly_deals = [
'Monday' => ['title' => 'День для пенсіонерів', 'description' => '15% знижки для клієнтів старше 60 років', 'note' => 'Потрібне посвідчення для підтвердження віку'],
'Wednesday' => ['title' => 'Сімейний день', 'description' => '10% знижки на дитячі товари', 'note' => 'Поширюється на всі товари для догляду за дитиною'],
'Friday' => ['title' => 'Готові до вихідних', 'description' => 'Купи 1 отримай 50% знижки на другий вітамін', 'note' => 'Другий товар повинен бути рівної або меншої вартості'],
'Saturday' => ['title' => 'День краси', 'description' => '20% знижки на всю косметику', 'note' => 'Включає всі товари для краси та догляду за шкірою']
];

// Перевірка, чи існує таблиця weekly_deals
$table_exists = $conn->query("SHOW TABLES LIKE 'weekly_deals'")->num_rows > 0;

if ($table_exists) {
// Отримання даних з бази даних
$sql = "SELECT * FROM weekly_deals";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $weekly_deals[$row['day']] = [
            'title' => $row['title'],
            'description' => $row['description'],
            'note' => $row['note']
        ];
    }
}
}

// Отримання інформації про магазин
$store_info = [
    'section_title' => 'Відвідайте наш магазин',
    'section_subtitle' => 'Приходьте до нашої аптеки, щоб скористатися цими акціями',
    'map_embed' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3000!2d-73.9857!3d40.7484!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDQ0JzU0LjIiTiA3M8KwNTknMDguNSJX!5e0!3m2!1sen!2sus!4v1620000000000!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
    'location_title' => 'Наше розташування',
    'location_address' => 'вул. Здоров\'я 123, Медичний район<br>Місто, Країна, 12345',
    'hours_title' => 'Години роботи',
    'working_hours' => 'Понеділок - П\'ятниця: 8:00 - 20:00<br>Субота: 9:00 - 18:00<br>Неділя: 10:00 - 16:00',
    'contact_title' => 'Зв\'яжіться з нами',
    'contact_phone' => '+1 (123) 456-7890',
    'contact_email' => 'info@farmatek.com',
    'details_title' => 'Деталі акцій',
    'details_text' => 'Всі акції дійсні тільки в магазині.<br>Пропозиції не можуть поєднуватися, якщо не вказано інше.<br>Поки товар є в наявності.'
];

// Перевірка, чи існує таблиця store_info
$table_exists = $conn->query("SHOW TABLES LIKE 'store_info'")->num_rows > 0;

if ($table_exists) {
    // Отримання даних з бази даних
    $sql = "SELECT * FROM store_info LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $store_info = [
            'section_title' => $row['section_title'],
            'section_subtitle' => $row['section_subtitle'],
            'map_embed' => $row['map_embed'],
            'location_title' => $row['location_title'],
            'location_address' => $row['location_address'],
            'hours_title' => $row['hours_title'],
            'working_hours' => $row['working_hours'],
            'contact_title' => $row['contact_title'],
            'contact_phone' => $row['contact_phone'],
            'contact_email' => $row['contact_email'],
            'details_title' => $row['details_title'],
            'details_text' => $row['details_text']
        ];
    }
}

// Переклад днів тижня
$day_translations = [
'Monday' => 'Понеділок',
'Wednesday' => 'Середа',
'Friday' => 'П\'ятниця',
'Saturday' => 'Субота'
];

// Функція для відображення логотипу
function displayLogo($logo_path, $store_name) {
  if ($logo_path == 'default' || empty($logo_path)) {
      return '<i class="fas fa-mortar-pestle"></i> ' . $store_name;
  } else {
      return '<img src="' . $logo_path . '" alt="' . $store_name . '" class="custom-logo">';
  }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $store_settings['store_name']; ?> - Акції та Знижки</title>
<link rel="shortcut icon" href="img/link_logo.png" type="image/png">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  .custom-logo {
      max-height: 60px;
      max-width: 200px;
      vertical-align: middle;
  }
  
  .footer-logo .custom-logo {
      max-height: 80px;
  }
</style>
</head>
<body>
<header>
  <div class="container">
      <div class="logo">
          <h1><?php echo displayLogo($store_settings['logo_path'], $store_settings['store_name']); ?></h1>
      </div>
      <div class="header-contact">
          <div class="contact-item">
              <i class="fas fa-phone"></i>
              <span><?php echo $store_settings['store_phone']; ?></span>
          </div>
          <div class="contact-item">
              <i class  ?></span>
          </div>
          <div class="contact-item">
              <i class="fas fa-map-marker-alt"></i>
              <span><?php echo $store_settings['store_address']; ?></span>
          </div>
      </div>
  </div>
</header>

<!-- Вітальна секція з фото аптеки -->
<section class="welcome">
  <div class="container">
      <div class="welcome-content">
          <div class="welcome-text">
              <h1><?php echo $welcome_section['title']; ?></h1>
              <p><?php echo $welcome_section['text1']; ?></p>
              <p><?php echo $welcome_section['text2']; ?></p>
              <a href="#store-info" class="btn"><?php echo $welcome_section['button_text']; ?></a>
          </div>
          <div class="welcome-image">
              <img src="<?php echo $welcome_section['image_url']; ?>" alt="Аптека <?php echo $store_settings['store_name']; ?>">
          </div>
      </div>
  </div>
</section>

<section id="offers" class="featured-offers">
  <div class="container">
      <h2 class="section-title">Поточні акції</h2>
      <p class="section-subtitle">Відвідайте нашу аптеку, щоб скористатися цими спеціальними пропозиціями</p>
      <div class="offers-grid">
          <?php foreach ($promotions as $promotion): ?>
              <div class="offer-card">
                  <div class="discount-badge">-<?php echo $promotion['discount']; ?>%</div>
                  <div class="offer-image" style="background-image: url('<?php echo $promotion['image_path']; ?>');"></div>
                  <div class="offer-content">
                      <h3><?php echo $promotion['title']; ?></h3>
                      <p class="price"><span class="old-price"><?php echo $promotion['old_price']; ?> грн</span> <?php echo $promotion['new_price']; ?> грн</p>
                      <p><?php echo $promotion['description']; ?></p>
                      <p class="offer-valid">Діє до: <?php echo date('d.m.Y', strtotime($promotion['valid_until'])); ?></p>
                      <a href="#store-info" class="btn btn-sm">Деталі магазину</a>
                  </div>
              </div>
          <?php endforeach; ?>
      </div>
  </div>
</section>

<section class="seasonal-promo" style="background: linear-gradient(rgba(44, 120, 115, 0.9), rgba(44, 120, 115, 0.9)), url('<?php echo $seasonal_promo['background_image']; ?>') no-repeat center center / cover;">
  <div class="container">
      <div class="promo-content">
          <h2><?php echo $seasonal_promo['title']; ?></h2>
          <h3><?php echo $seasonal_promo['subtitle']; ?></h3>
          <p><?php echo $seasonal_promo['description']; ?></p>
          <p class="promo-details"><?php echo $seasonal_promo['note']; ?></p>
          <a href="#store-info" class="btn"><?php echo $seasonal_promo['button_text']; ?></a>
      </div>
  </div>
</section>

<section class="weekly-deals">
  <div class="container">
      <h2 class="section-title">Тижневі знижки</h2>
      <p class="section-subtitle">Спеціальні знижки доступні в різні дні тижня</p>
      <div class="deals-grid">
          <?php foreach ($weekly_deals as $day => $deal): ?>
              <div class="deal-card">
                  <div class="deal-day"><?php echo $day_translations[$day]; ?></div>
                  <h3><?php echo $deal['title']; ?></h3>
                  <p><?php echo $deal['description']; ?></p>
                  <?php if (!empty($deal['note'])): ?>
                      <p class="deal-note"><?php echo $deal['note']; ?></p>
                  <?php endif; ?>
              </div>
          <?php endforeach; ?>
      </div>
  </div>
</section>

<section id="store-info" class="store-info">
  <div class="container">
      <h2 class="section-title"><?php echo $store_info['section_title']; ?></h2>
      <p class="section-subtitle"><?php echo $store_info['section_subtitle']; ?></p>
      
      <div class="store-details">
          <div class="store-map">
              <?php echo $store_info['map_embed']; ?>
          </div>
          <div class="store-info-details">
              <div class="info-card">
                  <i class="fas fa-map-marker-alt"></i>
                  <h3><?php echo $store_info['location_title']; ?></h3>
                  <p><?php echo $store_info['location_address']; ?></p>
              </div>
              <div class="info-card">
                  <i class="fas fa-clock"></i>
                  <h3><?php echo $store_info['hours_title']; ?></h3>
                  <p><?php echo $store_info['working_hours']; ?></p>
              </div>
              <div class="info-card">
                  <i class="fas fa-phone"></i>
                  <h3><?php echo $store_info['contact_title']; ?></h3>
                  <p>Телефон: <?php echo $store_info['contact_phone']; ?><br>
                  Email: <?php echo $store_info['contact_email']; ?></p>
              </div>
              <div class="info-card">
                  <i class="fas fa-info-circle"></i>
                  <h3><?php echo $store_info['details_title']; ?></h3>
                  <p><?php echo $store_info['details_text']; ?></p>
              </div>
          </div>
      </div>
  </div>
</section>

<footer>
  <div class="container">
      <div class="footer-content">
          <div class="footer-logo">
              <h2><?php echo displayLogo($store_settings['logo_path'], $store_settings['store_name']); ?></h2>
              <p>Ваше здоров'я - наш пріоритет</p>
          </div>
          <div class="social-media">
              <a href="#"><i class="fab fa-facebook"></i></a>
              <a href="#"><i class="fab fa-instagram"></i></a>
              <a href="#"><i class="fab fa-twitter"></i></a>
              <a href="#"><i class="fab fa-youtube"></i></a>
          </div>
      </div>
      <div class="footer-bottom">
          <p>&copy; <?php echo date('Y'); ?> <?php echo $store_settings['store_name']; ?>. Всі права захищені.</p>
          <p>* Всі акції дійсні тільки в магазині. Деталі уточнюйте в магазині. Ціни та пропозиції можуть змінюватися без попередження.</p>
      </div>
  </div>
</footer>

<script src="script.js"></script>
</body>
</html>

