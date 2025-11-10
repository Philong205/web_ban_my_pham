<?php
session_start();
require_once(__DIR__ . '/../BackEnd/ConnectionDB/DB_driver.php');
require_once(__DIR__ . '/../BackEnd/ConnectionDB/DB_classes.php');

header('Content-Type: application/json');

// Debug - ghi log
error_log("Update cart request received: " . print_r($_POST, true));

try {
    // Kiểm tra session
    if (!isset($_SESSION['user'])) {
        throw new Exception('Bạn cần đăng nhập để thực hiện thao tác này');
    }

    // Kiểm tra phương thức
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Phương thức không hợp lệ');
    }

    // Lấy dữ liệu
    $action = $_POST['action'] ?? '';
    $productId = $_POST['productId'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;

    $db = new DB_driver();
    $userId = $_SESSION['user']['id'];

    // Lấy giỏ hàng
    $cart = $db->get_row("SELECT MaGioHang FROM giohang WHERE MaND = " . intval($userId));
    if (!$cart) {
        throw new Exception('Không tìm thấy giỏ hàng');
    }
    $cartId = $cart['MaGioHang'];

    // Xử lý hành động
    if ($action === 'update') {
        $quantity = max(1, intval($quantity)); // Đảm bảo số lượng ≥ 1
        
        // Kiểm tra sản phẩm tồn tại
        $product = $db->get_row("SELECT * FROM sanpham WHERE MaSP = " . intval($productId));
        if (!$product) {
            throw new Exception('Sản phẩm không tồn tại');
        }

        // Kiểm tra đã có trong giỏ hàng chưa
        $existing = $db->get_row("SELECT * FROM giohang_chitiet WHERE MaGioHang = $cartId AND MaSP = " . intval($productId));
        
        if ($existing) {
            // Cập nhật số lượng
            $result = $db->update('giohang_chitiet', 
                ['SoLuong' => $quantity], 
                "MaGioHang = $cartId AND MaSP = " . intval($productId)
            );
        } else {
            // Thêm mới vào giỏ hàng
            $result = $db->insert('giohang_chitiet', [
                'MaGioHang' => $cartId,
                'MaSP' => intval($productId),
                'SoLuong' => $quantity,
                'DonGia' => $product['GiaSP'],
                'NgayThem' => date('Y-m-d H:i:s')
            ]);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Cập nhật thành công'
        ]);
        
    } elseif ($action === 'remove') {
        // Xóa sản phẩm khỏi giỏ hàng
        $result = $db->delete('giohang_chitiet', "MaGioHang = $cartId AND MaSP = " . intval($productId));

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Xóa sản phẩm thành công'
            ]);
        } else {
            throw new Exception('Không thể xóa sản phẩm khỏi giỏ hàng');
        }

    } else {
        throw new Exception('Hành động không hợp lệ');
    }

} catch (Exception $e) {
    // Ghi log lỗi
    error_log("Error in update_cart.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>

