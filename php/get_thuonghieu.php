<?php

require_once('../BackEnd/ConnectionDB/DB_classes.php');

if (isset($_GET['MaTH'])) {
    $MaTH = $_GET['MaTH'];

    // Lấy thông tin sản phẩm từ database
    $th = new ThuongHieuBUS();
    $thuonghieu = $th->select_by_id('*', $MaTH);

    // Kiểm tra xem có dữ liệu không
    if ($thuonghieu) {
        // Trả về dữ liệu JSON
        echo json_encode($thuonghieu);
    } else {
        // Trả về thông báo lỗi nếu không tìm thấy sản phẩm
        echo json_encode(['error' => 'Sản phẩm không tồn tại.']);
    }
} else {
    echo json_encode(['error' => 'Mã sản phẩm không hợp lệ.']);
}

?>
