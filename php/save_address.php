<?php
session_start();
require_once('../BackEnd/ConnectionDB/DB_driver.php');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$db = new DB_driver();
$userId = $_SESSION['user']['id'];
$address = $_POST['address'] ?? '';

if (empty($address)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập địa chỉ']);
    exit();
}

try {
    // Set all addresses to not default first
    $db->update('diachi', ['MacDinh' => 0], "MaND = $userId");
    
    // Insert new address with MacDinh=2
    $data = [
        'MaND' => $userId,
        'DiaChi' => $address,
        'MacDinh' => 2 // Special flag for new addresses
    ];
    
    $db->insert('diachi', $data);
    
    echo json_encode([
        'success' => true,
        'message' => 'Đã lưu địa chỉ mới'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}