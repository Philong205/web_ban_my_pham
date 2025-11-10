<?php
session_start(); // Bắt đầu session

// Xóa toàn bộ session
session_unset();    // Xóa tất cả biến session
session_destroy();  // Hủy session hoàn toàn

// Chuyển hướng về trang đăng nhập
header("Location: ..\admin");
exit;
?>
