<?php
// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "web2");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// === 1️⃣ Lấy dữ liệu từ form ===
$ho_ten     = trim($_POST['Ho_Ten'] ?? '');
$email      = trim($_POST['Email'] ?? '');
$mat_khau   = $_POST['Mat_Khau'] ?? '';
$lien_lac   = trim($_POST['Lien_Lac'] ?? '');
$dia_chi    = trim($_POST['Dia_Chi'] ?? '');
$chuc_vu    = trim($_POST['Chuc_Vu'] ?? '');
$luong      = intval($_POST['Luong'] ?? 0);
$gioi_thieu = trim($_POST['Gioi_Thieu'] ?? '');

 

// === 3️⃣ Mã hóa mật khẩu ===
// $mat_khau_hash = password_hash($mat_khau, PASSWORD_DEFAULT);

// === 4️⃣ Tạo mã admin tự động dựa trên số lượng hiện tại ===
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM quan_tri");
$row = mysqli_fetch_assoc($result);
$ma_admin = $row['total'] + 1; // ví dụ: nếu có 8 admin, mã mới = 9
$ma_admin = str_pad($ma_admin, 5, "0", STR_PAD_LEFT); // Ví dụ: 00009

// === 5️⃣ Câu lệnh SQL thêm dữ liệu ===
$sql = "INSERT INTO quan_tri 
        (Ma_Admin, Ho_Ten, Email, Mat_Khau, Lien_Lac, Dia_Chi, Chuc_Vu, Luong, Gioi_Thieu)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "isssssssis", 
    $ma_admin,
    $ho_ten,
    $email,
    $mat_khau_hash,
    $lien_lac,
    $dia_chi,
    $chuc_vu,
    $luong,
    $gioi_thieu
);

// === 6️⃣ Thực thi và phản hồi ===
if ($stmt->execute()) {
    echo "<script>
        alert('Thêm quản trị viên thành công! Mã admin: $ma_admin');
        window.location.href='../admin/admin.php?page=quantrivien';
    </script>";
} else {
    echo "❌ Lỗi: " . $stmt->error;
}

// Đóng kết nối
$stmt->close();
$conn->close();
?>
