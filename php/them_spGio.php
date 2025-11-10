<?php
session_start();
require_once(__DIR__ . '/../BackEnd/ConnectionDB/DB_driver.php');
require_once(__DIR__ . '/../BackEnd/ConnectionDB/DB_classes.php');

header('Content-Type: application/json');

try {
    // Kiểm tra đăng nhập
    if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
        throw new Exception('Bạn cần đăng nhập để thực hiện thao tác này');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Phương thức không hợp lệ');
    }

    // Lấy dữ liệu
    $action = $_POST['action'] ?? '';
    $productId = intval($_POST['productId'] ?? 0);
    $quantity = max(1, intval($_POST['quantity'] ?? 1));

    $db = new DB_driver();
    $userId = intval($_SESSION['user']['id']);

    // Truy vấn giỏ hàng
    $cart = $db->get_row("SELECT MaGioHang FROM giohang WHERE MaND = $userId");
    if (!$cart) {
        // Tạo mới giỏ hàng
        $db->insert('giohang', [
            'MaND' => $userId,
            'NgayTao' => date('Y-m-d H:i:s')
        ]);
        $cartId = $db->get_last_insert_id();
    } else {
        $cartId = intval($cart['MaGioHang']);
    }

    if ($action === 'update') {
        $product = $db->get_row("SELECT GiaSP FROM sanpham WHERE MaSP = $productId");
        if (!$product) {
            throw new Exception('Sản phẩm không tồn tại');
        }

        $existing = $db->get_row("SELECT * FROM giohang_chitiet WHERE MaGioHang = $cartId AND MaSP = $productId");

        if ($existing) {
            $db->update('giohang_chitiet', ['SoLuong' => $quantity], "MaGioHang = $cartId AND MaSP = $productId");
        } else {
            $db->insert('giohang_chitiet', [
                'MaGioHang' => $cartId,
                'MaSP' => $productId,
                'SoLuong' => $quantity,
                'DonGia' => $product['GiaSP'],
                'NgayThem' => date('Y-m-d H:i:s')
            ]);
        }

        echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
    } else {
        throw new Exception('Hành động không hợp lệ');
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '❌ Có lỗi khi thêm sản phẩm: ' . $e->getMessage()]);
}
?>
