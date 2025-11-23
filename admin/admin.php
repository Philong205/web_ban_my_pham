<?php
include '..\php\check_login.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
include 'header.php';
include 'sidebar.php';

switch ($page) {
    case 'donhang':
        include 'donhang.php';
        break;

    case 'sanpham':
        include 'sanpham.php';
        break;

    case 'thuonghieu':
        include 'thuonghieu.php';
        break;

    case 'khuyenmai':
        include 'khuyenmai.php';
        break;

    case 'loaisanpham':
        include 'loaisanpham.php';
        break;

    case 'khachhang':
        include 'khachhang.php';
        break;

    case 'quantrivien':
        include 'quantrivien.php';
        break;

    case 'home':
    default:
        include 'home.php';
        break;
}

include 'footer.php';
?>
