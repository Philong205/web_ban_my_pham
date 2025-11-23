<?php
$conn = new mysqli("localhost","root","","web2");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

$MaTH   = $_POST['MaTH'];
$TenTH  = $_POST['TenTH'];
$XuatXu = $_POST['XuatXu'];
$Mota   = $_POST['Mota'];

$LogoTH = $_POST['Logo_Cu']; // logo cũ

if (!empty($_FILES['LogoTH']['name'])) {
    $fileName = time()."_".basename($_FILES['LogoTH']['name']);
    $target = "../image/admin/ThuongHieu/".$fileName;
    move_uploaded_file($_FILES['LogoTH']['tmp_name'], $target);
    $LogoTH = $target;
}

$stmt = $conn->prepare("UPDATE thuonghieu SET TenTH=?, LogoTH=?, XuatXu=?, Mota=? WHERE MaTH=?");
$stmt->bind_param("ssssi", $TenTH, $LogoTH, $XuatXu, $Mota, $MaTH);

if ($stmt->execute()) {
    header("Location: ../admin/admin.php?page=thuonghieu");
} else {
    echo "Lỗi: " . $stmt->error;
}
?>
