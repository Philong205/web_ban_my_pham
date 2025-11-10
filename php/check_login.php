<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../admin/index.php"); // hoặc index.php nếu trang login của bạn là index
    exit;
}
?>
