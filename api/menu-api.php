<?php
// Prevent any output before JSON response
ob_start();

// Disable HTML error output and enable error reporting to catch issues
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

include_once '../includes/db-connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    try {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $discount_price = !empty($_POST['discount_price']) && $_POST['discount_price'] !== '' ? (float)$_POST['discount_price'] : null;
        $category_id = (int)($_POST['category_id'] ?? 0);
        $restaurant_id = 1; // Default restaurant ID for demo

        if (empty($name)) {
            throw new Exception('Item name is required');
        }

        if ($price <= 0) {
            throw new Exception('Price must be greater than 0');
        }

        if ($category_id <= 0) {
            throw new Exception('Valid category is required');
        }

        // Handle file upload
        $image_url = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/';
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($file_extension, $allowed_extensions)) {
                throw new Exception('Only JPG, JPEG, PNG, GIF, and WEBP files are allowed');
            }

            $file_name = time() . '_' . uniqid() . '.' . $file_extension;
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_url = $file_name;
            } else {
                throw new Exception('Failed to upload image');
            }
        }

        // Handle availability toggle
        $is_available = isset($_POST['is_available']) ? (int)$_POST['is_available'] : null;

        if ($id) {
            // Update existing item
            $sql = "UPDATE menu_items SET name = ?, description = ?";
            $params = [$name, $description];
            $types = "ss";

            $sql .= ", price = ?";
            $params[] = $price;
            $types .= "d";

            if ($discount_price !== null) {
                $sql .= ", discount_price = ?";
                $params[] = $discount_price;
                $types .= "d";
            } else {
                $sql .= ", discount_price = NULL";
            }

            if ($category_id > 0) {
                $sql .= ", category_id = ?";
                $params[] = $category_id;
                $types .= "i";
            }

            if ($image_url !== null) {
                $sql .= ", image_url = ?";
                $params[] = $image_url;
                $types .= "s";
            }

            if ($is_available !== null) {
                $sql .= ", is_available = ?";
                $params[] = $is_available;
                $types .= "i";
            }

            $sql .= " WHERE id = ?";
            $params[] = $id;
            $types .= "i";

            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception('Failed to prepare UPDATE statement: ' . $conn->error . ' SQL: ' . $sql);
            }
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Item updated successfully';
            } else {
                throw new Exception('Failed to update item: ' . $stmt->error);
            }
        } else {
            // Add new item
        // For demo purposes, allow items without images
        // if (!$image_url) {
        //     throw new Exception('Image is required for new items');
        // }

            $sql = "INSERT INTO menu_items (restaurant_id, name, price, discount_price, is_available, description, image_url, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [$restaurant_id, $name, $price, $discount_price, 1, $description, $image_url, $category_id];
            $types = "isddissi";

            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception('Failed to prepare INSERT statement: ' . $conn->error . ' SQL: ' . $sql);
            }
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Item added successfully';
                $response['id'] = $conn->insert_id;
            } else {
                throw new Exception('Failed to add item: ' . $stmt->error);
            }
        }

    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    // Clean output buffer and send JSON response
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $response = ['success' => false, 'message' => ''];

    try {
        $sql = "DELETE FROM menu_items WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Item deleted successfully';
        } else {
            throw new Exception('Failed to delete item');
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    // Clean output buffer and send JSON response
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

header('HTTP/1.1 405 Method Not Allowed');
// Clean output buffer and send JSON response
ob_end_clean();
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
?>