<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('../BackEnd/ConnectionDB/DB_classes.php');
$th = new ThuongHieuBUS(); // Giả sử class giống QuanTriBUS
$dsTH = $th->select_all();
?>

<div id="thuonghieu" class="container">
    <table class="table-header hideImg">
      <thead style="position: sticky; top:0; background-color:#fff; z-index:2;">
        <tr>
          <th style="width:5%">Mã TH</th>
          <th style="width:25%">Tên Thương Hiệu</th>
          <th style="width:15%">Logo</th>
          <th style="width:15%">Xuất Xứ</th>
          <th style="width:40%">Hành động</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (!empty($dsTH)) {
            foreach ($dsTH as $row) {
                echo "<tr id='th-{$row['MaTH']}'>
                        <td>{$row['MaTH']}</td>
                        <td>{$row['TenTH']}</td>
                        <td><img src='{$row['LogoTH']}' width='60'></td>
                        <td>{$row['XuatXu']}</td>
                        <td>
                            <button class='btn btn-success' onclick=\"
                                moModalSuaTH(
                                    '{$row['MaTH']}',
                                    '".addslashes($row['TenTH'])."',
                                    '".addslashes($row['LogoTH'])."',
                                    '".addslashes($row['XuatXu'])."',
                                    '".addslashes($row['Mota'])."'
                                );
                                document.getElementById('khungSuaTH').style.transform='scale(1)';
                            \">Sửa</button>

                            <button class='btn delete-btn' onclick=\"xoaTH('{$row['MaTH']}', '".addslashes($row['TenTH'])."')\">Xóa</button>

                            <button class='btn detail-btn' onclick=\"xemChiTietTH('{$row['MaTH']}'); document.getElementById('khungCTTH').style.transform='scale(1)'\">
                                Xem Thêm
                            </button>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5' style='text-align:center;'>Không có thương hiệu nào.</td></tr>";
        }
        ?>
      </tbody>
    </table>

    <div class="table-footer">
    <select name="kieuTimTH" id="kieuTimTH" onchange="timKiemTH()">
        <option value="ma">Tìm theo mã</option>
        <option value="ten">Tìm theo tên</option>
        <option value="xuatxu">Tìm theo xuất xứ</option>
    </select>
    <input type="text" id="searchTH" placeholder="Tìm kiếm..." onkeyup="timKiemTH()"/>
    <button onclick="document.getElementById('themTH').style.transform='scale(1)'; tuDongMaTH();">
        <i class="fa fa-plus-square"></i> Thêm Thương Hiệu
    </button>
</div>

</div>

<!-- ================== Thêm Thương Hiệu ================== -->
<div id="themTH" class="overlay">
  <span class="close" onclick="this.parentElement.style.transform='scale(0)'">&times;</span>
  <form method="POST" action="../php/them_thuonghieu.php" enctype="multipart/form-data">
    <table class="overlayTable table-outline table-content table-header">
      <tr><th colspan="2">Thêm Thương Hiệu</th></tr>
<style>
        #themTH td {
          color: white;
        }
      </style>
      <tr>
        <td>Tên Thương Hiệu:</td>
        <td><input type="text" name="TenTH" required></td>
      </tr>

      <tr>
        <td>Logo:</td>
        <td><input type="file" name="LogoTH" accept="image/*" required></td>
      </tr>

      <tr>
        <td>Xuất Xứ:</td>
        <td><input type="text" name="XuatXu" required></td>
      </tr>

      <tr>
        <td>Mô Tả:</td>
        <td><textarea name="Mota" rows="3" required></textarea></td>
      </tr>

      <tr>
        <td colspan="2" class="table-footer">
          <button type="submit" onclick="return confirm('Thông tin thương hiệu sẽ được lưu!')">Lưu</button>
        </td>
      </tr>
    </table>
  </form>
</div>

<!-- ================== Sửa Thương Hiệu ================== -->
<div id="khungSuaTH" class="overlay">
  <span class="close" onclick="this.parentElement.style.transform='scale(0)'">&times;</span>
  <form method="POST" action="../php/sua_thuonghieu.php" enctype="multipart/form-data">
    <table class="overlayTable table-outline table-content table-header">
      <tr><th colspan="2">Sửa Thương Hiệu</th></tr>

      <input type="hidden" id="MaTH" name="MaTH">
      <input type="hidden" id="Logo_Cu" name="Logo_Cu">

      <style>
        #khungSuaTH td {
          color: white;
        }
      </style>

      <tr>
        <td>Tên Thương Hiệu:</td>
        <td><input type="text" name="TenTH" id="TenTH" required></td>
      </tr>

      <tr>
        <td>Logo:</td>
        <td>
          <input type="file" name="LogoTH" accept="image/*">
          <img id="LogoTH_Cu_View" src="" style="width:80px;height:80px;margin-top:5px;border-radius:8px;">
        </td>
      </tr>

      <tr>
        <td>Xuất Xứ:</td>
        <td><input type="text" name="XuatXu" id="XuatXu" required></td>
      </tr>

      <tr>
        <td>Mô Tả:</td>
        <td><textarea name="Mota" id="Mota" rows="3" required></textarea></td>
      </tr>

      <tr>
        <td colspan="2" class="table-footer">
          <button type="submit">Lưu</button>
          <button type="button" onclick="document.getElementById('khungSuaTH').style.transform='scale(0)'">Hủy</button>
        </td>
      </tr>
    </table>
  </form>
</div>

<!-- Khung chi tiết Thương Hiệu - Phiên bản mới -->
<div id="khungCTTH" class="overlay_quantri">
    <div class="modal-content">
        <span class="close" onclick="closeTH()">&times;</span>

        <!-- Header -->
        <div class="quan-tri-header">
            <h2 id="TenTH_CT"></h2>
            <img id="LogoTH_CT" src="" style="width:100px; margin-bottom:10px;">
        </div>

        <!-- Thông tin chi tiết -->
        <div class="thong-tin-lien-he">
            <p><strong>Xuất Xứ:</strong> <span id="XuatXu_CT"></span></p>
            <p><strong>Mô tả:</strong> <span id="Mota_CT"></span></p>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
            <button onclick="closeTH()">Đóng</button>
        </div>
    </div>
    <!-- Khung chi tiết Thương Hiệu - Phiên bản mới -->

</div>

<script>
function closeTH() {
    document.getElementById('khungCTTH').style.transform = 'scale(0)';
}
</script>

<script>
function moModalSuaTH(Ma, Ten, Logo, XuatXu, Mota){
    document.getElementById("MaTH").value = Ma;
    document.getElementById("TenTH").value = Ten;
    document.getElementById("XuatXu").value = XuatXu;
    document.getElementById("Mota").value = Mota;
    document.getElementById("Logo_Cu").value = Logo;
    document.getElementById("LogoTH_Cu_View").src = Logo;
}

function xemChiTietTH(ma){
    fetch("../php/get_chitiet_thuonghieu.php?MaTH="+encodeURIComponent(ma))
        .then(res=>res.json())
        .then(th=>{
            document.getElementById("TenTH_CT").innerText = th.TenTH;
            document.getElementById("XuatXu_CT").innerText = th.XuatXu;
            document.getElementById("Mota_CT").innerText = th.Mota;
            document.getElementById("LogoTH_CT").src = th.LogoTH;
        });
}

function xoaTH(ma, ten){
    if(confirm('Bạn có chắc muốn xóa thương hiệu "'+ten+'" không?')){
        window.location.href="../php/xoa_thuonghieu.php?MaTH="+ma;
    }
}

function timKiemTH() {
    const input = document.getElementById('searchTH').value.toLowerCase();
    const filterBy = document.getElementById('kieuTimTH').value;
    const table = document.querySelector('#thuonghieu tbody');
    const rows = table.getElementsByTagName('tr');

    for(let i = 0; i < rows.length; i++) {
        let td;
        if(filterBy === 'ma') td = rows[i].getElementsByTagName('td')[0];
        else if(filterBy === 'ten') td = rows[i].getElementsByTagName('td')[1];
        else if(filterBy === 'xuatxu') td = rows[i].getElementsByTagName('td')[3];
        
        if(td){
            let textValue = td.textContent || td.innerText;
            rows[i].style.display = textValue.toLowerCase().indexOf(input) > -1 ? '' : 'none';
        }
    }
}

</script>
