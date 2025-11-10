<?php
include '../php/connect.php'; // hoặc đường dẫn đúng tới file kết nối database

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "UPDATE nguoidung SET TrangThai = 0 WHERE MaND = $id";
    if (mysqli_query($conn, $sql)) {
        header("Location: ../admin/admin.php?page=khachhang");
        exit();
    } else {
        echo "Lỗi khi khóa người dùng: " . mysqli_error($conn);
    }
} else {
    echo "Thiếu ID người dùng cần khóa.";
}
?>
