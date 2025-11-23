<?php
if(session_status()===PHP_SESSION_NONE) session_start();
require_once('../BackEnd/ConnectionDB/DB_classes.php');
$loai = new LoaiSanPhamBUS();
$dsLoai = $loai->select_all();
?>

<div id="loaisanpham" class="container">
    <table class="table-header hideImg">
        <thead style="position: sticky; top:0; background:#fff; z-index:2;">
            <tr>
                <th>Mã Loại</th>
                <th>Tên Loại</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(!empty($dsLoai)){
            foreach($dsLoai as $row){
                echo "<tr id='loai-{$row['MaLoai']}'>
                        <td>{$row['MaLoai']}</td>
                        <td>{$row['TenLoai']}</td>
                        <td>
                            <button class='btn btn-success' onclick=\"moModalSuaLoai('{$row['MaLoai']}','".addslashes($row['TenLoai'])."'); document.getElementById('khungSuaLoai').style.transform='scale(1)';\">Sửa</button>
                            <button class='btn delete-btn' onclick=\"xoaLoai('{$row['MaLoai']}','".addslashes($row['TenLoai'])."')\">Xóa</button>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='3' style='text-align:center;'>Không có loại sản phẩm nào.</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <div class="table-footer">
        <input type="text" id="searchLoai" placeholder="Tìm kiếm..." onkeyup="timKiemLoai()"/>
        <button onclick="document.getElementById('themLoai').style.transform='scale(1)'; autoMaLoai();">
            <i class="fa fa-plus-square"></i> Thêm Loại SP
        </button>
    </div>
</div>

<!-- Thêm Loại -->
<div id="themLoai" class="overlay">
  <span class="close" onclick="this.parentElement.style.transform='scale(0)'">&times;</span>
  <form method="POST" action="../php/them_loaisp.php">
    <table class="overlayTable table-outline table-content table-header">
      <tr><th colspan="2">Thêm Loại SP</th></tr>
      <tr><td>Tên Loại:</td><td><input type="text" name="TenLoai" required></td></tr>
      <tr><td colspan="2" class="table-footer">
          <button type="submit" onclick="return confirm('Thông tin loại SP sẽ được lưu!')">Lưu</button>
      </td></tr>
    </table>
  </form>
</div>

<!-- Sửa Loại -->
<div id="khungSuaLoai" class="overlay">
  <span class="close" onclick="this.parentElement.style.transform='scale(0)'">&times;</span>
  <form method="POST" action="../php/sua_loaisp.php">
    <table class="overlayTable table-outline table-content table-header">
      <tr><th colspan="2">Sửa Loại SP</th></tr>
      <input type="hidden" id="MaLoai" name="MaLoai">
      <tr><td>Tên Loại:</td><td><input type="text" id="TenLoai" name="TenLoai" required></td></tr>
      <tr><td colspan="2" class="table-footer">
          <button type="submit">Lưu</button>
          <button type="button" onclick="document.getElementById('khungSuaLoai').style.transform='scale(0)'">Hủy</button>
      </td></tr>
    </table>
  </form>
</div>

<script>
function moModalSuaLoai(Ma, Ten){ document.getElementById("MaLoai").value=Ma; document.getElementById("TenLoai").value=Ten; }
function xoaLoai(Ma, Ten){ if(confirm('Bạn có chắc muốn xóa loại "'+Ten+'" không?')) window.location.href="../php/xoa_loaisp.php?MaLoai="+Ma; }
function timKiemLoai(){
    const input=document.getElementById('searchLoai').value.toLowerCase();
    const table=document.querySelector('#loaisanpham tbody');
    const rows=table.getElementsByTagName('tr');
    for(let i=0;i<rows.length;i++){
        const td=rows[i].getElementsByTagName('td')[1];
        if(td){
            const textValue=td.textContent||td.innerText;
            rows[i].style.display=textValue.toLowerCase().indexOf(input)>-1?'':'none';
        }
    }
}
</script>
