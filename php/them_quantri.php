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
$gioi_thieu = trim($_POST['gioi_thieu'] ?? '');

// === 2️⃣ Upload hình ảnh (nếu có) ===
$hinh_anh = "";
if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
    // Kiểm tra loại file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['hinh_anh']['type'], $allowed_types)) {
        echo "<script>alert('Chỉ cho phép file ảnh!'); history.back();</script>";
        exit;
    }

    $target_dir = "../image/QuanTri/"; // Sửa dấu / và thêm / cuối
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $ten_file = time() . "_" . basename($_FILES["hinh_anh"]["name"]);
    $target_file = $target_dir . $ten_file;

    if (move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_file)) {
        $hinh_anh = $target_dir. $ten_file; // Chỉ lưu tên file, đường dẫn đầy đủ có thể ghép khi hiển thị
    } else {
        echo "<script>alert('Lỗi khi tải ảnh lên!'); history.back();</script>";
        exit;
    }
}

// === 3️⃣ Mã hóa mật khẩu ===
// $mat_khau = password_hash($mat_khau, PASSWORD_DEFAULT);

// === 4️⃣ Tạo mã admin dựa trên số lượng hiện tại ===
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM quan_tri");
$row = mysqli_fetch_assoc($result);
$ma_admin = $row['total'] + 1; // VD: nếu có 8 admin, mã mới = 9

// === 5️⃣ Câu lệnh SQL thêm dữ liệu ===
$sql = "INSERT INTO quan_tri 
        (Ma_Admin, Ho_Ten, Email, Mat_Khau, Hinh_Anh, Lien_Lac, Dia_Chi, Chuc_Vu, Gioi_Thieu)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "issssssss", // 'i' cho Ma_Admin, 's' cho các trường còn lại
    $ma_admin,
    $ho_ten,
    $email,
    $mat_khau,
    $hinh_anh,
    $lien_lac,
    $dia_chi,
    $chuc_vu,
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
