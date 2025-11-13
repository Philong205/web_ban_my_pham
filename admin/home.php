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
      <!-- Thanh tìm kiếm -->
      <div class="search">
        <input type="text" placeholder="Search.." />
        <button type="submit">
          <img src="../image/admin/search.png" alt="Search" />
        </button>
      </div>

      <!-- Thông tin user / Admin -->
      <div class="user">
        <a href="#" class="btn">Add New</a>
        <img src="../image/admin/notifications.png" alt="Notifications" />

        <!-- Nút avatar admin -->
        <!-- Nút avatar admin -->
        <button class="icon-case"
            onclick="
                xemChiTietQuanTri('<?php echo $_SESSION['admin']['Ma_Admin']; ?>'); 
                document.getElementById('khungChiTietQuanTri').style.transform='scale(1)';
            ">
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

 <!-- Khung thông tin Admin đang đăng nhập -->
<div id="khungThongTinAdmin" class="overlay_quantri">
    <div class="modal-content">
        <!-- Nút đóng -->
        <span class="close" onclick="document.getElementById('khungThongTinAdmin').style.transform='scale(0)'">&times;</span>

        <!-- Header -->
        <div class="quan-tri-header">
            <img id="HinhAdmin" src="../image/QuanTri/default-avatar.jpg" alt="Hình Admin">
            <h2 id="TenAdmin"></h2>
            <p id="ChucVuAdmin" class="chuc-vu"></p>
        </div>

        <!-- Thông tin liên hệ -->
        <div class="thong-tin-lien-he">
            <h3>Thông tin liên hệ</h3>
            <p><strong>Email:</strong> <span id="EmailAdmin"></span></p>
            <p><strong>Liên lạc:</strong> <span id="LienLacAdmin"></span></p>
            <p><strong>Địa chỉ:</strong> <span id="DiaChiAdmin"></span></p>
        </div>

        <!-- Giới thiệu -->
        <div class="gioi-thieu">
            <h3>Giới thiệu</h3>
            <p id="GioiThieuAdmin"></p>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
            <button onclick="document.getElementById('khungThongTinAdmin').style.transform='scale(0)'">Đóng</button>
        </div>
    </div>
</div>


<!-- CSS sử dụng giống khungChiTietQuanTri -->
<style>
.overlay_quantri {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    transform: scale(0);
    transition: transform 0.3s ease;
    z-index: 999;
}
.modal-content {
    background-color: #fff;
    width: 400px;
    max-width: 90%;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    animation: slideDown 0.3s ease;
}
@keyframes slideDown {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 25px;
    font-weight: bold;
    cursor: pointer;
    color: #555;
}
.quan-tri-header {
    text-align: center;
    padding: 25px 20px;
    border-bottom: 1px solid #eee;
}
.quan-tri-header img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
}
.quan-tri-header h2 {
    margin: 0;
    font-size: 22px;
}
.chuc-vu {
    color: #777;
    font-weight: 500;
}
.thong-tin-lien-he, .gioi-thieu {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
}
.thong-tin-lien-he h3, .gioi-thieu h3 {
    margin-top: 0;
    margin-bottom: 10px;
    color: #333;
}
.modal-footer {
    text-align: center;
    padding: 15px;
}
.modal-footer button {
    padding: 8px 20px;
    background-color: #2196F3;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.modal-footer button:hover {
    background-color: #1976D2;
}
</style>

<!-- JS hiển thị dữ liệu từ session -->
<script>
function hienThongTinAdmin(sessionAdmin) {
  if (!sessionAdmin || Object.keys(sessionAdmin).length === 0) {
    alert("Không có thông tin admin.");
    return;
  }

  // Hình đại diện
  const hinhEl = document.getElementById("HinhAdmin");
  hinhEl.src = sessionAdmin.Hinh_Anh
    ? "../image/QuanTri/" + sessionAdmin.Hinh_Anh
    : "../image/QuanTri/default-avatar.jpg";

  // Thông tin cơ bản
  document.getElementById("TenAdmin").innerText = sessionAdmin.Ho_Ten || "";
  document.getElementById("ChucVuAdmin").innerText = sessionAdmin.Chuc_Vu || "";
  document.getElementById("EmailAdmin").innerText = sessionAdmin.Email || "";
  document.getElementById("LienLacAdmin").innerText =
    sessionAdmin.Lien_Lac || "";
  document.getElementById("DiaChiAdmin").innerText = sessionAdmin.Dia_Chi || "";
  document.getElementById("GioiThieuAdmin").innerText =
    sessionAdmin.Gioi_Thieu || "";

  // Hiển thị modal
  document.getElementById("khungThongTinAdmin").style.transform = "scale(1)";
}

</script>

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