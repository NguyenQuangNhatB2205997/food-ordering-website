<?php
// api/login.php
// Nhận email + password từ JS (fetch POST), trả về JSON

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Chỉ chấp nhận POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Đọc dữ liệu JSON từ body
$data = json_decode(file_get_contents('php://input'), true);
$email    = isset($data['email'])    ? trim($data['email'])    : '';
$password = isset($data['password']) ? trim($data['password']) : '';

// Validation cơ bản phía server
if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email và mật khẩu không được để trống.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Định dạng email không hợp lệ.']);
    exit;
}

// Kết nối database
// Lưu ý: đường dẫn tương đối từ thư mục api/ lên includes/
require_once __DIR__ . '/../includes/db-connect.php';

// Truy vấn user theo email (dùng prepared statement để tránh SQL Injection)
$stmt = $conn->prepare("SELECT id, full_name, email, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Không tìm thấy email
    echo json_encode(['success' => false, 'message' => 'Email hoặc mật khẩu không đúng.']);
    $stmt->close();
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

// Kiểm tra password
// Lưu ý: sample-data dùng 'hashed_password_1' (chưa hash thật).
// Trong thực tế dùng password_verify(). Ở đây xử lý cả 2 trường hợp:
$passwordOk = false;

if (password_verify($password, $user['password'])) {
    // Mật khẩu đã được hash bằng password_hash() — trường hợp đăng ký thật
    $passwordOk = true;
} elseif ($password === $user['password']) {
    // Mật khẩu plain text — trường hợp sample data chưa hash
    $passwordOk = true;
}

if (!$passwordOk) {
    echo json_encode(['success' => false, 'message' => 'Email hoặc mật khẩu không đúng.']);
    exit;
}

// thêm dòng kiểm tra role admin
$is_admin = ($user['role'] === 'admin');

// Đăng nhập thành công — trả về thông tin user (KHÔNG trả password)
echo json_encode([
    'success'   => true,
    'message'   => 'Đăng nhập thành công!',
    'user'      => [
        'id'        => $user['id'],
        'full_name' => $user['full_name'],
        'email'     => $user['email'],
        'role'      => $user['role'],
        'is_admin'  => $is_admin
    ]
]);

$conn->close();
?>