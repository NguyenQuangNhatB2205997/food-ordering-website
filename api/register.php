<?php
// api/register.php
// Nhận thông tin đăng ký từ JS (fetch POST), trả về JSON

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Đọc dữ liệu JSON từ body
$data      = json_decode(file_get_contents('php://input'), true);
$full_name = isset($data['full_name']) ? trim($data['full_name']) : '';
$email     = isset($data['email'])     ? trim($data['email'])     : '';
$phone     = isset($data['phone'])     ? trim($data['phone'])     : '';
$password  = isset($data['password'])  ? trim($data['password'])  : '';

// --- Validation phía server ---
$errors = [];

if (empty($full_name)) {
    $errors[] = 'Họ tên không được để trống.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email không đúng định dạng.';
}

// Chuẩn hóa số điện thoại VN
if (!empty($phone)) {
    $phone_clean = preg_replace('/[\s\-]/', '', $phone);
    if (preg_match('/^\+84/', $phone_clean)) {
        $phone_clean = '0' . substr($phone_clean, 3);
    } elseif (strlen($phone_clean) === 9 && preg_match('/^[35789]/', $phone_clean)) {
        $phone_clean = '0' . $phone_clean;
    }
    if (!preg_match('/^0[35789][0-9]{8}$/', $phone_clean)) {
        $errors[] = 'Số điện thoại không hợp lệ.';
    }
} else {
    $errors[] = 'Số điện thoại không được để trống.';
}

if (strlen($password) < 8) {
    $errors[] = 'Mật khẩu phải có ít nhất 8 ký tự.';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// Kết nối database
require_once __DIR__ . '/../includes/db-connect.php';

// Kiểm tra email đã tồn tại chưa
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email này đã được đăng ký. Vui lòng dùng email khác.']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Hash mật khẩu trước khi lưu (bảo mật)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user mới vào database
$stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'customer')");
$stmt->bind_param("sss", $full_name, $email, $hashed_password);

if ($stmt->execute()) {
    $new_user_id = $conn->insert_id;
    echo json_encode([
        'success' => true,
        'message' => 'Đăng ký thành công! Chào mừng bạn đến với FoodRush.',
        'user'    => [
            'id'        => $new_user_id,
            'full_name' => $full_name,
            'email'     => $email,
            'role'      => 'customer'
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Đăng ký thất bại. Vui lòng thử lại.']);
}

$stmt->close();
$conn->close();
?>