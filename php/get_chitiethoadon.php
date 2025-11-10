<?php
require_once('../BackEnd/ConnectionDB/DB_classes.php');

if (isset($_GET['MaHD'])) {
    $MaHD = $_GET['MaHD'];

    $cthdBUS = new ChiTietHoaDonBUS();
    $dsChiTiet = $cthdBUS->select_all();

    $ds = array_filter($dsChiTiet, function ($item) use ($MaHD) {
        return $item['MaHD'] == $MaHD;
    });

    echo json_encode(array_values($ds));
} else {
    echo json_encode(['error' => 'Thiếu mã hóa đơn']);
}
?>
