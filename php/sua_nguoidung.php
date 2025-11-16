<?php
// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "web2");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Nhận dữ liệu từ POST
$MaND = $_POST['MaND'] ?? '';
$TenND = $_POST['TenND'] ?? '';
$Email = $_POST['Email'] ?? '';
$TaiKhoan = $_POST['TaiKhoan'] ?? '';
$MatKhau = $_POST['MatKhau'] ?? '';
$SDT = $_POST['SDT'] ?? '';
$DiaChi = $_POST['DiaChi'] ?? '';

// Kiểm tra dữ liệu hợp lệ
if ($MaND && $TenND && $Email && $TaiKhoan && $MatKhau && $SDT && $DiaChi) {
    // Chuẩn bị câu lệnh cập nhật
    $stmt = $conn->prepare("UPDATE nguoidung SET TenND=?, Email=?, TaiKhoan=?, MatKhau=?, SDT=?, DiaChi=? WHERE MaND=?");
    $stmt->bind_param("ssssssi", $TenND, $Email, $TaiKhoan, $MatKhau, $SDT, $DiaChi, $MaND);
    
    if ($stmt->execute()) {
        // Thành công, quay lại trang danh sách
        header("Location: ../admin/admin.php?page=khachhang");
        exit;
    } else {
        echo "Lỗi cập nhật: " . $stmt->error;
        echo "<script>alert('Cập nhật không thành công!'); window.location.href = '../user/index.php';</script>";
        header("Location: ../admin/admin.php?page=khachhang");
    }
    

    $stmt->close();
} else {
    echo "Thiếu dữ liệu hoặc dữ liệu không hợp lệ!";
}

$conn->close();
?>
