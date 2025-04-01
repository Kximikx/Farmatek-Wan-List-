<?php
session_start();

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
  header("Location: index.php");
  exit;
}

// Include database connection
require_once 'db_connect.php';

// Get subscribers from database
$sql = "SELECT * FROM subscribers ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Farmatek Admin - Subscribers</title>
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
              <li class="active"><a href="subscribers.php"><i class="fas fa-users"></i> Subscribers</a></li>
              <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
              <li><a href="dashboard.php?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
          </ul>
      </nav>
  </div>
  
  <div class="admin-content">
      <header>
          <h1>Newsletter Subscribers</h1>
          <div class="user-info">
              <span>Welcome, Admin</span>
              <a href="dashboard.php?logout=1" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
      
      <div class="content-section">
          <div class="section-header">
              <h2>All Subscribers</h2>
              <div>
                  <button class="btn" onclick="exportSubscribers()"><i class="fas fa-download"></i> Export List</button>
              </div>
          </div>
          
          <table class="data-table">
              <thead>
                  <tr>
                      <th>ID</th>
                      <th>Email</th>
                      <th>Date Subscribed</th>
                      <th>Actions</th>
                  </tr>
              </thead>
              <tbody>
                  <?php
                  if ($result->num_rows > 0) {
                      while($row = $result->fetch_assoc()) {
                          echo "<tr>
                              <td>{$row['id']}</td>
                              <td>{$row['email']}</td>
                              <td>" . date('d.m.Y H:i', strtotime($row['created_at'])) . "</td>
                              <td class='actions'>
                                  <a href='delete_subscriber.php?id={$row['id']}' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this subscriber?\")'><i class='fas fa-trash'></i></a>
                              </td>
                          </tr>";
                      }
                  } else {
                      echo "<tr><td colspan='4' class='no-data'>No subscribers found.</td></tr>";
                  }
                  ?>
              </tbody>
          </table>
      </div>
  </div>
  
  <script>
      function exportSubscribers() {
          // Create a CSV string
          let csv = 'ID,Email,Date Subscribed\n';
          
          <?php
          if ($result->num_rows > 0) {
              // Reset result pointer
              $result->data_seek(0);
              
              while($row = $result->fetch_assoc()) {
                  echo "csv += '{$row['id']},{$row['email']}," . date('d.m.Y', strtotime($row['created_at'])) . "\\n';";
              }
          }
          ?>
          
          // Create a Blob and download link
          const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
          const url = URL.createObjectURL(blob);
          const link = document.createElement('a');
          link.setAttribute('href', url);
          link.setAttribute('download', 'subscribers.csv');
          link.style.visibility = 'hidden';
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
      }
  </script>
</body>
</html>

