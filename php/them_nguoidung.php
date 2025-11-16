<?php
// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "web2");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy dữ liệu từ form
$tennd    = trim($_POST['tennd'] ?? '');
$email    = trim($_POST['email'] ?? '');
$taikhoan = trim($_POST['taikhoan'] ?? '');
$matkhau  = $_POST['matkhau'] ?? '';
$diachi   = trim($_POST['diachi'] ?? '');
$sdt      = trim($_POST['sdt'] ?? '');

// Mã hóa mật khẩu
// $matkhau_mahoa = password_hash($matkhau, PASSWORD_DEFAULT);

// Tạo mã người dùng dựa trên số lượng hiện tại
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM nguoidung");
$row = mysqli_fetch_assoc($result);
$MaND = $row['total'] + 1; // ví dụ: nếu có 8 người dùng, mã mới = 9

// Chèn bản ghi mới
$stmt = $conn->prepare("INSERT INTO nguoidung (MaND, TenND, Email, TaiKhoan, MatKhau, DiaChi, SDT) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssss", $MaND, $tennd, $email, $taikhoan, $matkhau, $diachi, $sdt);

if ($stmt->execute()) {
    echo "<script>alert('Thêm người dùng thành công'); window.location.href='../admin/admin.php?page=khachhang';</script>";
} else {
    echo "Thêm người dùng thất bại:  " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
