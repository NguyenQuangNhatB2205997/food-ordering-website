<?php
// api/user-profile.php
// Quản lý user profile: lấy dữ liệu & cập nhật

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Kết nối database
require_once __DIR__ . '/../includes/db-connect.php';

// ============================================================
// GET: Lấy thông tin user
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Nhận user_id từ query parameter
    if (!isset($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'Thiếu user ID']);
        exit;
    }
    
    $user_id = intval($_GET['id']);
    
    // Truy vấn user từ database (không lấy password)
    $stmt = $conn->prepare("SELECT id, full_name, email, phone_number, date_of_birth, avatar_url, role, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy user']);
        $stmt->close();
        exit;
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();

    // Lấy danh sách địa chỉ đã lưu
    $addr_stmt = $conn->prepare("SELECT id, label, full_address, is_default, location_lat, location_long FROM saved_addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC");
    $addr_stmt->bind_param("i", $user_id);
    $addr_stmt->execute();
    $addresses = $addr_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $addr_stmt->close();

    // Tính toán thống kê
    $stats = ['orders' => 0, 'total_spent' => 0, 'reviews' => 0];
    $stats_stmt = $conn->prepare("SELECT COUNT(*) AS order_count, COALESCE(SUM(total_amount), 0) AS total_spent FROM orders WHERE user_id = ?");
    $stats_stmt->bind_param("i", $user_id);
    $stats_stmt->execute();
    $stats_result = $stats_stmt->get_result()->fetch_assoc();
    $stats_stmt->close();
    $stats['orders'] = intval($stats_result['order_count']);
    $stats['total_spent'] = floatval($stats_result['total_spent']);

    $review_check = $conn->query("SHOW TABLES LIKE 'reviews'");
    if ($review_check && $review_check->num_rows > 0) {
        $rev_stmt = $conn->prepare("SELECT COUNT(*) AS review_count FROM reviews WHERE user_id = ?");
        $rev_stmt->bind_param("i", $user_id);
        $rev_stmt->execute();
        $review_result = $rev_stmt->get_result()->fetch_assoc();
        $rev_stmt->close();
        $stats['reviews'] = intval($review_result['review_count']);
    }

    echo json_encode([
        'success' => true,
        'user' => $user,
        'addresses' => $addresses,
        'stats' => $stats
    ]);
    $conn->close();
    exit;
}

// ============================================================
// POST: Cập nhật thông tin user
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['address_action'])) {
        $user_id = intval($data['user_id'] ?? 0);
        if ($user_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Thiếu user ID']);
            exit;
        }

        $action = $data['address_action'];
        if ($action === 'add') {
            $label = trim($data['label'] ?? '');
            $full_address = trim($data['full_address'] ?? '');
            $is_default = !empty($data['is_default']) ? 1 : 0;

            if ($label === '' || $full_address === '') {
                echo json_encode(['success' => false, 'message' => 'Label và địa chỉ không được để trống']);
                exit;
            }

            if ($is_default) {
                $reset = $conn->prepare("UPDATE saved_addresses SET is_default = 0 WHERE user_id = ?");
                $reset->bind_param("i", $user_id);
                $reset->execute();
                $reset->close();
            }

            $insert = $conn->prepare("INSERT INTO saved_addresses (user_id, label, full_address, is_default, location_lat, location_long) VALUES (?, ?, ?, ?, NULL, NULL)");
            $insert->bind_param("issi", $user_id, $label, $full_address, $is_default);
            if (!$insert->execute()) {
                echo json_encode(['success' => false, 'message' => 'Không thể thêm địa chỉ']);
                $insert->close();
                exit;
            }
            $insert->close();
        } elseif ($action === 'edit') {
            $address_id = intval($data['address_id'] ?? 0);
            $label = trim($data['label'] ?? '');
            $full_address = trim($data['full_address'] ?? '');
            $is_default = !empty($data['is_default']) ? 1 : 0;

            if ($address_id <= 0 || $label === '' || $full_address === '') {
                echo json_encode(['success' => false, 'message' => 'Dữ liệu địa chỉ không hợp lệ']);
                exit;
            }

            if ($is_default) {
                $reset = $conn->prepare("UPDATE saved_addresses SET is_default = 0 WHERE user_id = ?");
                $reset->bind_param("i", $user_id);
                $reset->execute();
                $reset->close();
            }

            $update = $conn->prepare("UPDATE saved_addresses SET label = ?, full_address = ?, is_default = ? WHERE id = ? AND user_id = ?");
            $update->bind_param("ssiii", $label, $full_address, $is_default, $address_id, $user_id);
            if (!$update->execute()) {
                echo json_encode(['success' => false, 'message' => 'Không thể cập nhật địa chỉ']);
                $update->close();
                exit;
            }
            $update->close();
        } elseif ($action === 'delete') {
            $address_id = intval($data['address_id'] ?? 0);
            if ($address_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID địa chỉ không hợp lệ']);
                exit;
            }

            $delete = $conn->prepare("DELETE FROM saved_addresses WHERE id = ? AND user_id = ?");
            $delete->bind_param("ii", $address_id, $user_id);
            if (!$delete->execute()) {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa địa chỉ']);
                $delete->close();
                exit;
            }
            $delete->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
            exit;
        }

        // Trả về danh sách địa chỉ mới nhất
        $addr_stmt = $conn->prepare("SELECT id, label, full_address, is_default, location_lat, location_long FROM saved_addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC");
        $addr_stmt->bind_param("i", $user_id);
        $addr_stmt->execute();
        $addresses = $addr_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $addr_stmt->close();

        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật địa chỉ thành công',
            'addresses' => $addresses
        ]);
        $conn->close();
        exit;
    }
    
    // Kiểm tra dữ liệu bắt buộc
    if (empty($data['id']) || empty($data['full_name']) || empty($data['email'])) {
        echo json_encode(['success' => false, 'message' => 'Thiếu trường bắt buộc']);
        exit;
    }
    
    $user_id = intval($data['id']);
    $full_name = trim($data['full_name']);
    $email = trim($data['email']);
    $phone_number = isset($data['phone_number']) ? trim($data['phone_number']) : null;
    $date_of_birth = isset($data['date_of_birth']) ? trim($data['date_of_birth']) : null;
    
    // Validation
    if (strlen($full_name) < 2) {
        echo json_encode(['success' => false, 'message' => 'Tên phải ít nhất 2 ký tự']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email không hợp lệ']);
        exit;
    }
    
    // Kiểm tra email đã tồn tại chưa (ngoại trừ user hiện tại)
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email này đã được sử dụng']);
        $stmt->close();
        exit;
    }
    $stmt->close();
    
    $avatar_url = isset($data['avatar_url']) ? trim($data['avatar_url']) : '';

    // Cập nhật database
    $stmt = $conn->prepare("
        UPDATE users 
        SET full_name = ?, email = ?, phone_number = ?, date_of_birth = ?, avatar_url = IFNULL(NULLIF(?, ''), avatar_url), updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");
    $stmt->bind_param("sssssi", $full_name, $email, $phone_number, $date_of_birth, $avatar_url, $user_id);
    
    if ($stmt->execute()) {
        // Lấy dữ liệu cập nhật
        $select_stmt = $conn->prepare("SELECT id, full_name, email, phone_number, date_of_birth, avatar_url, role FROM users WHERE id = ?");
        $select_stmt->bind_param("i", $user_id);
        $select_stmt->execute();
        $updated_user = $select_stmt->get_result()->fetch_assoc();
        $select_stmt->close();
        
        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật hồ sơ thành công!',
            'user' => $updated_user
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật hồ sơ']);
    }
    
    $stmt->close();
    $conn->close();
    exit;
}

// Method không được hỗ trợ
http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
?>
