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
$result = $conn->query("SELECT Mat_Khau, Hinh_Anh FROM quan_tri WHERE Ma_Admin='$Ma_Admin'");
$old_password = '';
$old_image    = '';
if ($result && $row = $result->fetch_assoc()) {
    $old_password = $row['Mat_Khau'];
    $old_image    = $row['Hinh_Anh'];
}

// Nếu mật khẩu để trống thì giữ mật khẩu cũ
if (empty($Mat_Khau)) {
    $Mat_Khau = $old_password;
} else {
    // Nếu muốn mã hóa mật khẩu: $Mat_Khau = password_hash($Mat_Khau, PASSWORD_DEFAULT);
}

// Xử lý ảnh mới
$hinh_anh = $old_image; // mặc định giữ ảnh cũ
if (isset($_FILES['Hinh_Anh_Moi']) && $_FILES['Hinh_Anh_Moi']['error'] == 0) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['Hinh_Anh_Moi']['type'], $allowed_types)) {
        echo "<script>alert('Chỉ cho phép file ảnh!'); history.back();</script>"; exit;
    }

    $target_dir = "../image/QuanTri/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $ten_file = time() . "_" . basename($_FILES["Hinh_Anh_Moi"]["name"]);
    $target_file = $target_dir . $ten_file;

    if (move_uploaded_file($_FILES["Hinh_Anh_Moi"]["tmp_name"], $target_file)) {
        $hinh_anh = $ten_file; // chỉ lưu tên file
    } else {
        echo "<script>alert('Lỗi khi tải ảnh lên!'); history.back();</script>"; exit;
    }
}

// Cập nhật database, bao gồm Lương
$stmt = $conn->prepare("UPDATE quan_tri 
    SET Ho_Ten=?, Email=?, Mat_Khau=?, Hinh_Anh=?, Lien_Lac=?, Dia_Chi=?, Chuc_Vu=?, Gioi_Thieu=?, Luong=? 
    WHERE Ma_Admin=?");
$stmt->bind_param("ssssssssii", $Ho_Ten, $Email, $Mat_Khau, $hinh_anh, $Lien_Lac, $Dia_Chi, $Chuc_Vu, $Gioi_Thieu, $Luong, $Ma_Admin);

if ($stmt->execute()) {
    header("Location: ../admin/admin.php?page=quantrivien"); exit;
} else {
    echo "❌ Lỗi cập nhật: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
