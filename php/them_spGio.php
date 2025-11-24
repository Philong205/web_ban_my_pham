<?php
// session_start();
// require_once(__DIR__ . '/../BackEnd/ConnectionDB/DB_driver.php');
// require_once(__DIR__ . '/../BackEnd/ConnectionDB/DB_classes.php');

// header('Content-Type: application/json');

// try {
//     // Kiểm tra đăng nhập
//     if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
//         throw new Exception('Bạn cần đăng nhập để thực hiện thao tác này');
//     }

//     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//         throw new Exception('Phương thức không hợp lệ');
//     }

//     // Lấy dữ liệu
//     $action = $_POST['action'] ?? '';
//     $productId = intval($_POST['productId'] ?? 0);
//     $quantity = max(1, intval($_POST['quantity'] ?? 1));

//     $db = new DB_driver();
//     $userId = intval($_SESSION['user']['id']);

//     // Truy vấn giỏ hàng
//     $cart = $db->get_row("SELECT MaGioHang FROM giohang WHERE MaND = $userId");
//     if (!$cart) {
//         // Tạo mới giỏ hàng
//         $db->insert('giohang', [
//             'MaND' => $userId,
//             'NgayTao' => date('Y-m-d H:i:s')
//         ]);
//         $cartId = $db->get_last_insert_id();
//     } else {
//         $cartId = intval($cart['MaGioHang']);
//     }

//     if ($action === 'update') {
//         $product = $db->get_row("SELECT GiaSP FROM sanpham WHERE MaSP = $productId");
//         if (!$product) {
//             throw new Exception('Sản phẩm không tồn tại');
//         }

//         $existing = $db->get_row("SELECT * FROM giohang_chitiet WHERE MaGioHang = $cartId AND MaSP = $productId");

//         if ($existing) {
//             $db->update('giohang_chitiet', ['SoLuong' => $quantity], "MaGioHang = $cartId AND MaSP = $productId");
//         } else {
//             $db->insert('giohang_chitiet', [
//                 'MaGioHang' => $cartId,
//                 'MaSP' => $productId,
//                 'SoLuong' => $quantity,
//                 'DonGia' => $product['GiaSP'],
//                 'NgayThem' => date('Y-m-d H:i:s')
//             ]);
//         }

//         echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
//     } else {
//         throw new Exception('Hành động không hợp lệ');
//     }

// } catch (Exception $e) {
//     echo json_encode(['success' => false, 'message' => '❌ Có lỗi khi thêm sản phẩm: ' . $e->getMessage()]);
// }
?>

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

    // ===========================
    //   LẤY HOẶC TẠO GIỎ HÀNG
    // ===========================
    $cart = $db->get_row("SELECT MaGioHang FROM giohang WHERE MaND = $userId");

    if (!$cart) {
        // Tạo giỏ hàng mới
        $db->insert("giohang", [
            "MaND" => $userId,
            "NgayTao" => date("Y-m-d H:i:s")
        ]);
        $cartId = $db->get_last_insert_id();
    } else {
        $cartId = intval($cart["MaGioHang"]);
    }

    // ===========================
    //        XỬ LÝ UPDATE
    // ===========================
    if ($action === "update") {

        // LẤY SẢN PHẨM CHUẨN THEO BẢNG SQL CỦA BẠN
        $product = $db->get_row("
            SELECT GiaSP, SoLuong 
            FROM sanpham 
            WHERE MaSP = $productId
        ");

        if (!$product) {
            throw new Exception("Sản phẩm không tồn tại");
        }

        $tonKho = intval($product["SoLuong"]);

        // KIỂM TRA TỒN KHO
        if ($quantity > $tonKho) {
            throw new Exception("Sản phẩm không đủ trong kho. Chỉ còn $tonKho sản phẩm.");
        }

        // Kiểm tra sản phẩm đã có trong giỏ hay chưa
        $existing = $db->get_row("
            SELECT SoLuong 
            FROM giohang_chitiet 
            WHERE MaGioHang = $cartId AND MaSP = $productId
        ");

        if ($existing) {
            // Cập nhật số lượng
            $db->update(
                "giohang_chitiet",
                ["SoLuong" => $quantity],
                "MaGioHang = $cartId AND MaSP = $productId"
            );
        } else {
            // Thêm sản phẩm vào giỏ
            $db->insert("giohang_chitiet", [
                "MaGioHang" => $cartId,
                "MaSP" => $productId,
                "SoLuong" => $quantity,
                "DonGia" => $product["GiaSP"],
                "NgayThem" => date("Y-m-d H:i:s")
            ]);
        }

        echo json_encode([
            "success" => true,
            "message" => "Cập nhật thành công"
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

