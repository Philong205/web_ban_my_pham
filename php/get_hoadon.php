<?php
require_once('../BackEnd/ConnectionDB/DB_classes.php');

if (isset($_GET['MaHD'])) {
    $maHD = $_GET['MaHD'];
    $hoaDonBUS = new HoaDonBUS();
    $hoaDons = $hoaDonBUS->select_all();

    $hoadon = null;
    foreach ($hoaDons as $hd) {
        if ($hd['MaHD'] == $maHD) {
            $hoadon = $hd;
            break;
        }
    }

    if ($hoadon) {
        echo json_encode($hoadon);
    } else {
        echo json_encode(['error' => 'Hóa đơn không tồn tại']);
    }
} else {
    echo json_encode(['error' => 'Thiếu mã hóa đơn']);
}
?>
