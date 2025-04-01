<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Include database connection
  require_once 'admin/db_connect.php';
  
  // Get form data
  $name = htmlspecialchars($_POST['name']);
  $email = htmlspecialchars($_POST['email']);
  $phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : 'Не вказано';
  $inquiry = htmlspecialchars($_POST['inquiry']);
  $message = htmlspecialchars($_POST['message']);
  
  // Insert into database
  $sql = "INSERT INTO inquiries (name, email, phone, inquiry_type, message) VALUES (?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssss", $name, $email, $phone, $inquiry, $message);
  
  if ($stmt->execute()) {
      // Success message
      echo "<script>
          alert('Дякуємо за ваше звернення щодо наших акцій. Ми зв'яжемося з вами найближчим часом.');
          window.location.href = 'index.html';
      </script>";
  } else {
      // Error message
      echo "<script>
          alert('На жаль, сталася помилка при відправці вашого повідомлення. Будь ласка, спробуйте пізніше.');
          window.location.href = 'index.html';
      </script>";
  }
  
  $stmt->close();
  $conn->close();
} else {
  // Redirect if accessed directly
  header("Location: index.html");
  exit;
}
?>

