<?php
$host     = 'localhost';
$database = 'food_ordering_db';   // Tên DB đã thay đổi
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Kết nối database thất bại: ' . $conn->connect_error
    ]));
}

$conn->set_charset("utf8mb4");
?>