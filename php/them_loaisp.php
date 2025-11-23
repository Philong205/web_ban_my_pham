<?php
$conn = new mysqli("localhost","root","","web2");
if($conn->connect_error) die("Kết nối thất bại: ".$conn->connect_error);

$TenLoai = $_POST['TenLoai'] ?? '';
$stmt = $conn->prepare("INSERT INTO loaisanpham (TenLoai) VALUES (?)");
$stmt->bind_param("s",$TenLoai);
if($stmt->execute()) header("Location: ../admin/admin.php?page=loaisanpham");
else echo "❌ Lỗi thêm: ".$stmt->error;

$stmt->close();
$conn->close();
?>
