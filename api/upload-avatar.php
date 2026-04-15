<?php
// api/upload-avatar.php
// Nhận upload avatar mới và cập nhật user

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../includes/db-connect.php';

$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Thiếu user ID']);
    exit;
}

if (empty($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Không có file ảnh hợp lệ']);
    exit;
}

$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
];
$fileType = mime_content_type($_FILES['avatar']['tmp_name']);
if (!isset($allowedTypes[$fileType])) {
    echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận ảnh JPG, PNG hoặc WEBP']);
    exit;
}

$maxSize = 2 * 1024 * 1024; // 2MB
if ($_FILES['avatar']['size'] > $maxSize) {
    echo json_encode(['success' => false, 'message' => 'Kích thước ảnh không được vượt quá 2MB']);
    exit;
}

$ext = $allowedTypes[$fileType];
$uploadDir = __DIR__ . '/../assets/uploads/avatars/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$filename = sprintf('avatar_%d_%s.%s', $user_id, time(), $ext);
$destination = $uploadDir . $filename;

if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
    echo json_encode(['success' => false, 'message' => 'Lưu file thất bại']);
    exit;
}

$avatarUrl = 'assets/uploads/avatars/' . $filename;
$update = $conn->prepare("UPDATE users SET avatar_url = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
$update->bind_param("si", $avatarUrl, $user_id);
if (!$update->execute()) {
    echo json_encode(['success' => false, 'message' => 'Không thể cập nhật avatar vào database']);
    $update->close();
    exit;
}
$update->close();
$conn->close();

echo json_encode([
    'success' => true,
    'message' => 'Upload avatar thành công',
    'avatar_url' => $avatarUrl
]);
?>