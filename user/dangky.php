<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
      href="..\image\admin\link-hinh-logo.jpg"
      rel="icon"
      type="image/x-icon"
    />
    <title>Đăng Ký| EDEN Beauty</title>
    <link rel="stylesheet" href="../css/dangky.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="registration-form">
        <h2>ĐĂNG KÝ</h2>
        <form id="registerForm">
            <input type="text" name="TenND" placeholder="Họ và tên" required><br>
            <input type="email" name="Email" placeholder="Email" required><br>
            <input type="text" name="TaiKhoan" placeholder="Tên tài khoản" required><br>
            <input type="password" name="MatKhau" placeholder="Mật khẩu" required><br>
            <input type="text" name="SDT" placeholder="Số điện thoại" required><br>
            <input type="text" name="DiaChi" placeholder="Địa chỉ" required><br>
            <button type="submit">Đăng ký</button>
          </form>
        <button onclick="goBack()" class="back-button">← Quay về</button>
    </div>

    <script src="../js/dangky.js"></script>
</body>
</html>


<?php
require_once ('../BackEnd/ConnectionDB/DB_driver.php');
require_once ('../BackEnd/ConnectionDB/DB_classes.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $TenND = $_POST['TenND'];
    $Email = $_POST['Email'];
    $TaiKhoan = $_POST['TaiKhoan'];
    $MatKhau = $_POST['MatKhau'];
    $SDT = $_POST['SDT'];
    $DiaChi = $_POST['DiaChi'];

    // Khởi tạo các đối tượng BUS
    $nguoiDungBUS = new NguoiDungBUS();
    $phanQuyenBUS = new PhanQuyenBUS();
    $gioHangBUS = new GioHangBUS();
    
    // Lấy kết nối database
    $db = Database::getConnection();
    
    // Bắt đầu transaction
    $db->autocommit(false);

    try {
        // 1. Kiểm tra tài khoản/email đã tồn tại chưa
        $checkQuery = "SELECT * FROM nguoidung WHERE TaiKhoan = ? OR Email = ?";
        $stmt = $db->prepare($checkQuery);
        $stmt->bind_param("ss", $TaiKhoan, $Email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception("Tài khoản hoặc email đã tồn tại");
        }

        // 2. Tạo mã người dùng mới
        $maxIdResult = $db->query("SELECT MAX(MaND) AS max_id FROM nguoidung");
        $row = $maxIdResult->fetch_assoc();
        $newMaND = ($row['max_id'] ?? 0) + 1;

        // 3. Thêm người dùng mới
        $newUser = [
            'MaND' => $newMaND,
            'TenND' => $TenND,
            'Email' => $Email,
            'TaiKhoan' => $TaiKhoan,
            'MatKhau' => $MatKhau,
            'SDT' => $SDT,
            'DiaChi' => $DiaChi
        ];

        // Sử dụng câu lệnh SQL trực tiếp để đảm bảo chắc chắn
        $insertUser = $db->prepare("INSERT INTO nguoidung (MaND, TenND, Email, TaiKhoan, MatKhau, SDT, DiaChi) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertUser->bind_param("issssss", 
            $newMaND, $TenND, $Email, $TaiKhoan, $MatKhau, $SDT, $DiaChi);
        
        if (!$insertUser->execute()) {
            throw new Exception("Không thể thêm người dùng: " . $db->error);
        }

        // 4. Thêm phân quyền (quyền khách hàng = 1)
        $insertRole = $db->prepare("INSERT INTO phanquyen (MaND, MaQuyen) VALUES (?, 1)");
        $insertRole->bind_param("i", $newMaND);
        
        if (!$insertRole->execute()) {
            throw new Exception("Không thể thêm phân quyền: " . $db->error);
        }

        // 5. Tạo giỏ hàng mới
        $currentDate = date('Y-m-d H:i:s');
        $insertCart = $db->prepare("INSERT INTO giohang (MaND, NgayTao, NgayCapNhat) 
                                   VALUES (?, ?, ?)");
        $insertCart->bind_param("iss", $newMaND, $currentDate, $currentDate);
        
        if (!$insertCart->execute()) {
            throw new Exception("Không thể tạo giỏ hàng: " . $db->error);
        }

        // Commit transaction nếu mọi thứ thành công
        $db->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'Đăng ký thành công!',
            'maND' => $newMaND
        ]);
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $db->rollback();
        
        echo json_encode([
            'success' => false, 
            'message' => 'Đăng ký thất bại: ' . $e->getMessage()
        ]);
    } finally {
        // Đảm bảo luôn bật lại autocommit
        $db->autocommit(true);
    }
}
?>