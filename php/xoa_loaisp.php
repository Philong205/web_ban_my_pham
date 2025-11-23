<?php
$conn = new mysqli("localhost","root","","web2");
if($conn->connect_error) die("Kết nối thất bại: ".$conn->connect_error);

$MaLoai = $_GET['MaLoai'] ?? '';
$stmt = $conn->prepare("DELETE FROM loaisanpham WHERE MaLoai=?");
$stmt->bind_param("i",$MaLoai);
if($stmt->execute()) header("Location: ../admin/admin.php?page=loaisanpham");
else echo "❌ Lỗi xóa: ".$stmt->error;

$stmt->close();
$conn->close();
?>
