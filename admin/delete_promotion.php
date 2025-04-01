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

// Get promotion data to delete image
$sql = "SELECT image_path FROM promotions WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
$promotion = $result->fetch_assoc();

// Delete image file if it exists
if (!empty($promotion['image_path']) && file_exists('../' . $promotion['image_path'])) {
    unlink('../' . $promotion['image_path']);
}

// Delete from database
$sql = "DELETE FROM promotions WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Акцію успішно видалено!";
} else {
    $_SESSION['error_message'] = "Помилка видалення акції: " . $stmt->error;
}
}

header("Location: dashboard.php");
exit;
?>

