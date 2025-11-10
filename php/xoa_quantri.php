<?php
// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "web2");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Nhận dữ liệu từ GET hoặc POST (thường dùng GET khi xóa)
$Ma_Admin = $_GET['Ma_Admin'] ?? '';

if ($Ma_Admin) {
    // Tạo câu lệnh DELETE
    $stmt = $conn->prepare("DELETE FROM quan_tri WHERE Ma_Admin = ?");
    $stmt->bind_param("s", $Ma_Admin);

    if ($stmt->execute()) {
        // Xóa thành công → redirect về trang quản trị
        header("Location: ../admin/admin.php?page=quantrivien"); // chỉnh đường dẫn đúng theo cấu trúc
        exit;
    } else {
        echo "❌ Lỗi xóa người quản trị: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "⚠️ Thiếu mã người quản trị để xóa!";
}

$conn->close();
?>
