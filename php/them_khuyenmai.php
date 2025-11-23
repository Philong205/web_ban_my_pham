<?php
$conn = new mysqli("localhost","root","","web2");
if($conn->connect_error) die("Kết nối thất bại: ".$conn->connect_error);

$TenKM = $_POST['TenKM'] ?? '';
$GiaTriKM = $_POST['GiaTriKM'] ?? 0;
$NgayBD = $_POST['NgayBD'] ?? '';
$NgayKT = $_POST['NgayKT'] ?? '';

$stmt = $conn->prepare("INSERT INTO khuyenmai (TenKM,GiaTriKM,NgayBD,NgayKT) VALUES (?,?,?,?)");
$stmt->bind_param("sdss",$TenKM,$GiaTriKM,$NgayBD,$NgayKT);
if($stmt->execute()) header("Location: ../admin/admin.php?page=khuyenmai");
else echo "❌ Lỗi thêm: ".$stmt->error;

$stmt->close();
$conn->close();
?>
