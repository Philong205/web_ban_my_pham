<?php
// Kết nối cơ sở dữ liệu
require_once('../BackEnd/ConnectionDB/DB_classes.php'); // Đảm bảo đúng đường dẫn nếu file nằm chung folder

// Kiểm tra yêu cầu hành động
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'check':  // Kiểm tra sản phẩm có đã bán hay chưa
        if (isset($_GET['maSP'])) {
            checkProductSold($_GET['maSP']);
        } else {
            echo json_encode(["success" => false, "message" => "Thiếu mã sản phẩm"]);
        }
        break;
    
    case 'update':  // Cập nhật trạng thái sản phẩm
        if (isset($_GET['maSP']) && isset($_GET['status'])) {
            updateProductStatus($_GET['maSP'], $_GET['status']);
        } else {
            echo json_encode(["success" => false, "message" => "Thiếu thông tin cập nhật"]);
        }
        break;

    case 'delete':  // Xóa sản phẩm
        if (isset($_GET['maSP'])) {
            deleteProduct($_GET['maSP']);
        } else {
            echo json_encode(["success" => false, "message" => "Thiếu mã sản phẩm"]);
        }
        break;

    default:
        echo json_encode(["success" => false, "message" => "Hành động không hợp lệ"]);
        break;
}

// Hàm kiểm tra sản phẩm đã bán chưa
// Hàm kiểm tra xem sản phẩm đã bán chưa
function checkProductSold($maSP)
{
    try {
        $cthd = new ChiTietHoaDonBUS();
        $result = $cthd->select_list("SELECT * FROM ChiTietHoaDon WHERE MaSP = '$maSP'");
        
        if (!empty($result)) {
            echo json_encode(["success" => true, "sold" => true]);
        } else {
            echo json_encode(["success" => true, "sold" => false]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Lỗi khi kiểm tra sản phẩm: " . $e->getMessage()]);
    }
}



// Hàm cập nhật trạng thái sản phẩm
function updateProductStatus($maSP, $status) {
    try {
        $sp = new SanPhamBUS();
        
        // Nếu $status là text thì convert về int
        if ($status === 'hidden' || $status === '0') {
            $status = 0;
        } elseif ($status === 'visible' || $status === '1') {
            $status = 1;
        }

        $result = $sp->updateProductStatus($maSP, (int)$status);

        if ($result) {
            echo json_encode(["success" => true, "message" => "Cập nhật trạng thái sản phẩm thành công"]);
        } else {
            echo json_encode(["success" => false, "message" => "Cập nhật trạng thái sản phẩm thất bại"]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Lỗi cập nhật trạng thái: " . $e->getMessage()]);
    }
}

// Hàm xóa sản phẩm
function deleteProduct($maSP) {
    try {
        $sp = new SanPhamBUS();
        $result = $sp->deleteProduct($maSP); // Gọi đúng hàm deleteProduct()

        if ($result) {
            echo json_encode(["success" => true, "message" => "Xóa sản phẩm thành công"]);
        } else {
            echo json_encode(["success" => false, "message" => "Xóa sản phẩm thất bại"]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Lỗi xóa sản phẩm: " . $e->getMessage()]);
    }
}
?>
