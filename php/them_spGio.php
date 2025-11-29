
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
    $quantity = intval($_POST['quantity'] ?? 0);

    // ✅ Kiểm tra số lượng hợp lệ
    if ($quantity < 1) {
        throw new Exception("Số lượng sản phẩm phải lớn hơn hoặc bằng 1");
    }

    $db = new DB_driver();
    $userId = intval($_SESSION['user']['id']);

    // Lấy hoặc tạo giỏ hàng
    $cart = $db->get_row("SELECT MaGioHang FROM giohang WHERE MaND = $userId");
    if (!$cart) {
        $db->insert("giohang", [
            "MaND" => $userId,
            "NgayTao" => date("Y-m-d H:i:s")
        ]);
        $cartId = $db->get_last_insert_id();
    } else {
        $cartId = intval($cart["MaGioHang"]);
    }

    if ($action === "update") {
        // Lấy sản phẩm
        $product = $db->get_row("SELECT GiaSP, SoLuong FROM sanpham WHERE MaSP = $productId");
        if (!$product) {
            throw new Exception("Sản phẩm không tồn tại");
        }

        $tonKho = intval($product["SoLuong"]);

        // Kiểm tra sản phẩm đã có trong giỏ
        $existing = $db->get_row("SELECT SoLuong FROM giohang_chitiet WHERE MaGioHang = $cartId AND MaSP = $productId");
        $newQuantity = $quantity;
        if ($existing) {
            $newQuantity += intval($existing['SoLuong']); // cộng dồn số lượng
        }

        // Kiểm tra tồn kho
        if ($newQuantity > $tonKho) {
            throw new Exception("Sản phẩm không đủ trong kho. Chỉ còn $tonKho sản phẩm.");
        }

        // Thêm hoặc cập nhật giỏ hàng
        if ($existing) {
            $db->update("giohang_chitiet", ["SoLuong" => $newQuantity], "MaGioHang = $cartId AND MaSP = $productId");
        } else {
            $db->insert("giohang_chitiet", [
                "MaGioHang" => $cartId,
                "MaSP" => $productId,
                "SoLuong" => $newQuantity,
                "DonGia" => $product["GiaSP"],
                "NgayThem" => date("Y-m-d H:i:s")
            ]);
        }

        echo json_encode([
            "success" => true,
            "message" => "Sản phẩm đã được thêm vào giỏ hàng"
        ]);
        exit;
    }

    throw new Exception("Hành động không hợp lệ");

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "❌ " . $e->getMessage()
    ]);
}
?>



