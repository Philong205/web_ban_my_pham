<?php
session_start();
require_once('../BackEnd/ConnectionDB/DB_classes.php');

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
    <title>Sản Phẩm | EDEN Beauty</title>
  <link rel="stylesheet" href="../css/index.css" />
  <link rel="stylesheet" href="../css/detail.css" />
  <link rel="stylesheet" href="../css/product.css" />
  <link rel="stylesheet" href="../css/responsive.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
</head>
<body>
  
    <!-- HEADER -->
    <?php include 'header.php'; ?>
  <!-- DANH MỤC & TÌM KIẾM NÂNG CAO -->
  <section class="product-category">
    <div class="category-header">
      <h3 class="category-title">DANH MỤC SẢN PHẨM</h3>
      <div class="sort-options">
        <label for="sort-select">Sắp xếp theo</label>
        <select id="sort-select">
          <option value="default" <?= $sortOrder === 'default' ? 'selected' : '' ?>>Mặc định</option>
          <option value="price-asc" <?= $sortOrder === 'price-asc' ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
          <option value="price-desc" <?= $sortOrder === 'price-desc' ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
        </select>
      </div>
    </div>
  </section>

  <!-- FORM TÌM KIẾM NÂNG CAO -->
  <form method="GET" action="product.php" class="advanced-search">
    <input type="text" name="keyword" placeholder="Từ khóa..." value="<?= htmlspecialchars($keyword) ?>" />
    <select name="MaLoai">
      <option value="">-- Loại sản phẩm --</option>
      <?php 
      foreach ($dsLoai as $loai) {
        $selected = ($MaLoai == $loai['MaLoai']) ? 'selected' : '';
        echo "<option value='{$loai['MaLoai']}' $selected>" . htmlspecialchars($loai['TenLoai']) . "</option>";
      }
      ?>
    </select>
    <input type="number" name="minPrice" placeholder="Giá từ" value="<?= $minPrice ?? '' ?>" />
    <input type="number" name="maxPrice" placeholder="Giá đến" value="<?= $maxPrice ?? '' ?>" />
    <button type="submit">Lọc</button>
  </form>

  <!-- DANH SÁCH SẢN PHẨM -->
<section id="products" class="products">
  <div class="product-container" id="product-list">
    <?php
    $count = 0;
    foreach ($dsSanPham as $sp) {
      if ((int)$sp['TrangThai'] === 0) continue;

      $giaGoc = number_format($sp['GiaSP'], 0, ',', '.') . 'đ';
      $phanTramGiam = $sp['GiaTriKM'];
      $giaKM = ($phanTramGiam > 0) 
        ? number_format($sp['GiaSP'] * (1 - $phanTramGiam / 100), 0, ',', '.') . 'đ' 
        : number_format($sp['GiaSP'], 0, ',', '.') . 'đ';

      $extraClass = ($count >= 8) ? ' hidden-product' : '';
    ?>
      <a href="detail.php?MaSP=<?= $sp['MaSP'] ?>" class="product-item<?= $extraClass ?> nsx">
        <img src="<?= $sp['HinhAnh'] ?>" alt="<?= htmlspecialchars($sp['TenSP']) ?>" class="product-image" />
        <h3 class="product-name"><?= htmlspecialchars($sp['TenSP']) ?></h3>
        <p class="product-price">
          <?= $giaKM ?>
          <?php if ($giaGoc): ?>
            <span class="original-price"><?= $giaGoc ?></span>
            <span class="discount_product"><?= $phanTramGiam ?>%</span>
          <?php endif; ?>
        </p>
        <button class="add-to-cart" onclick="addToCart(<?= $product['MaSP'] ?>)" id="addToCart">
                Thêm vào giỏ hàng
            </button>
      </a>
    <?php $count++; } ?>
  </div>

  <!-- Hiển thị phân trang -->
  <div id="pagination">
  <ul class="pagination-list">
    <?php
  $totalProducts = $spBUS->getTotalProducts($keyword, $MaLoai, $minPrice, $maxPrice);
  $totalPages = ceil($totalProducts / $limit);

  // Lấy tất cả tham số hiện tại
  $queryParams = $_GET;

  // Nút "←" quay về trang trước
  if ($page > 1) {
      $queryParams['page'] = $page - 1;
      $prevURL = 'product.php?' . http_build_query($queryParams);
      echo "<li><a href='$prevURL' class='pagination-prev'>←</a></li>";
  }

  // Các nút số trang
  for ($i = 1; $i <= $totalPages; $i++) {
      $queryParams['page'] = $i;
      $pageURL = 'product.php?' . http_build_query($queryParams);
      $activeClass = ($i == $page) ? 'pagination-num active' : 'pagination-num';
      echo "<li><a href='$pageURL' class='$activeClass'>$i</a></li>";
  }

  // Nút "→" chuyển sang trang sau
  if ($page < $totalPages) {
      $queryParams['page'] = $page + 1;
      $nextURL = 'product.php?' . http_build_query($queryParams);
      echo "<li><a href='$nextURL' class='pagination-next'>→</a></li>";
  }
  ?>
  </ul>
</div>

</section>


  <!-- FOOTER -->
  <?php include 'footer.php'; ?>


  <script>
    document.getElementById("sort-select").addEventListener("change", function () {
      const sortOrder = this.value;
      const params = new URLSearchParams(window.location.search);
      params.set('sort', sortOrder);
      window.location.href = 'product.php?' + params.toString();
    });
  </script>

  <script src="../js/product.js"></script>
  <script src="../js/user.js"></script>
  <script src="../js/index.js"></script>
</body>
</html>
