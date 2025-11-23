<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once('../BackEnd/ConnectionDB/DB_classes.php');
$km = new KhuyenMaiBUS();
$dsKM = $km->select_all();
?>

<div id="khuyenmai" class="container">
    <table class="table-header hideImg">
        <thead style="position: sticky; top:0; background:#fff; z-index:2;">
            <tr>
                <th>Mã KM</th>
                <th>Tên KM</th>
                <th>Giá trị (%)</th>
                <th>Ngày BD</th>
                <th>Ngày KT</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(!empty($dsKM)){
            foreach($dsKM as $row){
                echo "<tr id='km-{$row['MaKM']}'>
                        <td>{$row['MaKM']}</td>
                        <td>{$row['TenKM']}</td>
                        <td>{$row['GiaTriKM']}</td>
                        <td>{$row['NgayBD']}</td>
                        <td>{$row['NgayKT']}</td>
                        <td>
                            <button class='btn btn-success' onclick=\"
                                moModalSuaKM(
                                    '{$row['MaKM']}',
                                    '".addslashes($row['TenKM'])."',
                                    '{$row['GiaTriKM']}',
                                    '{$row['NgayBD']}',
                                    '{$row['NgayKT']}'
                                );
                                document.getElementById('khungSuaKM').style.transform='scale(1)';
                            \">Sửa</button>
                            <button class='btn delete-btn' onclick=\"xoaKM('{$row['MaKM']}', '".addslashes($row['TenKM'])."')\">Xóa</button>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='6' style='text-align:center;'>Không có khuyến mãi nào.</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <div class="table-footer">
        <select id="kieuTimKM" onchange="timKiemKM()">
            <option value="ma">Tìm theo mã</option>
            <option value="ten">Tìm theo tên</option>
        </select>
        <input type="text" id="searchKM" placeholder="Tìm kiếm..." onkeyup="timKiemKM()"/>
        <button onclick="document.getElementById('themKM').style.transform='scale(1)'; autoMaKM();">
            <i class="fa fa-plus-square"></i> Thêm Khuyến Mãi
        </button>
    </div>
</div>

<!-- Thêm Khuyến Mãi -->
<div id="themKM" class="overlay">
  <span class="close" onclick="this.parentElement.style.transform='scale(0)'">&times;</span>
  <form method="POST" action="../php/them_khuyenmai.php">
    <table class="overlayTable table-outline table-content table-header">
      <tr><th colspan="2">Thêm Khuyến Mãi</th></tr>
      <tr><td>Tên KM:</td><td><input type="text" name="TenKM" required></td></tr>
      <tr><td>Giá trị (%):</td><td><input type="number" name="GiaTriKM" required></td></tr>
      <tr><td>Ngày BD:</td><td><input type="datetime-local" name="NgayBD" required></td></tr>
      <tr><td>Ngày KT:</td><td><input type="datetime-local" name="NgayKT" required></td></tr>
      <tr><td colspan="2" class="table-footer">
          <button type="submit" onclick="return confirm('Thông tin KM sẽ được lưu!')">Lưu</button>
      </td></tr>
    </table>
  </form>
</div>

<!-- Sửa Khuyến Mãi -->
<div id="khungSuaKM" class="overlay">
  <span class="close" onclick="this.parentElement.style.transform='scale(0)'">&times;</span>
  <form method="POST" action="../php/sua_khuyenmai.php">
    <table class="overlayTable table-outline table-content table-header">
      <tr><th colspan="2">Sửa Khuyến Mãi</th></tr>
      <input type="hidden" id="MaKM" name="MaKM">
      <tr><td>Tên KM:</td><td><input type="text" id="TenKM" name="TenKM" required></td></tr>
      <tr><td>Giá trị (%):</td><td><input type="number" id="GiaTriKM" name="GiaTriKM" required></td></tr>
      <tr><td>Ngày BD:</td><td><input type="datetime-local" id="NgayBD" name="NgayBD" required></td></tr>
      <tr><td>Ngày KT:</td><td><input type="datetime-local" id="NgayKT" name="NgayKT" required></td></tr>
      <tr><td colspan="2" class="table-footer">
          <button type="submit">Lưu</button>
          <button type="button" onclick="document.getElementById('khungSuaKM').style.transform='scale(0)'">Hủy</button>
      </td></tr>
    </table>
  </form>
</div>

<script>
function closeKM(){ document.getElementById('khungCTKM').style.transform='scale(0)'; }

function moModalSuaKM(Ma, Ten, GiaTri, NgayBD, NgayKT){
    document.getElementById("MaKM").value=Ma;
    document.getElementById("TenKM").value=Ten;
    document.getElementById("GiaTriKM").value=GiaTri;
    document.getElementById("NgayBD").value=NgayBD;
    document.getElementById("NgayKT").value=NgayKT;
}

function xoaKM(ma, ten){
    if(confirm('Bạn có chắc muốn xóa KM "'+ten+'" không?')){
        window.location.href="../php/xoa_khuyenmai.php?MaKM="+ma;
    }
}

function timKiemKM(){
    const input = document.getElementById('searchKM').value.toLowerCase();
    const filterBy = document.getElementById('kieuTimKM').value;
    const table = document.querySelector('#khuyenmai tbody');
    const rows = table.getElementsByTagName('tr');
    for(let i=0;i<rows.length;i++){
        let td;
        if(filterBy==='ma') td=rows[i].getElementsByTagName('td')[0];
        else td=rows[i].getElementsByTagName('td')[1];
        if(td){
            let textValue = td.textContent||td.innerText;
            rows[i].style.display = textValue.toLowerCase().indexOf(input)>-1?'':'none';
        }
    }
}
</script>
