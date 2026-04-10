<?php
// db-connect.php
$host = 'localhost';
$database = 'food_delivery_db';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Thiết lập font chữ tiếng Việt
mysqli_set_charset($conn, "utf8mb4");
?>