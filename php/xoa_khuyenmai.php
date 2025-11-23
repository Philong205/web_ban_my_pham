<?php
$conn = new mysqli("localhost","root","","web2");
if($conn->connect_error) die("Kết nối thất bại: ".$conn->connect_error);

$MaKM = $_GET['MaKM'] ?? '';
$stmt = $conn->prepare("DELETE FROM khuyenmai WHERE MaKM=?");
$stmt->bind_param("i",$MaKM);
if($stmt->execute()) header("Location: ../admin/admin.php?page=khuyenmai");
else echo "❌ Lỗi xóa: ".$stmt->error;

$stmt->close();
$conn->close();
?>
