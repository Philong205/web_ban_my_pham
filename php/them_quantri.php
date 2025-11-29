<?php
// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "web2");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// === 1️⃣ Lấy dữ liệu từ form ===
$ho_ten     = trim($_POST['ho_ten'] ?? '');
$email      = trim($_POST['email'] ?? '');
$mat_khau   = $_POST['mat_khau'] ?? '';
$lien_lac   = trim($_POST['lien_lac'] ?? '');
$dia_chi    = trim($_POST['dia_chi'] ?? '');
$chuc_vu    = trim($_POST['chuc_vu'] ?? '');
$luong      = intval($_POST['Luong'] ?? 0);
$gioi_thieu = trim($_POST['gioi_thieu'] ?? '');

// === 2️⃣ Tạo mã admin tự động dựa trên số lượng hiện tại ===
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM quan_tri");
$row = mysqli_fetch_assoc($result);
$ma_admin = $row['total'] + 1; // ví dụ: nếu có 8 admin, mã mới = 9

// === 3️⃣ Chuẩn bị câu lệnh SQL thêm dữ liệu ===
$sql = "INSERT INTO quan_tri 
        (Ma_Admin, Ho_Ten, Email, Mat_Khau, Lien_Lac, Dia_Chi, Chuc_Vu, Luong, Gioi_Thieu)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("❌ Lỗi chuẩn bị câu lệnh: " . $conn->error);
}

// Bind 9 biến: s = string, i = integer
$stmt->bind_param(
    "sssssssis", 
    $ma_admin,
    $ho_ten,
    $email,
    $mat_khau,
    $lien_lac,
    $dia_chi,
    $chuc_vu,
    $luong,
    $gioi_thieu
);

// === 4️⃣ Thực thi và phản hồi ===
if ($stmt->execute()) {
    echo "<script>
        alert('Thêm quản trị viên thành công! Mã admin: $ma_admin');
        window.location.href='../admin/admin.php?page=quantrivien';
    </script>";
} else {
    echo "❌ Lỗi khi thêm quản trị viên: " . $stmt->error;
}

// Đóng kết nối
$stmt->close();
$conn->close();
?>

