<?php
$conn = new mysqli("localhost","root","","web2");
if($conn->connect_error) die("Kết nối thất bại: ".$conn->connect_error);

$MaKM = $_POST['MaKM'] ?? '';
$TenKM = $_POST['TenKM'] ?? '';
$GiaTriKM = $_POST['GiaTriKM'] ?? 0;
$NgayBD = $_POST['NgayBD'] ?? '';
$NgayKT = $_POST['NgayKT'] ?? '';

$stmt = $conn->prepare("UPDATE khuyenmai SET TenKM=?, GiaTriKM=?, NgayBD=?, NgayKT=? WHERE MaKM=?");
$stmt->bind_param("sdssi",$TenKM,$GiaTriKM,$NgayBD,$NgayKT,$MaKM);
if($stmt->execute()) header("Location: ../admin/admin.php?page=khuyenmai");
else echo "❌ Lỗi cập nhật: ".$stmt->error;

$stmt->close();
$conn->close();
?>
