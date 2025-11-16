<?php

// require_once(__DIR__ . '../BackEnd/ConnectionDB/DB_classes.php');
require_once('../BackEnd/ConnectionDB/DB_classes.php');

// Khởi tạo BUS
$hdBUS = new HoaDonBUS();
$cthdBUS = new ChiTietHoaDonBUS();
$ttBUS = new TrangThaiBUS();

$i = 1;

// Lọc theo ngày nếu có submit form
$fromDate = $_POST['fromDate'] ?? null;
$toDate = $_POST['toDate'] ?? null;

// Lấy tất cả hóa đơn và chi tiết hóa đơn
$tatCaHoaDon = $hdBUS->select_all();
$dsCTHD = $cthdBUS->select_all();

if ($fromDate && $toDate) {
    $dsHoaDon = array_filter($tatCaHoaDon, function ($item) use ($fromDate, $toDate) {
        $ngayLap = substr($item['NgayLap'], 0, 10);
        return $ngayLap >= $fromDate && $ngayLap <= $toDate;
    });
} else {
    $dsHoaDon = $tatCaHoaDon;
}
?>

<!--___________________________________________________________________________________________________________________________-->
<!-- ____________________________________________________Đơn Hàng_____________________________________________________________ -->
<!--___________________________________________________________________________________________________________________________ -->

<div id="donHang" class="container">

<!-- Bảng danh sách hóa đơn -->
<div style="width: 100%; max-height: 640px; overflow-x: auto;">
    <table class="table-header" style="min-width: 2100px;">
      <thead style="position: sticky; top: 0; background: #f2f2f2;">
        <tr>
          <th style="width: 50px;">STT</th>
          <th style="width: 120px;">Mã đơn</th>
          <th style="width: 150px;">Khách</th>
          <th style="width: 250px;">Sản phẩm</th>
          <th style="width: 200px;">Địa chỉ</th>
          <th style="width: 120px;">Tổng tiền</th>
          <th style="width: 150px;">Ngày giờ</th>
          <th style="width: 120px;">Trạng thái</th>
          <th style="width: 180px;">Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($dsHoaDon)): ?>
            <?php foreach ($dsHoaDon as $row): ?>
                <tr id="order-<?= htmlspecialchars($row['MaHD']) ?>">
                    <td style="text-align:center;"><?= $i++ ?></td>
                    <td style="text-align:center;"><?= htmlspecialchars($row['MaHD']) ?></td>
                    <td style="text-align:center;"><?= htmlspecialchars($row['NguoiNhan']) ?></td>

                    <!-- Sản phẩm -->
                    <td style="text-align:center;">
                        <?php
                        $sanPhams = array_filter($dsCTHD, fn($item) => $item['MaHD'] === $row['MaHD']);
                        $sttSP = 1;
                        foreach ($sanPhams as $sp) {
                            echo $sttSP++ . '. ' . htmlspecialchars($sp['TenSP']) . "<br><br>";
                        }
                        ?>
                    </td>

                    <td style="text-align:center;"><?= htmlspecialchars($row['DiaChi']) ?></td>
                    <td style="text-align:center;"><?= number_format($row['TongTien'], 0, ',', '.') ?>₫</td>
                    <td style="text-align:center;"><?= htmlspecialchars($row['NgayLap']) ?></td>

                    <!-- Trạng thái với màu -->
                    <?php
                        $colorMap = [
                            1 => 'orangered', // Chưa xác nhận
                            2 => 'blue',      // Đã xác nhận
                            3 => '#2196f3',   // Đang giao hàng
                            4 => 'green',     // Đã giao hàng
                            5 => 'red'        // Đã hủy
                        ];
                        $color = $colorMap[$row['MaTT']] ?? 'black';
                    ?>
                    <td style="text-align:center; color: <?= $color ?>"><?= htmlspecialchars($row['TrangThai']) ?></td>

                    <!-- Hành động -->
                    <td style="text-align:center;">
                        <button class="btn edit-btn" style="height: 30px;"
                                onclick="chitietHoaDon('<?= $row['MaHD'] ?>'); document.getElementById('khungHoaDon').style.transform='scale(1)';">
                            <i class="fa fa-plus-square"></i> Chi tiết
                        </button>
                        <br><br>
                        <button class="btn delete-btn" style="height: 30px;"
                                onclick="updateTrangThai('<?= $row['MaHD'] ?>'); document.getElementById('khungUpdateTrangThai').style.transform='scale(1)';">
                            <i class="fa fa-refresh"></i> Update trạng thái
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="9" style="text-align:center;">Không có đơn hàng nào.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
</div>

<!-- Form lọc & tìm kiếm -->
<div class="table-footer">
    <form method="POST" class="timTheoNgay">
        Từ ngày: <input type="date" name="fromDate" value="<?= htmlspecialchars($fromDate) ?>" />
        Đến ngày: <input type="date" name="toDate" value="<?= htmlspecialchars($toDate) ?>" />
        <button type="submit"><i class="fa fa-search"></i> Tìm</button>
    </form>

    <select id="kieuTimDonHang">
        <option value="ma">Tìm theo mã đơn</option>
        <option value="khach">Tìm theo tên khách hàng</option>
        <option value="diachi">Tìm theo địa chỉ</option>
        <option value="trangthai">Tìm theo trạng thái</option>
    </select>
    <input type="text" id="searchDonHang" onkeyup="timKiemDonHang()" placeholder="Tìm kiếm..." />
</div>

<!-- Khung chi tiết hóa đơn -->
<div id="khungHoaDon" class="overlay">
    <span class="close" onclick="this.parentElement.style.transform='scale(0)';">&times;</span>

    <div class="hoa-don-header">
        <h1>HÓA ĐƠN EDEN BEAUTY</h1>
        <p><strong>Cửa hàng:</strong> Mỹ Phẩm Eden Beauty</p>
        <p><strong>Địa chỉ:</strong> 273 An Dương Vương, Quận 5, TP.HCM</p>
        <p><strong>SĐT:</strong> 0123 456 789</p>
        <p><strong>Email:</strong> support@EdenBeauty.com</p>
    </div>

    <section class="thong-tin-khach-hang">
        <h3>Thông tin khách hàng:</h3>
        <p><strong>Tên khách hàng:</strong> <span id="NguoiNhan"></span></p>
        <p><strong>Địa chỉ:</strong> <span id="DiaChi"></span></p>
        <p><strong>Ngày giờ mua:</strong> <span id="NgayLap"></span></p>
    </section>

    <section class="danh-sach-san-pham">
        <h3>Danh sách sản phẩm:</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Đơn giá (VNĐ)</th>
                    <th>Thành tiền (VNĐ)</th>
                </tr>
            </thead>
            <tbody id="danhSachSanPham"></tbody>
            <tfoot>
                <tr>
                    <td colspan="4"><strong>Tiền ship:</strong></td>
                    <td>30.000₫</td>
                </tr>
                <tr>
                    <td colspan="4"><strong>Tổng cộng:</strong></td>
                    <td id="TongTien"></td>
                </tr>
            </tfoot>
        </table>
    </section>

    <footer class="cam-on">
        <p>Cảm ơn quý khách đã mua sắm tại cửa hàng chúng tôi!</p>
    </footer>
</div>

<!-- Khung cập nhật trạng thái -->
<div id="khungUpdateTrangThai" class="overlay">
    <span class="close" onclick="this.parentElement.style.transform='scale(0)';">&times;</span>

    <form method="POST">
        <input type="hidden" name="HoaDon" id="HoaDon" value="">
        <table class="overlayTable table-outline table-content table-header hideImg">
            <style>
            #khungUpdateTrangThai td {
                color: white;
            }
        </style>
            <tr><th colspan="2">Cập nhật trạng thái</th></tr>
            <tr>
                <td>Trạng thái hiện tại:</td>
                <td><span id="TrangThaiDisplay"></span></td>
            </tr>
            <tr>
                <td>Chọn trạng thái mới:</td>
                <td>
                    <select name="updateTrangthai" id="updateTrangthai" required>
                        <option value="">-- Chọn trạng thái --</option>
                        <?php foreach ($ttBUS->select_all() as $tt): ?>
                            <option value="<?= $tt['MaTT'] ?>"><?= htmlspecialchars($tt['TrangThai']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="TrangThaiHidden" id="TrangThaiHidden" value="">
                </td>
            </tr>
            <tr>
                <td colspan="2" class="table-footer">
                    <button type="submit" name="sbmcapnhat">CẬP NHẬT</button>
                </td>
            </tr>
        </table>
    </form>
</div>
</div>
<!-- // end đơn hàng -->