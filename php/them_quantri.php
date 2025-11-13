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

// === 2️⃣ Upload hình ảnh (nếu có) ===
$hinh_anh = "";
if (isset($_FILES['Hinh_Anh']) && $_FILES['Hinh_Anh']['error'] == 0) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['Hinh_Anh']['type'], $allowed_types)) {
        echo "<script>alert('Chỉ cho phép file ảnh!'); history.back();</script>";
        exit;
    }

    $target_dir = "../image/QuanTri/"; 
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $ten_file = time() . "_" . basename($_FILES["Hinh_Anh"]["name"]);
    $target_file = $target_dir . $ten_file;

    if (move_uploaded_file($_FILES["Hinh_Anh"]["tmp_name"], $target_file)) {
        $hinh_anh = $ten_file; // chỉ lưu tên file
    } else {
        echo "<script>alert('Lỗi khi tải ảnh lên!'); history.back();</script>";
        exit;
    }
} else {
    $hinh_anh = "default-avatar.jpg"; // mặc định nếu không upload
}

// === 3️⃣ Mã hóa mật khẩu ===
// $mat_khau_hash = password_hash($mat_khau, PASSWORD_DEFAULT);

// === 4️⃣ Tạo mã admin tự động dựa trên số lượng hiện tại ===
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM quan_tri");
$row = mysqli_fetch_assoc($result);
$ma_admin = $row['total'] + 1; // ví dụ: nếu có 8 admin, mã mới = 9
$ma_admin = str_pad($ma_admin, 5, "0", STR_PAD_LEFT); // Ví dụ: 00009

// === 5️⃣ Câu lệnh SQL thêm dữ liệu ===
$sql = "INSERT INTO quan_tri 
        (Ma_Admin, Ho_Ten, Email, Mat_Khau, Hinh_Anh, Lien_Lac, Dia_Chi, Chuc_Vu, Luong, Gioi_Thieu)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "isssssssis", 
    $ma_admin,
    $ho_ten,
    $email,
    $mat_khau_hash,
    $hinh_anh,
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
