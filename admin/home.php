<?php
// Đảm bảo đã có session
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

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
                hienThongTinAdmin('<?php echo $_SESSION['admin']['Ma_Admin']; ?>'); 
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

<!-- Khung thông tin Admin -->
<div id="khungThongTinAdmin" class="overlay_quantri">
    <div class="modal-content">

        <!-- Nút đóng -->
        <span class="close" onclick="document.getElementById('khungThongTinAdmin').style.transform='scale(0)'">&times;</span>

        <!-- Header -->
        <div class="quan-tri-header">
            
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
            <button class="btn-edit" onclick="moKhungSuaAdmin()">Sửa thông tin</button>

        </div>
    </div>
</div>

<!-- Popup Sửa Thông Tin Admin -->
<div id="popupEditAdmin" class="overlay-popup" style="transform: scale(0);">
    <div class="popup-content">

        <!-- Header -->
        <div class="popup-header">
            <h2>Cập nhật thông tin quản trị</h2>
            <span class="close" onclick="document.getElementById('popupEditAdmin').style.transform='scale(0)'">&times;</span>
        </div>

        <!-- Body -->
        <div class="popup-body">
            <form id="formEditAdmin" method="POST" enctype="multipart/form-data" action="../php/update_admin_home.php">


                <div class="form-group">
                    <label>Họ tên:</label>
                    <input type="text" id="EditHoTen" name="Ho_Ten" required>
                </div>

                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" id="EditEmail" name="Email" required>
                </div>

                <div class="form-group">
                    <label>Liên lạc:</label>
                    <input type="text" id="EditLienLac" name="Lien_Lac">
                </div>

                <div class="form-group">
                    <label>Địa chỉ:</label>
                    <textarea id="EditDiaChi" name="Dia_Chi"></textarea>
                </div>

              

                <div class="form-group">
                    <label>Giới thiệu:</label>
                    <textarea id="EditGioiThieu" name="Gioi_Thieu"></textarea>
                </div>

                <!-- Footer / Submit -->
                <div class="popup-footer">
                    <button type="submit" name="CapNhat" class="btn-submit">Lưu thay đổi</button>
                    <button type="button" class="btn-cancel" onclick="document.getElementById('popupEditAdmin').style.transform='scale(0)'"">Hủy</button>
                </div>

            </form>
        </div>
    </div>
</div>
</div>


<!-- Lấy dữ liệu admin từ PHP (session) -->
<script>
    const adminSession = <?php echo json_encode($_SESSION['admin']); ?>;

    function hienThongTinAdmin() {
        if (!adminSession) {
            alert("Không tìm thấy thông tin admin!");
            return;
        }

        // Gán dữ liệu vào HTML
        document.getElementById("TenAdmin").innerText = adminSession.Ho_Ten || "";
        document.getElementById("ChucVuAdmin").innerText = adminSession.Chuc_Vu || "";
        document.getElementById("EmailAdmin").innerText = adminSession.Email || "";
        document.getElementById("LienLacAdmin").innerText = adminSession.Lien_Lac || "";
        document.getElementById("DiaChiAdmin").innerText = adminSession.Dia_Chi || "";
        document.getElementById("GioiThieuAdmin").innerText = adminSession.Gioi_Thieu || "";

        // Hiện popup
        document.getElementById("khungThongTinAdmin").style.transform = "scale(1)";
    }

    function moKhungSuaAdmin() {
    if (!adminSession) {
        alert("Không tìm thấy thông tin admin!");
        return;
    }

    document.getElementById("EditHoTen").value = adminSession.Ho_Ten || "";
    document.getElementById("EditEmail").value = adminSession.Email || "";
    document.getElementById("EditLienLac").value = adminSession.Lien_Lac || "";
    document.getElementById("EditDiaChi").value = adminSession.Dia_Chi || "";
    document.getElementById("EditGioiThieu").value = adminSession.Gioi_Thieu || "";

    document.getElementById("popupEditAdmin").style.transform = "scale(1)";
}

</script>


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