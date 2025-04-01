<?php
session_start();

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
  header("Location: index.php");
  exit;
}

// Include database connection
require_once 'db_connect.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
  header("Location: dashboard.php");
  exit;
}

$id = (int)$_GET['id'];

// Get inquiry data
$sql = "SELECT * FROM inquiries WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  $_SESSION['error_message'] = "Inquiry not found.";
  header("Location: dashboard.php");
  exit;
}

$inquiry = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Farmatek Admin - View Inquiry</title>
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
              <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
              <li><a href="add_promotion.php"><i class="fas fa-plus-circle"></i> Add Promotion</a></li>
              <li><a href="weekly_deals.php"><i class="fas fa-calendar-week"></i> Weekly Deals</a></li>
              <li><a href="welcome_section.php"><i class="fas fa-home"></i> Welcome Section</a></li>
              <li><a href="seasonal_promo.php"><i class="fas fa-percentage"></i> Seasonal Promo</a></li>
              <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
              <li><a href="dashboard.php?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
          </ul>
      </nav>
  </div>
  
  <div class="admin-content">
      <header>
          <h1>View Inquiry</h1>
          <div class="user-info">
              <span>Welcome, Admin</span>
              <a href="dashboard.php?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
      </header>
      
      <div class="content-section">
          <div class="section-header">
              <h2>Inquiry Details</h2>
              <div>
                  <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
                  <a href="delete_inquiry.php?id=<?php echo $inquiry['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this inquiry?')"><i class="fas fa-trash"></i> Delete</a>
              </div>
          </div>
          
          <div class="inquiry-details">
              <div class="detail-row">
                  <div class="detail-label">ID:</div>
                  <div class="detail-value"><?php echo $inquiry['id']; ?></div>
              </div>
              
              <div class="detail-row">
                  <div class="detail-label">Name:</div>
                  <div class="detail-value"><?php echo $inquiry['name']; ?></div>
              </div>
              
              <div class="detail-row">
                  <div class="detail-label">Email:</div>
                  <div class="detail-value"><?php echo $inquiry['email']; ?></div>
              </div>
              
              <div class="detail-row">
                  <div class="detail-label">Phone:</div>
                  <div class="detail-value"><?php echo $inquiry['phone'] ?: 'Not provided'; ?></div>
              </div>
              
              <div class="detail-row">
                  <div class="detail-label">Inquiry Type:</div>
                  <div class="detail-value"><?php echo $inquiry['inquiry_type']; ?></div>
              </div>
              
              <div class="detail-row">
                  <div class="detail-label">Date:</div>
                  <div class="detail-value"><?php echo date('d.m.Y H:i', strtotime($inquiry['created_at'])); ?></div>
              </div>
              
              <div class="detail-row full">
                  <div class="detail-label">Message:</div>
                  <div class="detail-value message-content"><?php echo nl2br($inquiry['message']); ?></div>
              </div>
          </div>
      </div>
  </div>
  
  <style>
      .inquiry-details {
          background-color: white;
          border-radius: 10px;
          padding: 30px;
          box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      }
      
      .detail-row {
          display: flex;
          margin-bottom: 20px;
          border-bottom: 1px solid #eee;
          padding-bottom: 15px;
      }
      
      .detail-row.full {
          flex-direction: column;
      }
      
      .detail-label {
          font-weight: bold;
          width: 150px;
          color: #2c7873;
      }
      
      .detail-row.full .detail-label {
          margin-bottom: 10px;
      }
      
      .message-content {
          background-color: #f9f9f9;
          padding: 20px;
          border-radius: 5px;
          white-space: pre-wrap;
      }
      
      .btn-danger {
          background-color: #e74c3c;
      }
      
      .btn-danger:hover {
          background-color: #c0392b;
      }
  </style>
</body>
</html>

