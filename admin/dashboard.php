<?php
session_start();

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
header("Location: index.php");
exit;
}

// Handle logout
if (isset($_GET['logout'])) {
session_destroy();
header("Location: index.php");
exit;
}

// Include database connection
require_once 'db_connect.php';

// Get promotions from database
$sql = "SELECT * FROM promotions ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Farmatek Адмін - Панель керування</title>
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
            <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Панель керування</a></li>
            <li><a href="add_promotion.php"><i class="fas fa-plus-circle"></i> Додати акцію</a></li>
            <li><a href="weekly_deals.php"><i class="fas fa-calendar-week"></i> Тижневі знижки</a></li>
            <li><a href="welcome_section.php"><i class="fas fa-home"></i> Вітальна секція</a></li>
            <li><a href="seasonal_promo.php"><i class="fas fa-percentage"></i> Сезонна акція</a></li>
            <li><a href="store_info.php"><i class="fas fa-store"></i> Інформація про магазин</a></li>
            <li><a href="subscribers.php"><i class="fas fa-users"></i> Підписники</a></li>
            <li><a href="logo_settings.php"><i class="fas fa-image"></i> Логотип</a></li>
            <li><a href="?logout=1"><i class="fas fa-sign-out-alt"></i> Вийти</a></li>
        </ul>
    </nav>
</div>

<div class="admin-content">
    <header>
        <h1>Панель керування</h1>
        <div class="user-info">
            <span>Вітаємо, Адміністратор</span>
            <a href="?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Вийти</a>
        </div>
    </header>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert success"><?php echo $_SESSION['success_message']; ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert error"><?php echo $_SESSION['error_message']; ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
    <div class="dashboard-stats">
        <div class="stat-card">
            <i class="fas fa-tag"></i>
            <div class="stat-info">
                <h3>Активні акції</h3>
                <p>
                    <?php
                    $sql_active = "SELECT COUNT(*) as total FROM promotions WHERE valid_until >= CURDATE()";
                    $result_active = $conn->query($sql_active);
                    $row_active = $result_active->fetch_assoc();
                    echo $row_active['total'];
                    ?>
                </p>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-calendar-day"></i>
            <div class="stat-info">
                <h3>Тижневі знижки</h3>
                <p>4</p>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <div class="stat-info">
                <h3>Підписники розсилки</h3>
                <p>
                    <?php
                    $sql_subscribers = "SELECT COUNT(*) as total FROM subscribers";
                    $result_subscribers = $conn->query($sql_subscribers);
                    $row_subscribers = $result_subscribers->fetch_assoc();
                    echo $row_subscribers['total'];
                    ?>
                </p>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-store"></i>
            <div class="stat-info">
                <h3>Інформація про магазин</h3>
                <p><a href="store_info.php" style="color: #2c7873; text-decoration: underline;">Редагувати</a></p>
            </div>
        </div>
    </div>
    
    <div class="content-section">
        <div class="section-header">
            <h2>Поточні акції</h2>
            <a href="add_promotion.php" class="btn"><i class="fas fa-plus"></i> Додати нову</a>
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Назва</th>
                    <th>Знижка</th>
                    <th>Діє до</th>
                    <th>Статус</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $status_class = (strtotime($row['valid_until']) > time()) ? 'active' : 'expired';
                        $status_text = (strtotime($row['valid_until']) > time()) ? 'Активна' : 'Закінчилась';
                        
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['title']}</td>
                            <td>{$row['discount']}%</td>
                            <td>{$row['valid_until']}</td>
                            <td><span class='status-badge {$status_class}'>{$status_text}</span></td>
                            <td class='actions'>
                                <a href='edit_promotion.php?id={$row['id']}' class='edit-btn'><i class='fas fa-edit'></i></a>
                                <a href='delete_promotion.php?id={$row['id']}' class='delete-btn' onclick='return confirm(\"Ви впевнені, що хочете видалити цю акцію?\")'><i class='fas fa-trash'></i></a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='no-data'>Акції не знайдено. <a href='add_promotion.php'>Додайте вашу першу акцію</a></td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <div class="content-section">
        <div class="section-header">
            <h2>Останні запити</h2>
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ім'я</th>
                    <th>Email</th>
                    <th>Тип запиту</th>
                    <th>Дата</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_inquiries = "SELECT * FROM inquiries ORDER BY id DESC LIMIT 5";
                $result_inquiries = $conn->query($sql_inquiries);
                
                if ($result_inquiries && $result_inquiries->num_rows > 0) {
                    while($row = $result_inquiries->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['inquiry_type']}</td>
                            <td>{$row['created_at']}</td>
                            <td class='actions'>
                                <a href='view_inquiry.php?id={$row['id']}' class='view-btn'><i class='fas fa-eye'></i></a>
                                <a href='delete_inquiry.php?id={$row['id']}' class='delete-btn' onclick='return confirm(\"Ви впевнені, що хочете видалити цей запит?\")'><i class='fas fa-trash'></i></a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='no-data'>Запити не знайдено.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>

