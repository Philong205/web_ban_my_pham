<?php
// session_start();
// require_once "../BackEnd/ConnectionDB/DB_classes.php"; // file kết nối database

// // Khởi tạo kết nối
// $db = new Database();
// $conn = $db->getConnection(); // $conn là mysqli hợp lệ

// // Kiểm tra admin đã đăng nhập chưa
// if (!isset($_SESSION['admin'])) {
//     echo "<script>alert('Vui lòng đăng nhập trước!'); window.location='index.php';</script>";
//     exit;
// }

// // Xử lý form submit
// if (isset($_POST['CapNhat'])) {

//     $ma = $_SESSION['admin']['Ma_Admin'];
//     $ho = trim($_POST['Ho_Ten']);
//     $email = trim($_POST['Email']);
//     $ll = trim($_POST['Lien_Lac']);
//     $dc = trim($_POST['Dia_Chi']);
//     $gt = trim($_POST['Gioi_Thieu']);

//     // === Upload hình ảnh (nếu có) ===
//     $hinh_anh = $_SESSION['admin']['Hinh_Anh']; // giữ hình cũ mặc định

//     if (isset($_FILES['Hinh_Anh']) && $_FILES['Hinh_Anh']['error'] === 0) {
//         $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

//         if (!in_array($_FILES['Hinh_Anh']['type'], $allowed_types)) {
//             echo "<script>alert('Chỉ cho phép file ảnh (JPEG, PNG, GIF)!'); history.back();</script>";
//             exit;
//         }

//         $target_dir = "../image/admin/"; 
//         if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

//         $ten_file = time() . "_" . basename($_FILES["Hinh_Anh"]["name"]);
//         $target_file = $target_dir . $ten_file;

//         if (move_uploaded_file($_FILES["Hinh_Anh"]["tmp_name"], $target_file)) {
//             $hinh_anh = $ten_file; // cập nhật tên file mới
//         } else {
//             echo "<script>alert('Lỗi khi tải ảnh lên!'); history.back();</script>";
//             exit;
//         }
//     }

//     // === Cập nhật database ===
//     $stmt = $conn->prepare("
//         UPDATE quan_tri SET 
//             Ho_Ten=?, Email=?, Lien_Lac=?, Dia_Chi=?, Gioi_Thieu=?, Hinh_Anh=?
//         WHERE Ma_Admin=?
//     ");

//     // 7 biến → 7 ký tự type
//     $stmt->bind_param("sssssss", $ho, $email, $ll, $dc, $gt, $hinh_anh, $ma);
//     $stmt->execute();
//     $stmt->close();

//     // === Cập nhật session ===
//     $_SESSION['admin']['Ho_Ten'] = $ho;
//     $_SESSION['admin']['Email'] = $email;
//     $_SESSION['admin']['Lien_Lac'] = $ll;
//     $_SESSION['admin']['Dia_Chi'] = $dc;
//     $_SESSION['admin']['Gioi_Thieu'] = $gt;
//     $_SESSION['admin']['Hinh_Anh'] = $hinh_anh;

//     echo "<script>alert('Cập nhật thành công!'); window.location='../admin/admin.php?page=home';</script>";
//     exit;
// }
?>

<?php
session_start();
require_once "../BackEnd/ConnectionDB/DB_classes.php"; // file kết nối database

// Khởi tạo kết nối
$db = new Database();
$conn = $db->getConnection(); // $conn là mysqli hợp lệ

// Kiểm tra admin đã đăng nhập chưa
if (!isset($_SESSION['admin'])) {
    echo "<script>alert('Vui lòng đăng nhập trước!'); window.location='index.php';</script>";
    exit;
}

// Xử lý form submit
if (isset($_POST['CapNhat'])) {

    $ma = $_SESSION['admin']['Ma_Admin'];
    $ho = trim($_POST['Ho_Ten']);
    $email = trim($_POST['Email']);
    $ll = trim($_POST['Lien_Lac']);
    $dc = trim($_POST['Dia_Chi']);
    $gt = trim($_POST['Gioi_Thieu']);

    // === Cập nhật database ===
    $stmt = $conn->prepare("
        UPDATE quan_tri SET 
            Ho_Ten=?, Email=?, Lien_Lac=?, Dia_Chi=?, Gioi_Thieu=?
        WHERE Ma_Admin=?
    ");

    if (!$stmt) {
        die("Lỗi chuẩn bị câu lệnh: " . $conn->error);
    }

    $stmt->bind_param("ssssss", $ho, $email, $ll, $dc, $gt, $ma);

    if (!$stmt->execute()) {
        die("Lỗi cập nhật: " . $stmt->error);
    }

    $stmt->close();

    // === Cập nhật session ===
    $_SESSION['admin']['Ho_Ten'] = $ho;
    $_SESSION['admin']['Email'] = $email;
    $_SESSION['admin']['Lien_Lac'] = $ll;
    $_SESSION['admin']['Dia_Chi'] = $dc;
    $_SESSION['admin']['Gioi_Thieu'] = $gt;

    echo "<script>alert('Cập nhật thành công!'); window.location='../admin/admin.php?page=home';</script>";
    exit;
}
?>

