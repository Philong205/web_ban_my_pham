
<?php
require_once('../BackEnd/ConnectionDB/DB_classes.php');

// Kiểm tra sản phẩm đã được bán hay chưa
function checkProductSold($maSP) {
    if (!$maSP) {
        return json_encode(['success' => false, 'message' => 'Thiếu mã sản phẩm']);
    }

    $hoaDonBUS = new HoaDonBUS();
    $isSold = $hoaDonBUS->checkProductSold($maSP);
    return json_encode(['success' => true, 'sold' => $isSold]);
}

// Cập nhật trạng thái sản phẩm (ẩn hoặc hiện)
function updateProductStatus($maSP, $status) {
    if (!$maSP || !$status) {
        return json_encode(['success' => false, 'message' => 'Thiếu tham số']);
    }

    $sanPhamBUS = new SanPhamBUS();
    $updateStatus = $sanPhamBUS->updateProductStatus($maSP, $status);
    return json_encode(['success' => $updateStatus]);
}

// Xóa sản phẩm
function deleteProduct($maSP) {
    if (!$maSP) {
        return json_encode(['success' => false, 'message' => 'Thiếu mã sản phẩm']);
    }

    $sanPhamBUS = new SanPhamBUS();
    $deleteSuccess = $sanPhamBUS->deleteProduct($maSP);
    return json_encode(['success' => $deleteSuccess]);
}

// Xử lý yêu cầu
if (isset($_GET['action']) && isset($_GET['maSP'])) {
    $action = $_GET['action'];
    $maSP = $_GET['maSP'];

    switch ($action) {
        case 'check':
            echo checkProductSold($maSP);
            break;
        case 'update':
            $status = $_GET['status'] ?? null;
            echo updateProductStatus($maSP, $status);
            break;
        case 'delete':
            echo deleteProduct($maSP);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu yêu cầu']);
}
?>

