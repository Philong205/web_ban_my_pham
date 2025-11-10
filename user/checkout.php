<?php
session_start();
require_once('../BackEnd/ConnectionDB/DB_driver.php');
require_once('../BackEnd/ConnectionDB/DB_classes.php');

if (!isset($_SESSION['user'])) {
    header("Location: ../php/login.php");
    exit();
}

// Lưu thông tin từ POST vào session nếu có
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['checkout_data'] = [
        'shipping_address' => $_POST['shipping_address'] ?? 'user_default',
        'new_address' => $_POST['new_address'] ?? '',
        'payment_method' => $_POST['payment_method'] ?? 'Thanh toán khi nhận hàng.'
    ];
}

// Lấy dữ liệu từ session nếu có
$checkoutData = $_SESSION['checkout_data'] ?? [
    'shipping_address' => 'user_default',
    'new_address' => '',
    'payment_method' => 'Thanh toán khi nhận hàng.'
];

$db = new DB_driver();
$userId = $_SESSION['user']['id'];

// Lấy thông tin giỏ hàng
$cartItems = $db->get_list("SELECT gct.*, sp.TenSP, sp.GiaSP, sp.HinhAnh, sp.MaKM, km.GiaTriKM 
                          FROM giohang_chitiet gct
                          JOIN giohang gh ON gct.MaGioHang = gh.MaGioHang
                          JOIN sanpham sp ON gct.MaSP = sp.MaSP
                          LEFT JOIN khuyenmai km ON sp.MaKM = km.MaKM
                          WHERE gh.MaND = " . intval($userId));

if (empty($cartItems)) {
    header("Location: cart.php");
    exit();
}

// Lấy địa chỉ đã lưu
$savedAddresses = $db->get_list("SELECT d.*, n.SDT AS default_SDT 
                                FROM diachi d
                                JOIN nguoidung n ON d.MaND = n.MaND
                                WHERE d.MaND = " . intval($userId));

// Debug: Kiểm tra dữ liệu địa chỉ
error_log("Saved addresses: " . print_r($savedAddresses, true));


// Tính tổng tiền
$total = 0;
foreach ($cartItems as $item) {
    $discountedPrice = $item['GiaTriKM'] > 0 ? $item['GiaSP'] * (1 - $item['GiaTriKM']/100) : $item['GiaSP'];
    $total += $discountedPrice * $item['SoLuong'];
}
$shippingFee = 30000;
$grandTotal = $total + $shippingFee;

// Xác định bước hiện tại
$step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$maxStep = 3;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán - EDEN Beauty</title>
    <link rel="stylesheet" href="../css/checkout-cart.css">
    <link rel="stylesheet" href="../css/checkout-steps.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- HEADER -->
    <?php include 'header.php'; ?>

    <!-- Thanh tiến trình -->
    <div class="checkout-progress">
        <div class="step <?= $step >= 1 ? 'active' : '' ?>">
            <div class="step-number">1</div>
            <div class="step-title">Địa chỉ giao hàng</div>
        </div>
        <div class="step <?= $step >= 2 ? 'active' : '' ?>">
            <div class="step-number">2</div>
            <div class="step-title">Phương thức thanh toán</div>
        </div>
        <div class="step <?= $step >= 3 ? 'active' : '' ?>">
            <div class="step-number">3</div>
            <div class="step-title">Xác nhận đơn hàng</div>
        </div>
    </div>

    <!-- Nội dung từng bước -->
    <div class="checkout-container">
        <form id="checkout-form" method="POST" action="../php/xuly_payment.php">
            <!-- Bước 1: Địa chỉ giao hàng -->
<div class="checkout-step" id="step-1" style="display: <?= $step == 1 ? 'block' : 'none' ?>;">
    <h2><i class="fas fa-map-marker-alt"></i> Địa chỉ giao hàng</h2>
    <div class="address-options">
        <div class="address-card">
            <label>
                <input type="radio" name="shipping_address" value="user_default" 
                    <?= $checkoutData['shipping_address'] === 'user_default' ? 'checked' : '' ?>>
                <div class="address-details">
                    <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong><br>
                    <?= htmlspecialchars($_SESSION['user']['DiaChi'] ?? 'Chưa có địa chỉ') ?><br>
                    ĐT: <?= htmlspecialchars($_SESSION['user']['SDT']) ?>
                    <span class="default-badge">(Địa chỉ mặc định)</span>
                </div>
            </label>
        </div>
        
        <div class="address-card">
            <label>
                <input type="radio" name="shipping_address" value="new_address"
                    <?= $checkoutData['shipping_address'] === 'new_address' ? 'checked' : '' ?>>
                <div class="address-details">
                    <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong><br>
                    <textarea name="new_address" placeholder="Nhập địa chỉ mới"><?= 
                        htmlspecialchars($checkoutData['new_address']) 
                    ?></textarea><br>
                    ĐT: <?= htmlspecialchars($_SESSION['user']['SDT']) ?>
                </div>
            </label>
        </div>
    </div>
    
    <div class="step-actions">
        <a href="cart.php" class="btn-back"><i class="fas fa-arrow-left"></i> Quay lại giỏ hàng</a>
        <button type="button" class="btn-next" onclick="nextStep(1)">Tiếp tục <i class="fas fa-arrow-right"></i></button>
    </div>
</div>

            <!-- Bước 2: Phương thức thanh toán -->
<div class="checkout-step" id="step-2" style="display: <?= $step == 2 ? 'block' : 'none' ?>;">
    <h2><i class="fas fa-credit-card"></i> Phương thức thanh toán</h2>
    
    <div class="payment-methods">
        <div class="payment-card">
            <label>
                <input type="radio" name="payment_method" value="Thanh toán khi nhận hàng." 
                    <?= $checkoutData['payment_method'] === 'Thanh toán khi nhận hàng.' ? 'checked' : '' ?>>
                <div class="payment-details">
                    <i class="fas fa-money-bill-wave"></i>
                    <h3>Thanh toán khi nhận hàng (COD)</h3>
                    <p>Bạn sẽ thanh toán bằng tiền mặt khi nhận được hàng</p>
                </div>
            </label>
        </div>

        <div class="payment-card">
            <label>
                <input type="radio" name="payment_method" value="Chuyển khoản ngân hàng."
                    <?= $checkoutData['payment_method'] === 'Chuyển khoản ngân hàng.' ? 'checked' : '' ?>>
                <div class="payment-details">
                    <i class="fas fa-university"></i>
                    <h3>Chuyển khoản ngân hàng</h3>
                    <p>Chuyển khoản trước qua ngân hàng</p>
                    <div class="bank-details" style="display: none">
                    <img src="../img/PhongBank.jpg" alt="Thông tin ngân hàng" style="max-width: 200px; height: auto; display: block; margin: 0 auto;">
                        <p>Vui lòng chuyển khoản theo thông tin trên và ghi nội dung: Mã đơn hàng + Số điện thoại</p>
                    </div>
                    </div>
                </div>
            </label>
        </div>

        <div class="payment-card">
            <label>
                <input type="radio" name="payment_method" value="Thanh toán qua thẻ."
                    <?= $checkoutData['payment_method'] === 'Thanh toán qua thẻ.' ? 'checked' : '' ?>>
                <div class="payment-details">
                    <i class="fas fa-credit-card"></i>
                    <h3>Thẻ tín dụng/ghi nợ</h3>
                    <p>Thanh toán ngay bằng thẻ Visa, MasterCard</p>
                    <div class="card-payment-form" style="display: none; margin-top: 15px;">
                        <div class="form-group">
                            <label>Số thẻ</label>
                            <input type="text" placeholder="1234 5678 9012 3456" class="card-input" maxlength="19">
                        </div>
                        <div class="form-group">
                            <label>Họ tên chủ thẻ</label>
                            <input type="text" placeholder="NGUYEN VAN A" class="card-input">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Ngày hết hạn</label>
                                <input type="text" placeholder="MM/YY" class="card-input" maxlength="5">
                            </div>
                            <div class="form-group">
                                <label>CVV</label>
                                <input type="text" placeholder="123" class="card-input" maxlength="3">
                            </div>
                        </div>
                    </div>
                </div>
            </label>
        </div>
    </div>

    <div class="step-actions">
        <button type="button" class="btn-back" onclick="prevStep(2)"><i class="fas fa-arrow-left"></i> Quay lại</button>
        <button type="button" class="btn-next" onclick="nextStep(2)">Tiếp tục <i class="fas fa-arrow-right"></i></button>
    </div>
</div>

            <!-- Bước 3: Xác nhận đơn hàng -->
<div class="checkout-step" id="step-3" style="display: <?= $step == 3 ? 'block' : 'none' ?>;">
    <h2><i class="fas fa-clipboard-check"></i> Xác nhận đơn hàng</h2>
    
    <div class="order-summary">
        <div class="shipping-info">
            <h3>Thông tin giao hàng</h3>
            <div id="shipping-info-display">
                <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong><br>
                <?php 
                if ($checkoutData['shipping_address'] === 'user_default') {
                    echo htmlspecialchars($_SESSION['user']['DiaChi'] ?? 'Chưa có địa chỉ');
                } else {
                    echo htmlspecialchars($checkoutData['new_address']);
                }
                ?><br>
                ĐT: <?= htmlspecialchars($_SESSION['user']['SDT']) ?>
            </div>
        </div>

<div class="payment-info">
    <h3>Phương thức thanh toán</h3>
    <div id="payment-info-display">
        <?php 
        if (isset($_POST['payment_method'])) {
            switch ($_POST['payment_method']) {
                case 'Thanh toán khi nhận hàng.': 
                    echo '<i class="fas fa-money-bill-wave"></i> Thanh toán khi nhận hàng'; 
                    break;
                case 'Chuyển khoản ngân hàng.': 
                    echo '<i class="fas fa-university"></i> Chuyển khoản ngân hàng'; 
                    break;
                case 'Thanh toán qua thẻ.': 
                    echo '<i class="fas fa-credit-card"></i> Thanh toán bằng thẻ'; 
                    break;
            }
        }
        ?>
    </div>
</div>

                    <div class="order-items">
                        <h3>Chi tiết đơn hàng</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $item): 
                                    $discountedPrice = $item['GiaTriKM'] > 0 ? $item['GiaSP'] * (1 - $item['GiaTriKM']/100) : $item['GiaSP'];
                                ?>
                                <tr>
                                    <td>
                                        <img src="<?= $item['HinhAnh'] ?>" alt="<?= htmlspecialchars($item['TenSP']) ?>">
                                        <?= htmlspecialchars($item['TenSP']) ?>
                                    </td>
                                    <td><?= $item['SoLuong'] ?></td>
                                    <td><?= number_format($discountedPrice) ?> ₫</td>
                                    <td><?= number_format($discountedPrice * $item['SoLuong']) ?> ₫</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="order-totals">
                        <div class="total-row">
                            <span>Tạm tính:</span>
                            <span><?= number_format($total) ?> ₫</span>
                        </div>
                        <div class="total-row">
                            <span>Phí vận chuyển:</span>
                            <span><?= number_format($shippingFee) ?> ₫</span>
                        </div>
                        <div class="total-row grand-total">
                            <span>Tổng cộng:</span>
                            <span><?= number_format($grandTotal) ?> ₫</span>
                        </div>
                    </div>
                </div>

                <div class="step-actions">
                    <button type="button" class="btn-back" onclick="prevStep(3)"><i class="fas fa-arrow-left"></i> Quay lại</button>
                    <button type="submit" class="btn-confirm"><i class="fas fa-check"></i> Xác nhận đặt hàng</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Handle payment method selection display
document.addEventListener("DOMContentLoaded", function() {
    // Show/hide bank transfer details
    const bankTransferRadio = document.querySelector('input[name="payment_method"][value="Chuyển khoản ngân hàng."]');
    const bankTransferDetails = document.querySelector('.bank-details');
    
    // Show/hide card payment form
    const cardPaymentRadio = document.querySelector('input[name="payment_method"][value="Thanh toán qua thẻ."]');
    const cardPaymentForm = document.querySelector('.card-payment-form');
    
    function updatePaymentDetails() {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        // Bank transfer
        if (bankTransferDetails) {
            bankTransferDetails.style.display = selectedMethod === 'Chuyển khoản ngân hàng.' ? 'block' : 'none';
        }
        
        // Card payment
        if (cardPaymentForm) {
            cardPaymentForm.style.display = selectedMethod === 'Thanh toán qua thẻ.' ? 'block' : 'none';
        }
    }
    
    // Initial update
    updatePaymentDetails();
    
    // Update on change
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', updatePaymentDetails);
    });
    
    // Format card number input
    const cardNumberInput = document.querySelector('.card-payment-form input[placeholder="1234 5678 9012 3456"]');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '');
            if (value.length > 0) {
                value = value.match(new RegExp('.{1,4}', 'g')).join(' ');
            }
            e.target.value = value;
        });
    }
    
    // Format expiry date input
    const expiryInput = document.querySelector('.card-payment-form input[placeholder="MM/YY"]');
    if (expiryInput) {
        expiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    }
});

    // Chuyển đổi giữa các bước
    function goToStep(step) {
        window.location.href = `../user/checkout.php?step=${step}`;
    }

    function nextStep(currentStep) {
    // Validate trước khi chuyển bước
    if (currentStep === 1) {
        const selectedAddress = document.querySelector('input[name="shipping_address"]:checked');
        if (!selectedAddress) {
            alert('Vui lòng chọn địa chỉ giao hàng');
            return;
        }
        
        if (selectedAddress.value === 'new_address') {
            const address = document.querySelector('textarea[name="new_address"]').value;
            if (!address.trim()) {
                alert('Vui lòng nhập địa chỉ giao hàng');
                return;
            }
        }
    }
    
    // Submit form để lưu dữ liệu vào session
    const form = document.getElementById('checkout-form');
    form.action = `../user/checkout.php?step=${currentStep + 1}`;
    form.submit();
}

function prevStep(currentStep) {
    const form = document.getElementById('checkout-form');
    form.action = `../user/checkout.php?step=${currentStep - 1}`;
    form.submit();
}

    // Cập nhật thông tin hiển thị ở bước xác nhận
    function updateSummaryDisplay() {
        if (document.getElementById('step-3').style.display === 'block') {
            // Hiển thị thông tin địa chỉ
            const selectedAddress = document.querySelector('input[name="shipping_address"]:checked');
            let addressHtml = '';
            
            if (selectedAddress.value.startsWith('saved_')) {
                const addressId = selectedAddress.value.split('_')[1];
                const addressCard = selectedAddress.closest('.address-card');
                addressHtml = addressCard.querySelector('.address-details').innerHTML;
            } else {
                const name = document.querySelector('input[name="new_name"]').value;
                const phone = document.querySelector('input[name="new_phone"]').value;
                const address = document.querySelector('textarea[name="new_address"]').value;
                
                addressHtml = `
                    <strong>${name}</strong><br>
                    ${address}<br>
                    ĐT: ${phone}
                `;
            }
            
            document.getElementById('shipping-info-display').innerHTML = addressHtml;
            
            // Hiển thị phương thức thanh toán
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            let paymentHtml = '';
            
            switch (paymentMethod) {
                case 'Thanh toán khi nhận hàng.':
                    paymentHtml = '<i class="fas fa-money-bill-wave"></i> Thanh toán khi nhận hàng (COD)';
                    break;
                case 'Chuyển khoản ngân hàng.':
                    paymentHtml = '<i class="fas fa-university"></i> Chuyển khoản ngân hàng';
                    break;
                case 'Thanh toán qua thẻ.':
                    paymentHtml = '<i class="fas fa-credit-card"></i> Thanh toán bằng thẻ tín dụng/ghi nợ';
                    break;
            }
            
            document.getElementById('payment-info-display').innerHTML = paymentHtml;
        }
    }

    // Gọi hàm khi trang tải xong
    document.addEventListener('DOMContentLoaded', function() {
        updateSummaryDisplay();
        
        // Theo dõi thay đổi địa chỉ/phương thức thanh toán
        document.querySelectorAll('input[name="shipping_address"], input[name="payment_method"]').forEach(input => {
            input.addEventListener('change', updateSummaryDisplay);
        });
    });

    const selectedMethod = sessionStorage.getItem('selectedPaymentMethod');
if (selectedMethod) {
    const radio = document.querySelector(`input[name="payment_method"][value="${selectedMethod}"]`);
    if (radio) radio.checked = true;
}

const selectedAddress = sessionStorage.getItem('selectedAddress');
if (selectedAddress) {
    const radio = document.querySelector(`input[name="shipping_address"][value="${selectedAddress}"]`);
    if (radio) radio.checked = true;
}

    </script>
    <script src="../js/checkout-cart.js"></script>
</body>
</html>