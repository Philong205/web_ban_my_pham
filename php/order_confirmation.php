<?php
session_start();
require_once('../BackEnd/ConnectionDB/DB_driver.php');
require_once('../BackEnd/ConnectionDB/DB_classes.php');

if (!isset($_GET['order_id']) || !isset($_SESSION['user'])) {
    header("Location: ../user/index.php");
    exit();
}

$db = new DB_driver();
$orderId = intval($_GET['order_id']);
$userId = $_SESSION['user']['id'];

// Lấy thông tin đơn hàng
$order = $db->get_row("SELECT * FROM hoadon WHERE MaHD = $orderId AND MaND = " . intval($userId));
if (!$order) {
    header("Location: ../user/index.php");
    exit();
}

// Lấy chi tiết đơn hàng
$orderItems = $db->get_list("SELECT * FROM chitiethoadon WHERE MaHD = $orderId");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
      href="..\image\admin\link-hinh-logo.jpg"
      rel="icon"
      type="image/x-icon"
    />
    <title>Xác nhận đơn hàng | EDEN Beauty</title>
    <link rel="stylesheet" href="../css/checkout-cart.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/order-confirmation.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- HEADER -->
    <?php include '../user/header.php'; ?>

    <div class="confirmation-container">
        <div class="confirmation-box">
            <h1><i class="fas fa-check-circle"></i> Đơn hàng của bạn đã được đặt thành công!</h1>
            
            <div class="order-summary">
                <h2>Thông tin đơn hàng #<?= $orderId ?></h2>
                <p><strong>Ngày đặt hàng:</strong> <?= date('d/m/Y H:i', strtotime($order['NgayLap'])) ?></p>
                <p><strong>Người nhận:</strong> <?= htmlspecialchars($order['NguoiNhan']) ?></p>
                <p><strong>Địa chỉ giao hàng:</strong> <?= htmlspecialchars($order['DiaChi']) ?></p>
                <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['SDT']) ?></p>
                <p><strong>Phương thức thanh toán:</strong> 
                    <?= $order['PhuongThucTT'] === 'Thanh toán khi nhận hàng.' ? 'Tiền mặt khi nhận hàng' : 
                       ($order['PhuongThucTT'] === 'Chuyển khoản ngân hàng.' ? 'Chuyển khoản ngân hàng' : 'Thanh toán bằng thẻ') ?>
                </p>
                <p><strong>Tình trạng:</strong> 
                    Chờ xác nhận.
                </p>
                
                <h3>Chi tiết đơn hàng</h3>
                <table class="order-items">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderItems as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['TenSP']) ?></td>
                            <td><?= $item['SoLuong'] ?></td>
                            <td><?= number_format($item['DonGia']) ?> ₫</td>
                            <td><?= number_format($item['ThanhTien']) ?> ₫</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"><strong>Tạm tính:</strong></td>
                            <td><?= number_format($order['TongTien'] - 30000) ?> ₫</td>
                        </tr>
                        <tr>
                            <td colspan="3"><strong>Phí vận chuyển:</strong></td>
                            <td>30,000 ₫</td>
                        </tr>
                        <tr>
                            <td colspan="3"><strong>Tổng cộng:</strong></td>
                            <td><?= number_format($order['TongTien']) ?> ₫</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="confirmation-actions">
                <a href="../user/index.php" class="btn-continue">Tiếp tục mua sắm</a>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <?php include '../user/footer.php'; ?>
</body>
</html>