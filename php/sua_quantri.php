<?php
$conn = new mysqli("localhost", "root", "", "web2");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

$Ma_Admin   = $_POST['Ma_Admin']   ?? '';
$Ho_Ten     = $_POST['Ho_Ten']     ?? '';
$Email      = $_POST['Email']      ?? '';
$Mat_Khau   = $_POST['Mat_Khau']   ?? '';
$Lien_Lac   = $_POST['Lien_Lac']   ?? '';
$Dia_Chi    = $_POST['Dia_Chi']    ?? '';
$Chuc_Vu    = $_POST['Chuc_Vu']    ?? '';
$Gioi_Thieu = $_POST['Gioi_Thieu'] ?? '';
$Luong      = $_POST['Luong']      ?? 0; // thêm trường Lương

// Lấy dữ liệu cũ từ database
$result = $conn->query("SELECT Mat_Khau FROM quan_tri WHERE Ma_Admin='$Ma_Admin'");
$old_password = '';

if ($result && $row = $result->fetch_assoc()) {
    $old_password = $row['Mat_Khau'];
}

// Nếu mật khẩu để trống thì giữ mật khẩu cũ
if (empty($Mat_Khau)) {
    $Mat_Khau = $old_password;
} else {
    // Nếu muốn mã hóa mật khẩu: $Mat_Khau = password_hash($Mat_Khau, PASSWORD_DEFAULT);
}


// Cập nhật database, bao gồm Lương
// $stmt = $conn->prepare("UPDATE quan_tri 
//     SET Ho_Ten=?, Email=?, Mat_Khau=?, Lien_Lac=?, Dia_Chi=?, Chuc_Vu=?, Gioi_Thieu=?, Luong=? 
//     WHERE Ma_Admin=?");
// $stmt->bind_param("ssssssssii", $Ho_Ten, $Email, $Mat_Khau, $Lien_Lac, $Dia_Chi, $Chuc_Vu, $Gioi_Thieu, $Luong, $Ma_Admin);

$stmt = $conn->prepare("UPDATE quan_tri 
    SET Ho_Ten=?, Email=?, Mat_Khau=?, Lien_Lac=?, Dia_Chi=?, Chuc_Vu=?, Gioi_Thieu=?, Luong=? 
    WHERE Ma_Admin=?");

$stmt->bind_param("sssssssis",
    $Ho_Ten,
    $Email,
    $Mat_Khau,
    $Lien_Lac,
    $Dia_Chi,
    $Chuc_Vu,
    $Gioi_Thieu,
    $Luong,
    $Ma_Admin
);

if ($stmt->execute()) {
    header("Location: ../admin/admin.php?page=quantrivien"); exit;
} else {
    echo "❌ Lỗi cập nhật: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
