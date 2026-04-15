<?php
$host     = 'localhost';
$database = 'food_ordering_db';   // Tên DB đã thay đổi
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    // Instead of dying with HTML, throw an exception that can be caught
    throw new Exception('Database connection failed: ' . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>