<?php
$conn = new mysqli("localhost", "root", "", "web2");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

$TenTH   = $_POST['TenTH'] ?? '';
$XuatXu  = $_POST['XuatXu'] ?? '';
$Mota    = $_POST['Mota'] ?? '';
$LogoTH  = '';

if (!empty($_FILES['LogoTH']['name'])) {
    $fileName = time() . "_" . basename($_FILES['LogoTH']['name']);
    $targetPath = "../image/admin/ThuongHieu/" . $fileName;
    move_uploaded_file($_FILES['LogoTH']['tmp_name'], $targetPath);
    $LogoTH = $targetPath;
}

// Lấy mã tiếp theo
$result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM thuonghieu");
$row = mysqli_fetch_assoc($result);
$ma = $row['total'] + 1;
$ma = str_pad($ma, 5, "0", STR_PAD_LEFT);

$stmt = $conn->prepare("INSERT INTO thuonghieu (MaTH, TenTH, LogoTH, XuatXu, Mota) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $ma, $TenTH, $LogoTH, $XuatXu, $Mota);

if ($stmt->execute()) {
    echo "<script>alert('Thêm thương hiệu thành công!'); window.location.href='../admin/admin.php?page=thuonghieu';</script>";
} else {
    echo "Lỗi: " . $stmt->error;
}
?>
