<?php
// Database configuration
$servername = "sql210.infinityfree.com";
$username = "if0_38649041";  // Change to your database username
$password = "nhGCo8Rq2AsmRh";      // Change to your database password
$dbname = "if0_38649041_farmatek";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
mysqli_set_charset($conn, 'utf8');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>



