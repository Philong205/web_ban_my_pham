<?php
$conn = new mysqli("localhost","root","","web2");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$TenLoai = $_POST['TenLoai'] ?? '';

// Lấy số lượng hiện tại
$result = $conn->query("SELECT COUNT(*) AS total FROM loaisanpham");
$row = $result->fetch_assoc();
$MaLoai = $row['total'] + 1;

// Nếu COUNT tạo mã đang tồn tại → dùng MAX + 1
$check = $conn->query("SELECT MaLoai FROM loaisanpham WHERE MaLoai = '$MaLoai'");
if ($check->num_rows > 0) {
    $max = $conn->query("SELECT MAX(MaLoai) AS max_id FROM loaisanpham")->fetch_assoc();
    $MaLoai = $max['max_id'] + 1;
}

$stmt = $conn->prepare("INSERT INTO loaisanpham (MaLoai, TenLoai) VALUES (?,?)");
$stmt->bind_param("is", $MaLoai, $TenLoai);

if ($stmt->execute()) {
    header("Location: ../admin/admin.php?page=loaisanpham");
    exit();
} else {
    echo "❌ Lỗi thêm: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
