<?php
header('Content-Type: application/json');
include_once '../includes/db-connect.php';

$ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];
$ids = array_map('intval', $ids);
$ids = array_filter($ids);

if (empty($ids)) {
    echo json_encode([]);
    exit;
}

$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "SELECT id, name, description, price, image_url FROM menu_items WHERE id IN ($placeholders) LIMIT " . count($ids);

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $conn->error]);
    exit;
}

$types = str_repeat('i', count($ids));
$stmt->bind_param($types, ...$ids);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = [
        'id' => (int)$row['id'],
        'name' => $row['name'],
        'description' => $row['description'],
        'price' => (float)$row['price'],
        'image_url' => $row['image_url']
    ];
}

$stmt->close();
echo json_encode($items);
?>
