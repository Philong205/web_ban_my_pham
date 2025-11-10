<?php
session_start();
require_once('../BackEnd/ConnectionDB/DB_driver.php');
require_once ('../BackEnd/ConnectionDB/DB_classes.php');

if (!isset($_SESSION['user'])) {
    header("Location: ../php/login.php");
    exit();
}

$nguoiDungBUS = new NguoiDungBUS();
$hoaDonBUS = new HoaDonBUS();
$chiTietHoaDonBUS = new ChiTietHoaDonBUS();

$userId = $_SESSION['user']['id'];

// Get user info
$user = $nguoiDungBUS->select_by_id("*", $userId);

// Get purchase history
$orders = $chiTietHoaDonBUS->get_list("
    SELECT hd.MaHD, hd.NgayLap, hd.TongTien, hd.TrangThai, 
           cthd.MaSP, cthd.TenSP, cthd.SoLuong, cthd.DonGia, cthd.ThanhTien
    FROM hoadon hd
    JOIN chitiethoadon cthd ON hd.MaHD = cthd.MaHD
    WHERE hd.MaND = " . intval($userId) . "
    ORDER BY hd.NgayLap DESC
");

// Group order items by invoice
$groupedOrders = [];
foreach ($orders as $order) {
    $groupedOrders[$order['MaHD']]['info'] = [
        'date' => $order['NgayLap'],
        'total' => $order['TongTien'],
        'status' => $order['TrangThai']
    ];
    $groupedOrders[$order['MaHD']]['items'][] = [
        'product' => $order['TenSP'],
        'quantity' => $order['SoLuong'],
        'price' => $order['DonGia'],
        'subtotal' => $order['ThanhTien']
    ];
}

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $tenND = $_POST['TenND'] ?? '';
        $sdt = $_POST['SDT'] ?? '';
        $diaChi = $_POST['DiaChi'] ?? '';
        
        if (!empty($tenND) && !empty($sdt) && !empty($diaChi)) {
            $data = [
                'TenND' => $tenND,
                'SDT' => $sdt,
                'DiaChi' => $diaChi
            ];
            
            if ($nguoiDungBUS->update_by_id($data, $userId)) {
                // Cập nhật session
                $_SESSION['user']['name'] = $tenND;
                $_SESSION['user']['SDT'] = $sdt;
                $_SESSION['user']['DiaChi'] = $diaChi;
                
                echo json_encode(['success' => true, 'message' => 'Cập nhật thông tin thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Cập nhật thất bại!']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin!']);
        }
        exit();
    }
}
?>
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
    <title>Thông Tin Người Dùng| EDEN Beauty</title>
    <link rel="stylesheet" href="../css/user.css">
    <link rel="stylesheet" href="../css/index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <div class="user-container">
        <div class="back-home">
            <a href="index.php" class="back-home-btn">
                <i class="fas fa-home"></i> Quay lại trang chủ
            </a>
        </div>

        <!-- User Information -->
        <div class="user-info">
            <i class="fas fa-user-circle user-avatar"></i>
            <h2 class="user-name"><?= htmlspecialchars($user['TenND']) ?></h2>
            <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($user['SDT']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['Email']) ?></p>
            <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($user['DiaChi']) ?></p>
        </div>

         <!-- Toggle Icons -->
         <div class="toggle-icons">
            <div class="icon-section" onclick="toggleEditProfile()">
                <i class="fas fa-user-edit"></i>
                <span>Sửa thông tin</span>
            </div>
            <div class="icon-section" onclick="toggleHistory()">
                <i class="fas fa-history history-icon"></i>
                <span>Lịch sử mua hàng</span>
            </div>
            <div class="icon-section" onclick="logout()">
                <i class="fas fa-sign-out-alt"></i>
                <span>Đăng xuất</span> 
            </div>
        </div>
        <!-- Edit Profile Form -->
        <div class="edit-section" id="editProfile" style="display: none;">
            <h3>Sửa Thông Tin Cá Nhân</h3>
            <form id="profileForm">
                <div class="form-group">
                    <label for="editTenND">Họ và tên:</label>
                    <input type="text" id="editTenND" name="TenND" value="<?= htmlspecialchars($user['TenND']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="editSDT">Số điện thoại:</label>
                    <input type="text" id="editSDT" name="SDT" value="<?= htmlspecialchars($user['SDT']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="editDiaChi">Địa chỉ:</label>
                    <textarea id="editDiaChi" name="DiaChi" rows="3" required><?= htmlspecialchars($user['DiaChi']) ?></textarea>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="save-btn">Lưu thay đổi</button>
                    <button type="button" class="cancel-btn" onclick="toggleEditProfile()">Hủy</button>
                </div>
            </form>
        </div>

        <!-- Purchase History -->
        <div class="purchase-history" id="purchaseHistory" style="display: none;">
            <h3>Lịch Sử Mua Hàng</h3>
            <?php if (!empty($groupedOrders)): ?>
                <?php foreach ($groupedOrders as $orderId => $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <span><strong>Đơn hàng #<?= $orderId ?></strong></span>
                            <span><?= date('d/m/Y', strtotime($order['info']['date'])) ?></span>
                            <span><strong>Tổng tiền:</strong> <?= number_format($order['info']['total']) ?> ₫</span>
                            <span><strong>Trạng thái:</strong> <?= $order['info']['status'] ?></span>
                        </div>
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th>Sản Phẩm</th>
                                    <th>Số Lượng</th>
                                    <th>Đơn Giá</th>
                                    <th>Thành Tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order['items'] as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['product']) ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td><?= number_format($item['price']) ?> ₫</td>
                                        <td><?= number_format($item['subtotal']) ?> ₫</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Bạn chưa có đơn hàng nào.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
         function toggleEditProfile() {
            const profileSection = document.getElementById('editProfile');
            const historySection = document.getElementById('purchaseHistory');
            
            profileSection.style.display = profileSection.style.display === 'none' ? 'block' : 'none';
            historySection.style.display = 'none';
        }

        function toggleHistory() {
            const historySection = document.getElementById('purchaseHistory');
            historySection.style.display = historySection.style.display === 'none' ? 'block' : 'none';
        }

        // Xử lý form sửa thông tin
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'update_profile');
            
            fetch('user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Thành công!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Lỗi!', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Lỗi!', 'Đã xảy ra lỗi khi cập nhật thông tin', 'error');
            });
        });

        function logout() {
            Swal.fire({
                title: 'Đăng xuất',
                text: 'Bạn có chắc chắn muốn đăng xuất?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Đăng xuất',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../php/logout.php';
                }
            });
        }
    </script>
</body>
</html>