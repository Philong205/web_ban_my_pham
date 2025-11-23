<?php
$conn = new mysqli("localhost","root","","web2");
if($conn->connect_error) die("Kết nối thất bại: ".$conn->connect_error);

$MaLoai = $_POST['MaLoai'] ?? '';
$TenLoai = $_POST['TenLoai'] ?? '';
$stmt = $conn->prepare("UPDATE loaisanpham SET TenLoai=? WHERE MaLoai=?");
$stmt->bind_param("si",$TenLoai,$MaLoai);
if($stmt->execute()) header("Location: ../admin/admin.php?page=loaisanpham");
else echo "❌ Lỗi cập nhật: ".$stmt->error;

$stmt->close();
$conn->close();
?>
