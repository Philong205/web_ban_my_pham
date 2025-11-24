<?php
// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "web2");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Nhận dữ liệu từ POST
$MaHD = $_POST['MaHD'] ?? '';
$TrangThai = $_POST['updateTrangthai'] ?? '';

// Kiểm tra dữ liệu hợp lệ
if ($MaHD && $TrangThai) {
    // Cập nhật trạng thái trong bảng hoadon
    $stmt = $conn->prepare("UPDATE hoadon SET MaTT=? WHERE MaHD=?");
    $stmt->bind_param("ii", $TrangThai, $MaHD);

    if ($stmt->execute()) {
        // Thành công, quay lại trang danh sách đơn hàng
        header("Location: ../admin/admin.php?page=donhang");
        exit;
    } else {
        echo "Lỗi cập nhật: " . $stmt->error;
        echo "<script>alert('Cập nhật trạng thái không thành công!'); window.location.href = '../admin/admin.php?page=donhang';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Thiếu dữ liệu hoặc dữ liệu không hợp lệ!'); window.location.href = '../admin/admin.php?page=donhang';</script>";
}

$conn->close();
?>
