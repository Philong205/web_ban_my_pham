<?php
require_once '../php/connect.php'; // Kết nối CSDL

// Kiểm tra mã hóa đơn
if (!isset($_GET['mahd']) || !is_numeric($_GET['mahd'])) {
    echo "<div style='color:red; text-align:center;'>Mã hóa đơn không hợp lệ!</div>";
    exit;
}
$mahd = intval($_GET['mahd']);

// Lấy thông tin hóa đơn
$sql = "SELECT hd.MaHD, nd.TenND, nd.DiaChi, nd.SDT, hd.NgayLap 
        FROM hoadon hd 
        JOIN nguoidung nd ON hd.MaND = nd.MaND 
        WHERE hd.MaHD = $mahd";

$result = $conn->query($sql);
if ($result->num_rows == 0) {
    echo "<div style='color:red; text-align:center;'>Không tìm thấy hóa đơn.</div>";
    exit;
}
$row = $result->fetch_assoc();

// Lấy danh sách sản phẩm trong hóa đơn
$sql_ct = "SELECT sp.TenSP, cthd.SoLuong, cthd.DonGia, (cthd.SoLuong * cthd.DonGia) AS ThanhTien 
           FROM chitiethoadon cthd 
           JOIN sanpham sp ON cthd.MaSP = sp.MaSP 
           WHERE cthd.MaHD = $mahd";

$result_ct = $conn->query($sql_ct);
$ds_sanpham = [];
$tong_tien = 0;

while ($sp = $result_ct->fetch_assoc()) {
    $ds_sanpham[] = $sp;
    $tong_tien += $sp['ThanhTien'];
}
?>

<style>
  .hoadon-wrapper {
    font-family: 'Segoe UI', sans-serif;
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    width: 90%;
    max-width: 900px;
    margin: auto;
  }
  .hoadon-wrapper h2 {
    text-align: center;
    color: #dc143c;
    margin-bottom: 20px;
  }
  .hoadon-wrapper .info p {
    margin: 5px 0;
  }
  .hoadon-wrapper table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
  }
  .hoadon-wrapper thead {
    background-color: #f0f0f0;
  }
  .hoadon-wrapper th, .hoadon-wrapper td {
    padding: 10px;
    border: 1px solid #ccc;
  }
  .hoadon-wrapper tfoot td {
    font-weight: bold;
    background: #fafafa;
  }
  .hoadon-wrapper .total-row td {
    text-align: right;
  }
</style>

<div class='hoadon-wrapper'>
  <h2>CHI TIẾT HÓA ĐƠN</h2>
  <div class='info'>
    <p><strong>Mã hóa đơn:</strong> <?php echo $row['MaHD']; ?></p>
    <p><strong>Khách hàng:</strong> <?php echo $row['TenND']; ?></p>
    <p><strong>Địa chỉ:</strong> <?php echo $row['DiaChi']; ?></p>
    <p><strong>Số điện thoại:</strong> <?php echo $row['SDT']; ?></p>
    <p><strong>Thời gian:</strong> <?php echo $row['NgayLap']; ?></p>
  </div>
  <table>
    <thead>
      <tr>
        <th>STT</th>
        <th>Sản phẩm</th>
        <th>Số lượng</th>
        <th>Đơn giá</th>
        <th>Thành tiền</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($ds_sanpham as $index => $sp): ?>
      <tr>
        <td><?php echo $index + 1; ?></td>
        <td><?php echo $sp['TenSP']; ?></td>
        <td><?php echo $sp['SoLuong']; ?></td>
        <td><?php echo number_format($sp['DonGia'], 0, ',', '.') . 'đ'; ?></td>
        <td><?php echo number_format($sp['ThanhTien'], 0, ',', '.') . 'đ'; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr class="total-row">
        <td colspan="4">Tổng cộng:</td>
        <td><?php echo number_format($tong_tien, 0, ',', '.') . 'đ'; ?></td>
      </tr>
    </tfoot>
  </table>
</div>
