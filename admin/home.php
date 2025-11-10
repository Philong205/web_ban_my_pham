<?php

// Đường dẫn hiện tại: admin/nháp/home.php
// Ta cần quay ra khỏi "admin/nháp" → về thư mục gốc Web2 → vào thư mục php
// include __DIR__ . '../php/connect.php';
require_once('../php/connect.php');

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối database thất bại: " . mysqli_connect_error());
}

// Đếm số khách hàng
$sql_kh = "SELECT COUNT(*) AS so_khach FROM nguoidung";
$so_khach = $conn->query($sql_kh)->fetch_assoc()['so_khach'];

// Đếm số sản phẩm
$sql_sp = "SELECT COUNT(*) AS so_sp FROM sanpham";
$so_sp = $conn->query($sql_sp)->fetch_assoc()['so_sp'];

// Đếm số đơn hàng
$sql_dh = "SELECT COUNT(*) AS so_dh FROM hoadon";
$so_dh = $conn->query($sql_dh)->fetch_assoc()['so_dh'];
?>

<div id="home" class="container">
  <div class="header">
    <div class="nav">
      <div class="search">
        <input type="text" placeholder="Search.." />
        <button type="submit">
          <img src="../image/admin/search.png" alt="" />
        </button>
      </div>
      <div class="user">
        <a href="#" class="btn">Add New</a>
        <img src="../image/admin/notifications.png" alt="" />
        <button class="img-case"
          onclick="document.getElementById('khungThongTinAdmin').style.transform = 'scale(1)'; autoMaSanPham()">
          <img src="../image/admin/user.png" alt="Thông tin Admin" />
        </button>
      </div>
    </div>
  </div>

  <div class="content">
    <div class="cards">
      <div class="card">
        <div class="box">
          <p><?php echo $so_khach; ?></p>
          <span>Khách hàng</span>
        </div>
        <button class="icon-case" onclick="window.location.href = 'admin.php?page=khachhang';">
          <img src="../image/admin/students.png" alt="" />
        </button>
      </div>

      <div class="card">
        <div class="box">
          <p><?php echo $so_sp; ?></p>
          <span>Sản phẩm hiện có</span>
        </div>
        <button class="icon-case" onclick="window.location.href = 'admin.php?page=sanpham';">
          <img src="../image/admin/test.png" alt="" />
        </button>
      </div>

      <div class="card">
        <div class="box">
          <p><?php echo $so_dh; ?></p>
          <span>Tổng đơn hàng</span>
        </div>
        <button class="icon-case" onclick="window.location.href = 'admin.php?page=donhang';">
          <img src="../image/admin/bill.png" alt="" />
        </button>
      </div>

      <div class="card" onclick="moThongKeDoanhThu()">
        <div class="box">
          <h3>Doanh thu</h3>
        </div>
        <div class="icon-case">
          <img src="../image/admin/profit.png" alt="" />
        </div>
      </div>
    </div>

    <div class="content-2">
      <div class="recent-payments">
        <div class="title">
          <h2>Các đơn hàng gần đây</h2>
          <a href="#donHang" class="btn">Xem tất cả</a>
        </div>
        <table>
          <?php
          // Lấy các đơn hàng gần đây
          $sql = "SELECT hd.MaHD, nd.TenND, hd.TongTien 
                  FROM hoadon hd
                  JOIN nguoidung nd ON hd.MaND = nd.MaND
                  ORDER BY hd.MaHD DESC
                  LIMIT 4";

          $result = $conn->query($sql);

          if ($result && $result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>" . htmlspecialchars($row['TenND']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['MaHD']) . "</td>";
                  echo "<td>" . number_format($row['TongTien'], 0, ',', '.') . "đ</td>";
                  echo "<td><button onclick='xemChiTietDonHang(" . $row['MaHD'] . ")' class='btn btn-danger'>Xem chi tiết</button></td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='4'>Không có đơn hàng nào.</td></tr>";
          }
          ?>
        </table>
      </div>

      <div class="new-students">
        <div class="title">
          <h2>Khách hàng mới</h2>
          <a class="btn">Xem chi tiết</a>
        </div>
        <table>
          <tr>
            <th>Thông tin</th>
            <th>Tên</th>
            <th>Chi tiết</th>
          </tr>
          <tr>
            <td><img src="../image/admin/user.png" alt="" /></td>
            <td>Trần Thị D</td>
            <td><img src="../image/admin/info.png" alt="" /></td>
          </tr>
          <tr>
            <td><img src="../image/admin/user.png" alt="" /></td>
            <td>Võ Văn A</td>
            <td><img src="../image/admin/info.png" alt="" /></td>
          </tr>
          <tr>
            <td><img src="../image/admin/user.png" alt="" /></td>
            <td>Lê Ngọc C</td>
            <td><img src="../image/admin/info.png" alt="" /></td>
          </tr>
          <tr>
            <td><img src="../image/admin/user.png" alt="" /></td>
            <td>Lương Thanh B</td>
            <td><img src="../image/admin/info.png" alt="" /></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
function xemChiTietDonHang(mahd) {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "chitiet_donhang.php?mahd=" + mahd, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("noidungChiTietDonHang").innerHTML = xhr.responseText;
            document.getElementById("modalChiTietDonHang").style.display = "flex";
        }
    };
    xhr.send();
}

function dongModalChiTiet() {
    document.getElementById("modalChiTietDonHang").style.display = "none";
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<div id="khungThongKe" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
  background:rgba(0,0,0,0.6); z-index:1000; justify-content:center; align-items:flex-start; overflow:auto;">
  <div style="background:white; padding:20px; margin-top:60px; border-radius:10px; max-width:800px; width:90%; position:relative;">
    <span onclick="document.getElementById('khungThongKe').style.display='none';" 
      style="position:absolute; top:10px; right:20px; cursor:pointer; font-weight:bold;">&times;</span>

    <h2>Thống kê doanh thu</h2>
    <form onsubmit="loadThongKe(event)">
      Từ ngày: <input type="date" id="fromDate" required>
      Đến ngày: <input type="date" id="toDate" required>
      <button type="submit">Thống kê</button>
    </form>
    <div id="ketquaThongKe" style="margin-top:20px;"></div>
  </div>
</div>


<script>
function moThongKeDoanhThu() {
  document.getElementById("khungThongKe").style.display = "flex";
}

function loadThongKe(e) {
  e.preventDefault();
  const from = document.getElementById("fromDate").value;
  const to = document.getElementById("toDate").value;

  const xhr = new XMLHttpRequest();
  xhr.open("GET", "thongke_doanhthu.php?from=" + from + "&to=" + to, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      document.getElementById("ketquaThongKe").innerHTML = xhr.responseText;
    }
  };
  xhr.send();
}
</script>