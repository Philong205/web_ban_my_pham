<?php
?>
<!-- ___________________________________________________________________________________________________________________________-->
<!-- ____________________________________________________Khách Hàng_____________________________________________________________ -->
<!--___________________________________________________________________________________________________________________________ -->

<div id="khachHang" class="container">
        <table class="table-header">
          <thead>
            <tr>
              <!-- Theo độ rộng của table content -->
              <th title="Sắp xếp" style="width: 7%">
                Stt <i class="fa fa-sort"></i>
              </th>
              <th title="Sắp xếp" style="width: 17%">
                Họ tên <i class="fa fa-sort"></i>
              </th>
              <th title="Sắp xếp" style="width: 22%">
                Email <i class="fa fa-sort"></i>
              </th>
              <th title="Sắp xếp" style="width: 15%">
                Tài khoản <i class="fa fa-sort"></i>
              </th>
              <th title="Sắp xếp" style="width: 12%">
                Mật khẩu <i class="fa fa-sort"></i>
              </th>
              <th style="width: 17%">Hành động <i class="fa fa-sort"></i></th>
            </tr>
          </thead>
          <tbody id="dsNguoiDung">
          <?php include "..\php\danhsach_nguoidung.php"; ?>
          </tbody>
        </table>




        <div class="table-footer">
        <select name="kieuTimKhachHang" id="kieuTimKhachHang">
          <option value="ten">Tìm theo họ tên</option>
          <option value="email">Tìm theo email</option>
          <option value="taikhoan">Tìm theo tài khoản</option>
        </select>
          <input
            type="text"
            placeholder="Tìm kiếm..."
            onkeyup="timKiemKhachHang()"
          />
          <button
            onclick="document.getElementById('themTK').style.transform = 'scale(1)'; autoMaSanPham()">
            <i class="fa fa-plus-square"></i> Thêm người dùng
          </button>
        </div>
</div>
       

 <div id="themTK" class="overlay">
  <span
    class="close"
    onclick="this.parentElement.style.transform = 'scale(0)';"
    >&times;</span
  >
      <form method="POST" action="..\php\them_nguoidung.php">
        <table class="overlayTable table-outline table-content table-header">
          <tr>
            <th colspan="2">Thêm người dùng</th>
          </tr>
          <style>
            #themTK td {
              color: white;
            }
          </style>
          <tr>
            <td style="color: white">Họ Tên:</td>
            <td><input type="text" name="tennd" required /></td>
          </tr>
          <tr>
            <td style="color: white">Email:</td>
            <td><input type="email" name="email" required /></td>
          </tr>
          <tr>
            <td style="color: white">Tên tài khoản:</td>
            <td><input type="text" name="taikhoan" required /></td>
          </tr>
          <tr>
            <td style="color: white">Mật Khẩu:</td>
            <td><input type="password" name="matkhau" required /></td>
          </tr>
          <tr>
            <td style="color: white">Địa Chỉ:</td>
            <td><input type="text" name="diachi" required /></td>
          </tr>
          <tr>
            <td style="color: white">Số điện thoại:</td>
            <td><input type="text" name="sdt" required /></td>
          </tr>
          <tr>
            <td colspan="2" class="table-footer">
              <button type="submit" onclick="return confirm('Thông tin đã được lưu!')">
                LƯU
              </button>
            </td>
          </tr>
        </table>
      </form>
    </div>


        <!-- HTML Form -->
<div id="khungSuaNguoiDung" class="overlay">
    <span class="close" onclick="this.parentElement.style.transform = 'scale(0)'">&times;</span>

    <style>
        #khungSuaNguoiDung td {
            color: white;
        }
    </style>

    <form method="POST" action="..\php\sua_nguoidung.php">
        <table class="overlayTable table-outline table-content table-header">
            <tr><th colspan="2">Sửa Người Dùng</th></tr>

            <input type="hidden" name="MaND" id="MaND_sua">

            <tr>
                <td>Họ tên:</td>
                <td><input type="text" class="form-control" name="TenND" id="TenND_sua" required></td>
            </tr>
            <tr>
                <td>Tên đăng nhập:</td>
                <td><input type="text" class="form-control" name="TaiKhoan" id="TaiKhoan_sua" required></td>
            </tr>
            <tr>
                <td>Mật khẩu:</td>
                <td><input type="text" class="form-control" name="MatKhau" id="MatKhau_sua" required></td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><input type="email" class="form-control" name="Email" id="Email_sua" required></td>
            </tr>
            <tr>
                <td>SĐT:</td>
                <td><input type="text" class="form-control" name="SDT" id="SDT_sua" required></td>
            </tr>
            <tr>
                <td>Địa chỉ:</td>
                <td><input type="text" class="form-control" name="DiaChi" id="DiaChi_sua" required></td>
            </tr>

            <tr>
                <td colspan="2" class="table-footer">
                    <div class="modal-footer">
                        <button type="submit" name="sbmSua" class="btn btn-primary">Lưu</button>
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('khungSuaNguoiDung').style.transform = 'scale(0)'">Hủy</button>
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>



</div>
      <!-- // khach hang -->
    </div>
    <!-- // main -->
    <script>
      function timKiemNguoiDung(input) {
        var query = input.value;
        var type = document.getElementById("kieuTimKhachHang").value;

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "..\php\timkiem_nguoidung.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
          if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById("dsNguoiDung").innerHTML = xhr.responseText;
          }
        };
        xhr.send("query=" + encodeURIComponent(query) + "&type=" + encodeURIComponent(type));
      }
    </script>
           


<script>
function moModalSua(ma, ten, email, taikhoan, matkhau, sdt, diachi) {
    // Kiểm tra xem phần tử có tồn tại trước khi gán
    const maND = document.getElementById("MaND_sua"); 
    const tenND = document.getElementById("TenND_sua");
    const emailND = document.getElementById("Email_sua");
    const tkND = document.getElementById("TaiKhoan_sua");
    const mkND = document.getElementById("MatKhau_sua");
    const sdtND = document.getElementById("SDT_sua");
    const dcND = document.getElementById("DiaChi_sua");

    if (!maND || !tenND || !emailND || !tkND || !mkND || !sdtND || !dcND) {
        alert("Lỗi: Modal sửa người dùng chưa được nạp đúng.");
        return;
    }

    maND.value = ma;
    tenND.value = ten;
    emailND.value = email;
    tkND.value = taikhoan;
    mkND.value = matkhau;
    sdtND.value = sdt;
    dcND.value = diachi;

    document.getElementById("khungSuaNguoiDung").style.transform = "scale(1)";
    modal.show();
}
</script>
