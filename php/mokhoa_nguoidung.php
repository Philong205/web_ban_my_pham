<?php
include '../php/connect.php'; // Đường dẫn đến file kết nối CSDL

$id = $_GET['id'] ?? 0;

$sql = "UPDATE nguoidung SET TrangThai = 1 WHERE MaND = $id";
mysqli_query($conn, $sql);

header("Location: ../admin/admin.php?page=khachhang");
exit();
?>
