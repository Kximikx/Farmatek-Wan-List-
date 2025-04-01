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
  $_SESSION['error_message'] = "No inquiry ID provided.";
  header("Location: dashboard.php");
  exit;
}

$id = (int)$_GET['id'];

// Delete from database
$sql = "DELETE FROM inquiries WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
  $_SESSION['success_message'] = "Inquiry deleted successfully!";
} else {
  $_SESSION['error_message'] = "Error deleting inquiry: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: dashboard.php");
exit;
?>

