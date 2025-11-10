<?php
session_start();
require_once('../BackEnd/ConnectionDB/DB_classes.php');

// Kiểm tra đăng nhập
// if (!isset($_SESSION['user'])) {
//     header("Location: ../php/login.php");
//     exit();
// }

// $db = new DB_driver();
// $userId = $_SESSION['user']['id'];

$connect = Database::getConnection();
$spBUS = new SanPhamBUS();
$kmBUS = new KhuyenMaiBUS();
$thBUS = new ThuongHieuBUS();
$lspBUS = new LoaiSanPhamBUS();

// $db = new DB_driver();
// $userId = $_SESSION['user']['id'];

$MaSP = isset($_GET['MaSP']) ? $_GET['MaSP'] : null;
$giaKM = null;
$giaGoc = null;
$giaTriKM = null;

if ($MaSP) {
    $sp = $spBUS->select_by_id('*',$MaSP);

    if ($sp && (int)$sp['TrangThai'] !== 0) {
        $giaTriKM = ($sp['GiaTriKM'] > 0) ? $sp['GiaTriKM'] . '%' : null;
        $phanTramGiam = $sp['GiaTriKM'];
        if ($sp['GiaTriKM'] > 0) {
            $giaKM = number_format($sp['GiaSP'] * (1 - $sp['GiaTriKM'] / 100), 0, ',', '.') . 'đ';
            $giaGoc = number_format($sp['GiaSP'], 0, ',', '.') . 'đ';
        } else {
            $giaGoc = number_format($sp['GiaSP'], 0, ',', '.') . 'đ';
        }
    } else {
        // Nếu sản phẩm không tồn tại hoặc đã bị ẩn
        $sp = null;
    }
}
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
    <title>Chi tiết sản phẩm | EDEN Beauty</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/product.css">
    <link rel="stylesheet" href="../css/detail.css">
    <link rel="stylesheet" href="../css/responsive.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>

<!-- HEADER -->
<?php include 'header.php'; ?>
<!-- CHI TIẾT SẢN PHẨM -->
<div id="product-detail" class="product-detail">
    <div class="detail-container">
        <div class="detail-left">
            <img id="detail-image" src="" alt="Hình ảnh sản phẩm" class="detail-image">
        </div>
        <div class="detail-right">
            <h1 id="detailName" class="detail-name"></h1>
            <p class="detail-descri">Thương hiệu: <a href="javascript:void(0);" id="TenTH" class="nsx" onclick="layThuonghieu(this.dataset.math)">Tên thương hiệu</a>
            <div class="detail-price">
                <?php if ($giaKM): ?>
                    <?= $giaKM ?> <span class="original-price"><?= $giaGoc ?></span>
                <?php elseif ($giaGoc): ?>
                    <?= $giaGoc ?>
                <?php else: ?>
                    <span class="error-message">Giá không khả dụng</span>
                <?php endif; ?>
                <span class="discount_product"><?= $phanTramGiam ?>%</span>
                </div>


            <p class="detail-descri">Dung tích: <span id="DungTich" class="nsx"></span></p>

            <div class="quantity">
                <label for="quantity">Số lượng:</label>
                <input type="number" id="quantity" name="SoLuong" value="1" min="1" max="10">
            </div>

            <form method="POST" style="margin-top: 10px;">
                <input type="hidden" name="MaSP" value="<?= $sp['MaSP'] ?>">
                <input type="hidden" name="SoLuong" id="hiddenQuantity" value="1">
                <button type="submit" id="addToCartButton" class="add-to-cart">Thêm vào giỏ hàng</button>
            </form>

        </div>
    </div>
</div>

<!-- MÔ TẢ SẢN PHẨM -->
<div class="product-description">
    <div class="decri">
        <h2 id="detailName2"></h2>
        <p><strong>Mô tả sản phẩm:</strong></p>
        <p id="MoTaSP"></p>
    </div>
    <div class="ts">
        <p><strong>Thông số sản phẩm:</strong></p>
        <table>
            <tr><th>Tên sản phẩm</th><td id="detailName3"></td></tr>
            <tr><th>Thương hiệu</th><td><a href="javascript:void(0);" id="TenTH2" class="nsx" data-math="TH001" onclick="layThuonghieu(this.dataset.math)">Some Brand</a></td>
            <tr><th>Xuất xứ</th><td id="XuatXu"></td></tr>
            <tr><th>Loại da</th><td id="LoaiDa"></td></tr>
        </table>
    </div>
    <div class="tp">
        <p><strong>Thành phần chính:</strong></p>
        <p id="TPChinh"></p>
    </div>
    <div class="tp">
        <p><strong>Thành phần đầy đủ:</strong></p>
        <p id="TPFull"></p>
    </div>
</div>

<!-- -----------------------------------------------------Khung chi tiết thương hiệu--------------------------------------------------- -->



<div id="khungChiTietThuongHieu" class="overlay">
<!-- <span class="close" onclick="this.parentElement.style.transform = 'scale(0)';">&times;</span> -->
<span class="close" onclick="document.getElementById('khungChiTietThuongHieu').classList.remove('open');">&times;</span>


    <div class="hoa-don-header">
        <h2 id="brand-title">Thông tin chi tiết thương hiệu</h2>
        <div class="brand-info">
            <p id="brand-description"></p>
            <img id="brand-logo" src="" alt="Logo thương hiệu" class="brand-logo">
            <p><strong>Xuất xứ:</strong> <span id="brand-origin"></span></p>
            <p><strong>Mô tả:</strong> <span id="brand-details"></span></p>
        </div>
    </div>
</div>

<!-- XEM THÊM -->
<div class="view-more-products">
    <a href="product.php" class="view-more-btn">← Xem các sản phẩm khác</a>
</div>

<!-- FOOTER -->
<?php include 'footer.php'; ?>


<!-- INPUT HIDDEN PHỤC VỤ JS -->
<div style="display: none;">
    <input type="text" id="MaSP">
    <input type="text" id="LoaiSanPham">
    <input type="text" id="ThuongHieu">
    <input type="text" id="XuatXu">
    <input type="text" id="SoLuong">
    <input type="text" id="DungTich">
    <input type="text" id="LoaiDa">
    <input type="text" id="KhuyenMai">
    <input type="text" id="TPChinh">
    <input type="text" id="TPFull">
    <input type="text" id="MoTaSP">
    <input type="text" id="TenKM">
    <input type="text" id="GiaTriKM">
    <input type="text" id="TenLoai">
    <input type="text" id="TenTH">
</div>

<!-- SCRIPT -->
<script src="../js/user.js"></script>
<script src="../js/index.js"></script>

<script>
    // Lấy MaSP từ URL và gọi laySanPham
    function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    document.addEventListener("DOMContentLoaded", function () {
        const MaSP = getQueryParam("MaSP");
        if (MaSP) {
            laySanPham(MaSP);
        } else {
            alert("Không tìm thấy mã sản phẩm trên URL!");
            document.getElementById("product-detail").innerHTML = "<p>Sản phẩm không hợp lệ.</p>";
        }

        // document.getElementById('addToCart').addEventListener('click', function () {
        //     alert("Đã thêm sản phẩm vào giỏ hàng");
        // });
    });
</script>

<!-- ----------------------------------------------------------thêm giỏ hàng----------------------------------------------------------------- -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const addToCartButton = document.querySelector("#addToCartButton");
  const quantityInput = document.getElementById("quantity");
  const hiddenQuantity = document.getElementById("hiddenQuantity");

  quantityInput.addEventListener("input", function () {
    hiddenQuantity.value = this.value;
  });

  addToCartButton.addEventListener("click", function (e) {
    e.preventDefault();

    const MaSP = document.querySelector('input[name="MaSP"]').value;
    const SoLuong = hiddenQuantity.value;

    fetch("../php/them_spGio.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `action=update&productId=${encodeURIComponent(MaSP)}&quantity=${encodeURIComponent(SoLuong)}`,
    })
    .then(async (response) => {
        const text = await response.text();
        console.log("Server trả về:", text);

        try {
            const data = JSON.parse(text);
            if (data.success) {
              alert("✅ Sản phẩm đã được thêm vào giỏ hàng!");
              window.location.href = "cart.php";
            } else {
              alert("❌ Có lỗi khi thêm sản phẩm: " + data.message);
            }
        } catch (e) {
            alert("🚫 Dữ liệu trả về không hợp lệ.");
            console.error("JSON parse error:", e);
        }
    })
    .catch((error) => {
        console.error("Lỗi fetch:", error);
        alert("🚫 Lỗi kết nối đến máy chủ.");
    });
  });
});
</script>

</body>
</html>
