<?php
session_start();
require_once('../BackEnd/ConnectionDB/DB_driver.php');
require_once('../BackEnd/ConnectionDB/DB_classes.php');

if (!isset($_SESSION['user'])) {
    header("Location: ../php/login.php");
    exit();
}

$db = new DB_driver();
$userId = $_SESSION['user']['id'];

// Kiểm tra giỏ hàng
$cartItems = $db->get_list("SELECT gct.*, sp.TenSP, sp.GiaSP, sp.MaKM, km.GiaTriKM 
                          FROM giohang_chitiet gct
                          JOIN giohang gh ON gct.MaGioHang = gh.MaGioHang
                          JOIN sanpham sp ON gct.MaSP = sp.MaSP
                          LEFT JOIN khuyenmai km ON sp.MaKM = km.MaKM
                          WHERE gh.MaND = " . intval($userId));

if (empty($cartItems)) {
    header("Location: ../user/cart.php");
    exit();
}

// Xử lý địa chỉ giao hàng
if (isset($_SESSION['checkout_data']['shipping_address'])) {
    if ($_SESSION['checkout_data']['shipping_address'] === 'user_default') {
        $shippingInfo = [
            'name' => $_SESSION['user']['name'],
            'phone' => $_SESSION['user']['SDT'],
            'address' => $_SESSION['user']['DiaChi'] ?? ''
        ];
    } else {
        $shippingInfo = [
            'name' => $_SESSION['user']['name'],
            'phone' => $_SESSION['user']['SDT'],
            'address' => $_SESSION['checkout_data']['new_address'] ?? ''
        ];
        
        if (empty($shippingInfo['address'])) {
            header("Location: ../user/checkout.php?step=1&error=invalid_address");
            exit();
        }
    }
} else {
    header("Location: ../user/checkout.php?step=1");
    exit();
}

// Cập nhật thông tin người nhận từ session user
$shippingInfo['name'] = $_SESSION['user']['name'];
$shippingInfo['phone'] = $_SESSION['user']['SDT'];




// Phương thức thanh toán
$paymentMethod = $_POST['payment_method'];

// Tính tổng tiền
$total = 0;
foreach ($cartItems as $item) {
    $discountedPrice = $item['GiaTriKM'] > 0 ? $item['GiaSP'] * (1 - $item['GiaTriKM']/100) : $item['GiaSP'];
    $total += $discountedPrice * $item['SoLuong'];
}
$shippingFee = 30000;
$grandTotal = $total + $shippingFee;

// Tạo hóa đơn - SỬA LẠI PHẦN TRANSACTION
$success = false;
$orderId = 0;
$error = '';

try {
    // Bắt đầu transaction
    $db->beginTransaction();
    
    // Thêm hóa đơn
    $orderData = [
        'MaND' => $userId,
        'MaTT' => 1,
        'TrangThai' => 'Chưa xác nhận',
        'NgayLap' => date('Y-m-d H:i:s'),
        'NguoiNhan' => $shippingInfo['name'],
        'SDT' => $shippingInfo['phone'],
        'DiaChi' => $shippingInfo['address'],
        'PhuongThucTT' => $paymentMethod,
        'TongTien' => $grandTotal
    ];
    
    if (!$db->insert('hoadon', $orderData)) {
        throw new Exception("Không thể tạo hóa đơn");
    }
    
    $orderId = $db->get_last_insert_id();
    
    // Thêm chi tiết hóa đơn
    foreach ($cartItems as $item) {
        $discountedPrice = $item['GiaTriKM'] > 0 ? $item['GiaSP'] * (1 - $item['GiaTriKM']/100) : $item['GiaSP'];
        
        $detailData = [
            'MaHD' => $orderId,
            'MaSP' => $item['MaSP'],
            'TenSP' => $item['TenSP'],
            'SoLuong' => $item['SoLuong'],
            'DonGia' => $discountedPrice,
            'ThanhTien' => $discountedPrice * $item['SoLuong']
        ];
        
        if (!$db->insert('chitiethoadon', $detailData)) {
            throw new Exception("Không thể thêm chi tiết hóa đơn");
        }
    }
    
    // Xóa giỏ hàng
    $cart = $db->get_row("SELECT MaGioHang FROM giohang WHERE MaND = " . intval($userId));
    if ($cart) {
        if (!$db->remove('giohang_chitiet', "MaGioHang = " . $cart['MaGioHang'])) {
            throw new Exception("Không thể xóa giỏ hàng");
        }
    }
    
    // Commit transaction nếu mọi thứ thành công
    $db->commit();
    $success = true;
    
} catch (Exception $e) {
    $db->rollback();
    $error = $e->getMessage();
    error_log("Payment processing error: " . $error);
}

// Bật lại autocommit
mysqli_autocommit($db->get_connection(), true);

if ($success) {
    // Chuyển hướng đến trang xác nhận
    header("Location: ../php/order_confirmation.php?order_id=$orderId");
    exit();
} else {
    // Chuyển hướng trở lại trang thanh toán với thông báo lỗi
    header("Location: ../user/checkout.php?step=3&error=payment_failed&message=" . urlencode($error));
    exit();
}

