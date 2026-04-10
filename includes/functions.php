<?php
// includes/functions.php

include_once 'db-connect.php';

function menuQuery($limit, $offset, $search = '') {
    global $conn;
    $search = mysqli_real_escape_string($conn, $search);
    
    $sql = "SELECT m.*, c.name as category_name 
            FROM menu_items m 
            LEFT JOIN categories c ON m.category_id = c.id 
            WHERE m.name LIKE '%$search%' 
            LIMIT $limit OFFSET $offset";
            
    $result = mysqli_query($conn, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}
function addMenuItem($name, $price, $description, $category_id, $file) {
    global $conn;
    
    // Xử lý Upload ảnh
    $image_name = time() . "_" . basename($file["name"]);
    $target_file = "../uploads/" . $image_name;
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        $sql = "INSERT INTO menu_items (name, price, description, image_url, category_id) 
                VALUES ('$name', '$price', '$description', '$image_name', '$category_id')";
        
        return mysqli_query($conn, $sql);
    }
    return false;
}
function deleteMenuItem($id) {
    global $conn;
    $id = (int)$id;
    $sql = "DELETE FROM menu_items WHERE id = $id";
    return mysqli_query($conn, $sql);
}

?>