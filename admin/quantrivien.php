<?php

// require_once(__DIR__ . '../BackEnd/ConnectionDB/DB_classes.php');
require_once('../BackEnd/ConnectionDB/DB_classes.php');
$qtv = new QuanTriBUS(); // Giả sử bạn đã tạo class QuanTriBUS tương tự SanPhamBUS
$i = 1;
$dsQuanTri = $qtv->select_all();
?>

<!-- ___________________________________________________________________________________________________________________________-->
<!-- ____________________________________________________Quản Trị Viên______________________________________________________ -->
<!--___________________________________________________________________________________________________________________________ -->

<div id="quantrivien" class="container">
    <table class="table-header hideImg">
      <thead style="position: sticky; top: 0; background-color: #fff; z-index: 2;">
        <tr>
          <th title="Sắp xếp" style="width: 5%" onclick="sortAdminTable('Ma_Admin')">Mã <i class="fa fa-sort"></i></th>
          <th title="Sắp xếp" style="width: 20%" onclick="sortAdminTable('ten')">Họ Tên <i class="fa fa-sort"></i></th>
          <th title="Sắp xếp" style="width: 15%" onclick="sortAdminTable('email')">Email <i class="fa fa-sort"></i></th>
          <th title="Sắp xếp" style="width: 20%" onclick="sortAdminTable('matkhau')">Mật khẩu <i class="fa fa-sort"></i></th>
          <th title="Sắp xếp" style="width: 15%" onclick="sortAdminTable('chucvu')">Chức Vụ <i class="fa fa-sort"></i></th>
          <th style="width: 20%">Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (!empty($dsQuanTri)) {
            foreach ($dsQuanTri as $row) {
                echo "<tr id='admin-{$row['Ma_Admin']}'>
                        <td>" . htmlspecialchars($row['Ma_Admin']) . "</td>
                        <td style='position: relative'>
                            <img class='hinhDaiDien' src='" . htmlspecialchars($row['Hinh_Anh']) . "' alt='Hình quản trị viên'>
                            " . htmlspecialchars($row['Ho_Ten']) . "
                        </td>
                        <td>" . htmlspecialchars($row['Email']) . "</td>
                        <td>" . htmlspecialchars($row['Mat_Khau']) . "</td>
                        <td>" . htmlspecialchars($row['Chuc_Vu']) . "</td>
                        <td>
                            <button class='btn edit-btn btn-success' onclick=\"
                                moModalSuaQuanTri(
                                    '" . $row['Ma_Admin'] . "',
                                    '" . addslashes($row['Ho_Ten']) . "',
                                    '" . addslashes($row['Email']) . "',
                                    '" . addslashes($row['Mat_Khau']) . "',
                                    '" . addslashes($row['Lien_Lac']) . "',
                                    '" . addslashes($row['Dia_Chi']) . "',
                                    '" . addslashes($row['Chuc_Vu']) . "',
                                    '" . addslashes($row['Gioi_Thieu']) . "',
                                    '" . addslashes($row['Hinh_Anh']) . "'
                                ); 
                                document.getElementById('khungSuaQuanTri').style.transform='scale(1)';
                            \">Sửa</button>
                            <button class='btn delete-btn' onclick=\"xoaQuanTri('{$row['Ma_Admin']}', '" . addslashes($row['Ho_Ten']) . "')\">Xóa</button>
                            <button class='btn detail-btn' onclick=\"xemChiTietQuanTri('{$row['Ma_Admin']}'); document.getElementById('khungChiTietQuanTri').style.transform='scale(1)';\">Xem Thêm</button>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='6' style='text-align: center;'>Không có quản trị viên nào.</td></tr>";
        }
        ?>
      </tbody>
    </table>

    <div class="table-footer">
        <select name="kieuTimAdmin" id="kieuTimAdmin" onchange="timKiemQuanTri()">
            <option value="ma">Tìm theo mã</option>
            <option value="ten">Tìm theo tên</option>
            <option value="email">Tìm theo email</option>
        </select>
        <input
            type="text"
            id="searchInput"
            placeholder="Tìm kiếm..."
            onkeyup="timKiemQuanTri()"
        />
        <button onclick="document.getElementById('themQuanTri').style.transform='scale(1)'; autoMaQuanTri();">
            <i class="fa fa-plus-square"></i>
            Thêm quản trị viên
        </button>
    </div>
</div>

<!-- -----------------------------------------------------Khung thêm quản trị viên--------------------------------------------------- -->

<div id="themQuanTri" class="overlay">
  <span
    class="close"
    onclick="this.parentElement.style.transform = 'scale(0)';"
    >&times;</span
  >
  <form
    method="POST"
    action="../php/them_quantri.php"
    enctype="multipart/form-data"
  >
    <table class="overlayTable table-outline table-content table-header">
      <tr>
        <th colspan="2">Thêm Quản Trị Viên</th>
      </tr>
      <style>
        #themQuanTri td {
          color: white;
        }
      </style>

      <tr>
        <td>Họ và Tên:</td>
        <td><input type="text" name="ho_ten" required /></td>
      </tr>

      <tr>
        <td>Email:</td>
        <td><input type="email" name="email" required /></td>
      </tr>

      <tr>
        <td>Mật khẩu:</td>
        <td><input type="password" name="mat_khau" required /></td>
      </tr>

      <tr>
        <td>Liên lạc (SĐT):</td>
        <td><input type="text" name="lien_lac" required /></td>
      </tr>

      <tr>
        <td>Địa chỉ:</td>
        <td><input type="text" name="dia_chi" required /></td>
      </tr>

      <tr>
        <td>Chức vụ:</td>
        <td>
          <select name="chuc_vu" required>
            <option value="">-- Chọn chức vụ --</option>
            <option value="Quản trị viên">Quản trị viên</option>
            <option value="Nhân viên">Nhân viên</option>
          </select>
        </td>
      </tr>

      <tr>
        <td>Giới thiệu:</td>
        <td><textarea name="gioi_thieu" rows="3" required></textarea></td>
      </tr>

      <tr>
        <td>Ảnh đại diện:</td>
        <td>
          <input type="file" name="hinh_anh" accept="image/*" required />
        </td>
      </tr>

      <tr>
        <td colspan="2" class="table-footer">
          <button
            type="submit"
            onclick="return confirm('Thông tin quản trị viên sẽ được lưu!')"
          >
            LƯU
          </button>
        </td>
      </tr>
    </table>
  </form>
</div>



<div id="khungSuaQuanTri" class="overlay">
  <span class="close" onclick="this.parentElement.style.transform = 'scale(0)'">&times;</span>

  <style>
    #khungSuaQuanTri td {
      color: white;
    }

    #Hinh_Anh_Cu_View {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
      display: none;
      margin-top: 5px;
    }
  </style>

  <form method="POST" action="../php/sua_quantri.php" enctype="multipart/form-data">
    <table class="overlayTable table-outline table-content table-header">
      <tr><th colspan="2">Sửa Quản Trị Viên</th></tr>

      <!-- Mã admin ẩn -->
      <input type="hidden" name="Ma_Admin" id="Ma_Admin">

      <tr>
        <td>Họ tên:</td>
        <td><input type="text" class="form-control" name="Ho_Ten" id="Ho_Ten" required></td>
      </tr>

      <tr>
        <td>Email:</td>
        <td><input type="email" class="form-control" name="Email" id="Email" required></td>
      </tr>

      <tr>
        <td>Mật khẩu:</td>
        <td><input type="text" class="form-control" name="Mat_Khau" id="Mat_Khau" required></td>
      </tr>

      <tr>
        <td>Số điện thoại:</td>
        <td><input type="text" class="form-control" name="Lien_Lac" id="Lien_Lac" required></td>
      </tr>

      <tr>
        <td>Địa chỉ:</td>
        <td><input type="text" class="form-control" name="Dia_Chi" id="Dia_Chi" required></td>
      </tr>

      <tr>
        <td>Chức vụ:</td>
        <td>
          <select class="form-control" name="Chuc_Vu" id="Chuc_Vu" required>
            <option value="">-- Chọn chức vụ --</option>
            <option value="Quản trị viên">Quản trị viên</option>
            <option value="Nhân viên">Nhân viên</option>
            <option value="Super Admin">Super Admin</option>
          </select>
        </td>
      </tr>

      <tr>
        <td>Giới thiệu:</td>
        <td><textarea class="form-control" name="Gioi_Thieu" id="Gioi_Thieu" rows="3"></textarea></td>
      </tr>

      <tr>
        <td>Ảnh đại diện mới:</td>
        <td>
          <input type="file" class="form-control" name="Hinh_Anh_Moi" accept="image/*">
          <img id="Hinh_Anh_Cu_View" alt="Ảnh hiện tại">
        </td>
      </tr>

      <tr>
        <td colspan="2" class="table-footer">
          <div class="modal-footer">
            <button type="submit" name="sbmSua" class="btn btn-primary">Lưu</button>
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('khungSuaQuanTri').style.transform = 'scale(0)'">Hủy</button>
          </div>
        </td>
      </tr>
    </table>
  </form>
</div>


<!-- Khung chi tiết quản trị viên - Phiên bản mới -->
<div id="khungChiTietQuanTri" class="overlay_quantri">
    <div class="modal-content">
        <span class="close" onclick="closeQuanTri()">&times;</span>

        <!-- Header -->
        <div class="quan-tri-header">
            <img id="HinhQuanTri" src="" alt="Hình quản trị viên">
            <h2 id="TenQuanTri"></h2>
            <p id="ChucVuQuanTri" class="chuc-vu"></p>
        </div>

        <!-- Thông tin liên hệ -->
        <div class="thong-tin-lien-he">
            <h3>Thông tin liên hệ</h3>
            <p><strong>Email:</strong> <span id="EmailQuanTri"></span></p>
            <p><strong>Liên lạc:</strong> <span id="LienLacQuanTri"></span></p>
            <p><strong>Địa chỉ:</strong> <span id="DiaChiQuanTri"></span></p>
        </div>

        <!-- Giới thiệu -->
        <div class="gioi-thieu">
            <h3>Giới thiệu</h3>
            <p id="GioiThieuQuanTri"></p>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
            <button onclick="closeQuanTri()">Đóng</button>
        </div>
    </div>
</div>

<!-- CSS -->
<style>
/* Overlay toàn màn hình */
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

/* Nội dung modal */
.modal-content {
    background-color: #fff;
    width: 400px;
    max-width: 90%;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    animation: slideDown 0.3s ease;
}

/* Hiệu ứng slide xuống */
@keyframes slideDown {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

/* Close button */
.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 25px;
    font-weight: bold;
    cursor: pointer;
    color: #555;
}

/* Header */
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

/* Thông tin liên hệ & Giới thiệu */
.thong-tin-lien-he, .gioi-thieu {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
}
.thong-tin-lien-he h3, .gioi-thieu h3 {
    margin-top: 0;
    margin-bottom: 10px;
    color: #333;
}

/* Footer */
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

<!-- JS -->
<script>
function openQuanTri() {
    document.getElementById('khungChiTietQuanTri').style.transform = 'scale(1)';
}
function closeQuanTri() {
    document.getElementById('khungChiTietQuanTri').style.transform = 'scale(0)';
}
</script>

<script>
function moModalSuaQuanTri(maAdmin, hoTen, email, matKhau, lienLac, diaChi, chucVu, gioiThieu, hinhAnh) {
    // Lấy các input trong form sửa
    const maEl = document.getElementById("Ma_Admin");
    const hoTenEl = document.getElementById("Ho_Ten");
    const emailEl = document.getElementById("Email");
    const matKhauEl = document.getElementById("Mat_Khau");
    const lienLacEl = document.getElementById("Lien_Lac");
    const diaChiEl = document.getElementById("Dia_Chi");
    const chucVuEl = document.getElementById("Chuc_Vu");
    const gioiThieuEl = document.getElementById("Gioi_Thieu");
    const hinhAnhEl = document.getElementById("Hinh_Anh_Cu_View");

    // Kiểm tra tồn tại
    if (!maEl || !hoTenEl || !emailEl || !matKhauEl || !lienLacEl || !diaChiEl || !chucVuEl || !gioiThieuEl) {
        alert("Lỗi: Form sửa quản trị viên chưa được nạp đúng.");
        return;
    }

    // Gán giá trị
    maEl.value = maAdmin;
    hoTenEl.value = hoTen;
    emailEl.value = email;
    matKhauEl.value = matKhau;
    lienLacEl.value = lienLac;
    diaChiEl.value = diaChi;
    chucVuEl.value = chucVu;
    gioiThieuEl.value = gioiThieu;

    // Hiển thị hình cũ nếu có
    if (hinhAnhEl) {
        hinhAnhEl.src = hinhAnh || "default-avatar.jpg";
        hinhAnhEl.style.display = hinhAnh ? "block" : "none";
    }

    // Hiện modal
    const modal = document.getElementById("khungSuaQuanTri");
    if (modal) modal.style.transform = "scale(1)";
}
</script>



</div>
