<?php
require_once('../BackEnd/ConnectionDB/DB_classes.php');

if (isset($_GET['MaAdmin'])) {
    $MaAdmin = $_GET['MaAdmin'];

    // Khởi tạo đối tượng BUS cho quản trị viên
    $quanTriBUS = new QuanTriBUS(); // Giả sử bạn có lớp QuanTriBUS kế thừa DB_business
    $dsQuanTri = $quanTriBUS->select_all();

    // Lọc quản trị viên theo Ma_Admin
    $ds = array_filter($dsQuanTri, function ($item) use ($MaAdmin) {
        return $item['Ma_Admin'] == $MaAdmin;
    });

    // Lấy phần tử đầu tiên (chỉ 1 quản trị viên)
    $admin = array_values($ds)[0] ?? [];

    echo json_encode($admin);
} else {
    echo json_encode(['error' => 'Thiếu mã quản trị viên']);
}

?>
