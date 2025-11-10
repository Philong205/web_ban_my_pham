<?php
session_start();
require_once('../BackEnd/ConnectionDB/DB_driver.php');
require_once('../BackEnd/ConnectionDB/DB_classes.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user'])) {
    header("Location: ../php/login.php");
    exit();
}

$db = new DB_driver();
$userId = $_SESSION['user']['id'];

// Lấy thông tin giỏ hàng
$sql = "SELECT gct.*, sp.TenSP, sp.GiaSP, sp.HinhAnh, sp.MaKM, km.GiaTriKM 
        FROM giohang_chitiet gct
        JOIN giohang gh ON gct.MaGioHang = gh.MaGioHang
        JOIN sanpham sp ON gct.MaSP = sp.MaSP
        LEFT JOIN khuyenmai km ON sp.MaKM = km.MaKM
        WHERE gh.MaND = " . intval($userId);

$cartItems = $db->get_list($sql);
$total = 0;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      href="..\image\admin\link-hinh-logo.jpg"
      rel="icon"
      type="image/x-icon"
    />
    <title>Giỏ Hàng| EDEN Beauty</title>
    <link rel="stylesheet" href="../css/checkout-cart.css" />
    <link rel="stylesheet" href="../css/index.css" />
    <link rel="stylesheet" href="../css/responsive.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
</head>
<body>
    <!-- HEADER -->
    <?php include 'header.php'; ?>
    
    <!--CART-->
    <div class="cart-container">
        <div class="breadcrumb">
            Trang chủ > <span class="current">Giỏ hàng</span>
            <hr />
        </div>
        <h1 class="cart-title">Giỏ hàng (<?= count($cartItems) ?> sản phẩm)</h1>
        <div class="cart">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Giá tiền</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Xóa</th>
                    </tr>
                </thead>
                <tbody>
    <?php foreach ($cartItems as $item): 
        $discountedPrice = $item['GiaTriKM'] > 0 ? $item['GiaSP'] * (1 - $item['GiaTriKM']/100) : $item['GiaSP'];
        $subtotal = $discountedPrice * $item['SoLuong'];
        $total += $subtotal;
    ?>
    <tr data-product-id="<?= $item['MaSP'] ?>">
    <td>
        <div class="product-info">
            <img src="<?= $item['HinhAnh'] ?>" class="cart-img" />
            <h3><?= htmlspecialchars($item['TenSP']) ?></h3>
        </div>
    </td>
    <td>
        <p class="price" data-price="<?= $discountedPrice ?>"><?= number_format($discountedPrice) ?> ₫</p>
        <?php if ($item['GiaTriKM'] > 0): ?>
        <p class="old-price"><?= number_format($item['GiaSP']) ?> ₫</p>
        <?php endif; ?>
    </td>
    <td>
        <input type="number" 
               value="<?= $item['SoLuong'] ?>" 
               min="1" 
               data-product-id="<?= $item['MaSP'] ?>"
               onchange="updateQuantity(<?= $item['MaSP'] ?>, this.value)" />
    </td>
    <td class="item-total"><?= number_format($subtotal) ?> ₫</td>
    <td>
        <button class="delete-btn" onclick="removeItem(<?= $item['MaSP'] ?>)">X</button>
    </td>
</tr>
    <?php endforeach; ?>
</tbody>
            </table>
        </div>
        <div class="summary">
            <p>Tạm tính: <span class="subtotal"><?= number_format($total) ?> ₫</span></p>
            <a class="checkout-btn" href="checkout.php">Tiến hành thanh toán</a>
        </div>
    </div>
    
    <!--FOOTER-->
    <?php include 'footer.php' ?>
    <script src="../js/checkout-cart.js"></script>
    <script>
// Hàm cập nhật số lượng sản phẩm - ĐÃ SỬA LỖI THIẾU DẤU NGOẶC
function updateQuantity(productId, quantity) {
    // Validate input
    quantity = parseInt(quantity);
    if (isNaN(quantity)) {
        alert('Số lượng không hợp lệ');
        return;
    }
    
    if (quantity < 1) {
        quantity = 1;
        document.querySelector(`input[data-product-id="${productId}"]`).value = 1;
    }

    // Hiển thị loading
    const inputElement = document.querySelector(`input[data-product-id="${productId}"]`);
    inputElement.disabled = true;

    fetch('../php/update_cart.php', {  // Đảm bảo đường dẫn đúng
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update&productId=${productId}&quantity=${quantity}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        inputElement.disabled = false;
        
        if (data.success) {
            // Cập nhật UI
            const price = parseFloat(document.querySelector(`tr[data-product-id="${productId}"] .price`).getAttribute('data-price'));
            const newSubtotal = price * quantity;
            document.querySelector(`tr[data-product-id="${productId}"] .item-total`).textContent = newSubtotal.toLocaleString() + ' ₫';
            
            // Tính lại tổng
            calculateTotals();
        } else {
            alert('Lỗi: ' + (data.message || 'Không thể cập nhật'));
            location.reload();
        }
    })
    .catch(error => {
        inputElement.disabled = false;
        console.error('Error:', error);
        alert('Lỗi kết nối: ' + error.message);
    });
} 
// Hàm tính tổng tiền
function calculateTotals() {
    let total = 0;
    document.querySelectorAll('tbody tr').forEach(row => {
        const subtotalText = row.querySelector('.item-total').textContent;
        const subtotal = parseFloat(subtotalText.replace(/[^\d]/g, ''));
        total += subtotal;
    });
    document.querySelector('.subtotal').textContent = total.toLocaleString() + ' ₫';
}
</script>
</body>
</html>