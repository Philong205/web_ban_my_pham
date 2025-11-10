<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once('../BackEnd/ConnectionDB/DB_driver.php');
require_once('../BackEnd/ConnectionDB/DB_classes.php');

// $spBUS = new SanPhamBUS();
// $kmBUS = new KhuyenMaiBUS();
// $thBUS = new ThuongHieuBUS();
// $lspBUS = new LoaiSanPhamBUS();

// $keyword1 = $_GET['keyword1'] ?? '';

// // Gọi hàm tìm kiếm nâng cao với tham số phân trang
// $dsSanPham = $spBUS->timKiemKeyWord($keyword1);

$spBUS = new SanPhamBUS();
$kmBUS = new KhuyenMaiBUS();
$thBUS = new ThuongHieuBUS();
$lspBUS = new LoaiSanPhamBUS();


// Lấy các tham số lọc từ URL
$sortOrder = $_GET['sort'] ?? 'default';
$keyword = $_GET['keyword'] ?? '';
$MaLoai = isset($_GET['MaLoai']) && $_GET['MaLoai'] !== '' ? (int)$_GET['MaLoai'] : null;
$minPrice = isset($_GET['minPrice']) ? (float)$_GET['minPrice'] : null;
$maxPrice = isset($_GET['maxPrice']) ? (float)$_GET['maxPrice'] : null;
// Lấy trang hiện tại, mặc định là trang 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 8; // Số sản phẩm trên mỗi trang
$offset = ($page - 1) * $limit; // Tính offset cho phân trang

// Gọi hàm tìm kiếm nâng cao với tham số phân trang
$dsSanPham = $spBUS->timKiemNangCao($keyword, $MaLoai, $minPrice, $maxPrice, $sortOrder, $page, $limit);

$dsLoai = $lspBUS->select_all();


// Nếu có lỗi đăng nhập từ login.php chuyển sang, gán vào biến để hiển thị
$error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : null;
unset($_SESSION['login_error']);

// Debug session
error_log("Current session data: " . print_r($_SESSION, true));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDEN Beauty</title>

    <!-- CSS dùng chung -->
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/detail.css" />
    <link rel="stylesheet" href="../css/product.css" />
    <link rel="stylesheet" href="../css/responsive.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <!-- JS thư viện -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
<header class="header">
    <div class="header-container">
        <!-- Logo -->
        <a href="../user/index.php" class="logo">
            <img src="../img/link-hinh-logo.jpg" alt="Logo Website">
        </a>

        <!-- Tìm kiếm -->
        <form method="GET" action="product.php" class="search-box">
        <input type="text" name="keyword" class="search-txt" placeholder="Tìm kiếm sản phẩm..." value="<?= htmlspecialchars($keyword) ?>" />
        <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
      </form>

        <!-- Liên kết tài khoản -->
        <div class="header-links">
            <?php if (isset($_SESSION['user']) && !empty($_SESSION['user'])): ?>
                <div class="user-menu">
                    <a href="../user/user.php" title="Trang cá nhân">
                        <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['user']['name']); ?>
                    </a>
                    <a href="../php/logout.php" title="Đăng xuất" class="logout-link"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            <?php else: ?>
                <a href="#" id="loginBtn" title="Đăng nhập"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a>
            <?php endif; ?>

            <!-- Giỏ hàng -->
            <a href="../user/cart.php" class="cart-link" title="Giỏ hàng">
                <i class="fas fa-shopping-cart"></i>
                <?php
                $cartCount = 0;
                if (isset($_SESSION['user'])) {
                    $db = new DB_driver();
                    $userId = $_SESSION['user']['id'];
                    
                    // Truy vấn giỏ hàng theo database thực tế
                    $sql = "SELECT SUM(gct.SoLuong) AS total 
                            FROM giohang gh
                            JOIN giohang_chitiet gct ON gh.MaGioHang = gct.MaGioHang 
                            WHERE gh.MaND = " . intval($userId);
                    
                    $result = $db->get_list($sql);
                    error_log("Cart query result: " . print_r($result, true));
                    
                    if (!empty($result) && isset($result[0]['total'])) {
                        $cartCount = (int)$result[0]['total'];
                    }
                }
                ?>
                <span class="cart-count"><?php echo $cartCount; ?></span>
            </a>
        </div>
    </div>

    <!-- MENU -->
  <div class="menu">
    <nav class="main-menu">
      <ul class="display1">
        <li><a href="../user/">Trang chủ</a></li>
        <li>
          <a href="../user/product.php">Sản Phẩm<span class="arrow">&#9662;</span></a>
          <ul class="submenu1">
            <?php 
            $lspBUS = new LoaiSanPhamBUS();
            $dsLoai = $lspBUS->select_all();
            foreach ($dsLoai as $loai) { 
              echo '<li><a href="../user/product.php?MaLoai=' . $loai['MaLoai'] . '">' . htmlspecialchars($loai['TenLoai']) . '</a></li>';
            }
            ?>
          </ul>
        </li>
        <!-- <li><a href="index.php#" onclick="scrollToSection('big-sale'); return false;">Hot Deal</a></li>
        <li><a href="index.php#" onclick="scrollToSection('featured-products'); return false;">Bán chạy</a></li> -->
        <li><a href="../user/index.php#big-sale">Hot Deal</a></li>
        <li><a href="../user/index.php#featured-products">Bán chạy</a></li>

      </ul>
    </nav>
  </div>

<script>
document.getElementById('loginBtn')?.addEventListener('click', function(e) {
    e.preventDefault();

    Swal.fire({
        title: 'Đăng nhập',
        html:
            '<input type="email" id="email" class="swal2-input" placeholder="Email">' +
            '<input type="password" id="password" class="swal2-input" placeholder="Mật khẩu">',
        showCancelButton: true,
        confirmButtonText: 'Đăng nhập',
        cancelButtonText: 'Hủy',
        footer: '<a href="../user/dangky.php">Chưa có tài khoản? Đăng ký</a>',
        preConfirm: () => {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!email || !password) {
                Swal.showValidationMessage('Vui lòng nhập đầy đủ thông tin');
                return false;
            }

            // Tạo form để submit ngầm
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../php/login.php';

            const emailInput = document.createElement('input');
            emailInput.type = 'hidden';
            emailInput.name = 'email';
            emailInput.value = email;

            const passwordInput = document.createElement('input');
            passwordInput.type = 'hidden';
            passwordInput.name = 'password';
            passwordInput.value = password;

            form.appendChild(emailInput);
            form.appendChild(passwordInput);
            document.body.appendChild(form);

            form.submit();

            return true;
        }
    });
});
</script>

<!-- <script src="../js/index.js"></script> -->
</body>
</html>