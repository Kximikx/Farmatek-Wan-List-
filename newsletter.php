<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Include database connection
  require_once 'admin/db_connect.php';
  
  // Get email from form
  $email = htmlspecialchars($_POST['email']);
  
  // Validate email
  if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      // Insert into database
      $sql = "INSERT INTO subscribers (email) VALUES (?) ON DUPLICATE KEY UPDATE email = email";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("s", $email);
      
      if ($stmt->execute()) {
          // Success message
          echo "<script>
              alert('Дякуємо за підписку на нашу розсилку! Ви будете отримувати інформацію про наші останні акції та пропозиції.');
              window.location.href = 'index.html';
          </script>";
      } else {
          // Error message
          echo "<script>
              alert('На жаль, сталася помилка при обробці вашої підписки. Будь ласка, спробуйте пізніше.');
              window.location.href = 'index.html';
          </script>";
      }
      
      $stmt->close();
  } else {
      // Invalid email
      echo "<script>
          alert('Будь ласка, введіть дійсну адресу електронної пошти.');
          window.location.href = 'index.html';
      </script>";
  }
  
  $conn->close();
} else {
  // Redirect if accessed directly
  header("Location: index.html");
  exit;
}
?>

