<?php
session_start();
require_once('../BackEnd/ConnectionDB/DB_classes.php');

$connect = Database::getConnection();
$spBUS = new SanPhamBUS();
$kmBUS = new KhuyenMaiBUS();
$thBUS = new ThuongHieuBUS();
$lspBUS = new LoaiSanPhamBUS();


// $MaSP = isset($_GET['MaSP']) ? $_GET['MaSP'] : null;
$MaLoai = isset($_GET['MaLoai']) ? (int)$_GET['MaLoai'] : null;
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
    <title>Trang chủ EDEN Beauty</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- HEADER -->
    <?php include 'header.php'; ?>
<!-- Banner quảng cáo -->
<div class="banner-QC">
    <div class="slides-container">
        <div class="slides">
            <div class="slide">
                <img src="../img/banner1.webp" alt="Banner 1">
            </div>
            <div class="slide">
                <img src="../img/banner2.webp" alt="Banner 2">
            </div>
            <div class="slide">
                <img src="../img/banner3.webp" alt="Banner 3">
            </div>
        </div>
    </div>
    <!-- Nút điều hướng -->
    <button class="prev">&#10094;</button>
    <button class="next">&#10095;</button>
</div>

<!--GIỚI THIỆU-->
<section class="introduce-section">
    <div class="container">
        <div class="row">
            <div class="col-left">
                <img src="../img/intro.jpg" alt="Introduction Image" class="intro-image">
            </div>
            <div class="col-right">
                <h2><strong>GIỚI THIỆU VỀ CHÚNG TÔI</strong></h2>
                <p>Chúng tôi EDEN BEAUTY là một đội ngũ đam mê sáng tạo, cam kết mang đến cho khách hàng những sản phẩm chất lượng, phù hợp với nhu cầu và xu hướng mới nhất.</p>
                <p>được thành lập vào năm 2019. Đến nay, với hệ thống 8 cửa hàng được đặt tại trung tâm các thành phố lớn, các trung tâm thương mại cao cấp sầm uất, hiện đại bậc nhất, mỗi ngày thu hút gần 3.000 lượt khách đến tham quan và mua sắm.
                    Bean Perfume chính thức trở thành doanh nghiệp phân phối Mỹ Phẩm chính hãng lớn tại Việt Nam.</p>
                <p>Với sứ mệnh đem lại những trải nghiệm tuyệt vời, chúng tôi không ngừng nỗ lực để mang lại sự hài lòng cho khách hàng.</p>
            </div>
        </div>
    </div>

    <!--LÝ do tin dùng-->
     <div class="reasons-header">
        <h2 class="reasons-title">LÝ DO TIN DÙNG</h2>
    </div>
    <div class="reasons-container">
        <div class="reason-item">
            <i class="fas fa-clock"></i>
            <h3>Thanh toán nhanh</h3>
            <p>Hỗ trợ nhiều hình thức thanh toán kết hợp online và offline.</p>
        </div>
        <div class="reason-item">
            <i class="fas fa-tags"></i>
            <h3>Giá cả tối ưu</h3>
            <p>Đảm bảo giá cả tốt nhất và phù hợp với từng đối tượng khách hàng.</p>
        </div>
        <div class="reason-item">
            <i class="fas fa-gift"></i>
            <h3>Khuyến mãi lớn</h3>
            <p>Các chương trình ưu đãi giúp khách hàng tiết kiệm hơn.</p>
        </div>
    </div>

    <!-- Thành tích -->
    <div class="statistics-container">
        <div class="statistic-item">
            <h3>12</h3>
            <p>Năm kinh nghiệm</p>
        </div>
        <div class="statistic-item">
            <h3>200</h3>
            <p>Nhân viên</p>
        </div>
        <div class="statistic-item">
            <h3>3000+</h3>
            <p>Khách hàng</p>
        </div>
        <div class="statistic-item">
            <h3>8</h3>
            <p>Cửa hàng</p>
        </div>
    </div>
</div>
</section>

<!-- BÁN CHẠY -->
<section id="featured-products" class="featured-products">
  <hr class="col">
  <h2 class="section-title">SẢN PHẨM HOT</h2>
  <div class="product-container">
    <?php
      require_once('../BackEnd/ConnectionDB/DB_classes.php');
      $spBUS = new SanPhamBUS();
      $dsSanPham = $spBUS->select_best_selling();

      $count = 0;
      foreach ($dsSanPham as $sp) {
        if ((int)$sp['TrangThai'] === 0) continue;

        $giaKM = number_format($sp['GiaSP'], 0, ',', '.') . 'đ';
        $giaGoc = ($sp['GiaTriKM'] > 0)
          ? number_format($sp['GiaSP'] / (1 - $sp['GiaTriKM'] / 100), 0, ',', '.') . 'đ'
          : null;
        $soLuongBan = $sp['DaBan'] ?? 0;
        $extraClass = ($count >= 6) ? ' hidden-product' : '';

        // URL chi tiết sản phẩm
        $productUrl = 'detail.php?MaSP=' . $sp['MaSP'];
    ?>
      <a href="<?= $productUrl ?>" class="product-item<?= $extraClass ?> nsx">
        <img src="<?= $sp['HinhAnh'] ?>" alt="<?= htmlspecialchars($sp['TenSP']) ?>" class="product-image" />
        <h3 class="product-name"><?= htmlspecialchars($sp['TenSP']) ?></h3>
        <p class="product-price">
          <?= $giaKM ?>
          <?php if ($giaGoc): ?>
            <span class="original-price"><?= $giaGoc ?></span>
          <?php endif; ?>
        </p>
        <p class="sale-info">Đã bán <?= number_format($soLuongBan) ?> sản phẩm</p>
        <button class="add-to-cart" type="button">Thêm vào giỏ</button>
      </a>
    <?php
        $count++;
      }
    ?>
  </div>

  <!-- XEM TẤT CẢ -->
  <div class="view-all">
    <button class="view-all-btn" onclick="showAllProducts()">Xem tất cả</button>
  </div>
</section>

<script>
  function showAllProducts() {
    const hiddenItems = document.querySelectorAll('.featured-products .hidden-product');
    hiddenItems.forEach(item => item.classList.remove('hidden-product'));
    document.querySelector('.view-all').style.display = 'none';
  }
</script>


<script>
  function showAllProducts() {
    const hiddenItems = document.querySelectorAll('.featured-products .hidden-product');
    hiddenItems.forEach(item => item.classList.remove('hidden-product'));
    document.querySelector('.view-all').style.display = 'none';
  }
</script>



<!-- BIG SALE -->
<section id="big-sale" class="big-sale-section">
    <div class="sale-header">
        <h2 class="sale-title">ƯU ĐÃI ĐỘC QUYỀN GIẢM CHẤT ĐẾN 50%</h2>
        <p class="sale-description">Sản phẩm sale đến khi hết hàng. Tiết kiệm đến 50%, đừng bỏ lỡ bạn ơi...</p>
    </div>
    <div class="sale-timer">
        <div class="timer-box">
            <span class="timer-value">67</span>
            <span class="timer-label">Ngày</span>
        </div>
        <div class="timer-box">
            <span class="timer-value">17</span>
            <span class="timer-label">Giờ</span>
        </div>
        <div class="timer-box">
            <span class="timer-value">31</span>
            <span class="timer-label">Phút</span>
        </div>
        <div class="timer-box">
            <span class="timer-value">48</span>
            <span class="timer-label">Giây</span>
        </div>
    </div>
    <!-- BIG SALE -->
    <section class="big-sale">
    <h2 class="section-title">BIG SALE</h2>
    <div class="big-sale-container">
        <?php
        require_once('../BackEnd/ConnectionDB/DB_classes.php');
        $spBUS = new SanPhamBUS();
        $dsSale = $spBUS->select_all_order_by_discount(); // Lấy tất cả sản phẩm, đã sắp theo giảm giá giảm dần
        $dem = 0;

        foreach ($dsSale as $sp) {
            if ((int)$sp['TrangThai'] === 0 || $sp['GiaTriKM'] < 50) continue;

            $hiddenClass = ($dem >= 6) ? 'hidden-product' : '';

            $giaKM = number_format($sp['GiaSP'], 0, ',', '.') . 'đ';
            $giaGoc = number_format($sp['GiaSP'] / (1 - $sp['GiaTriKM'] / 100), 0, ',', '.') . 'đ';
            $phanTramGiam = $sp['GiaTriKM'];
            $soLuong = $sp['SoLuong'] ?? 100;

            echo '
            <div class="big-sale-item ' . $hiddenClass . '">
            <a href="detail.php?MaSP=' . $sp['MaSP'] . '" class="product-link">
                <img src="' . $sp['HinhAnh'] . '" alt="' . htmlspecialchars($sp['TenSP']) . '" class="big-sale-image">
                <h3 class="big-sale-name">' . htmlspecialchars($sp['TenSP']) . '</h3>
                <p class="big-sale-price">
                ' . $giaKM . ' <span class="original-price">' . $giaGoc . '</span> <span class="discount">-' . $phanTramGiam . '%</span>
                </p>
                <p class="sale-info">Còn ' . $soLuong . ' sản phẩm</p>
            </a>
            <button class="add-to-cart">Thêm vào giỏ</button>
            </div>';
            $dem++;
        }
        ?>
    </div>

    <!-- Nút xem tất cả -->
    <div class="view-all">
        <button class="view-all-btn" onclick="toggleBigSale()">Xem tất cả</button>
    </div>
    </section>

    <script>
    function toggleBigSale() {
    const hiddenProducts = document.querySelectorAll('.big-sale .hidden-product');
    hiddenProducts.forEach(item => item.classList.remove('hidden-product'));

    const button = document.querySelector('.big-sale .view-all-btn');
    button.style.display = 'none';
    }
</script>

<!--FOOTER-->

<?php include 'footer.php'; ?>
   
<script src="../js/index.js"></script>   

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const hash = window.location.hash;
    if (hash) {
      const target = document.querySelector(hash);
      if (target) {
        // Cuộn mượt đến phần tử
        target.scrollIntoView({ behavior: "smooth" });
      }
    }
  });
</script>

</body>
</html>
