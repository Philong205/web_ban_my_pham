<?php

require_once('../BackEnd/ConnectionDB/DB_classes.php');

if (isset($_GET['MaSP'])) {
    $MaSP = $_GET['MaSP'];

    // Lấy thông tin sản phẩm từ database
    $sp = new SanPhamBUS();
    $sanpham = $sp->select_by_id('*', $MaSP);

    // Kiểm tra xem có dữ liệu không
    if ($sanpham) {
        // Trả về dữ liệu JSON
        echo json_encode($sanpham);
    } else {
        // Trả về thông báo lỗi nếu không tìm thấy sản phẩm
        echo json_encode(['error' => 'Sản phẩm không tồn tại.']);
    }
} else {
    echo json_encode(['error' => 'Mã sản phẩm không hợp lệ.']);
}

?>
