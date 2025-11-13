
<?php


// require_once(__DIR__ . '/../../BackEnd/ConnectionDB/DB_classes.php');
require_once('../BackEnd/ConnectionDB/DB_classes.php');
$sp = new SanPhamBUS();
$i = 1;
$dsSanPham = $sp->select_all();
?>
<!-- ___________________________________________________________________________________________________________________________-->
<!-- ____________________________________________________Sản Phẩm_____________________________________________________________ -->
<!--___________________________________________________________________________________________________________________________ -->

        <div id="sanPham" class="container">
            <table class="table-header hideImg">
              <thead style = "position: sticky; top: 0;">
                <tr>
                  <th title="Sắp xếp" style="width: 5%" onclick="sortProductsTable('stt')">Stt <i class="fa fa-sort"></i></th>
                  <th title="Sắp xếp" style="width: 10%" onclick="sortProductsTable('masp')">Mã <i class="fa fa-sort"></i></th>
                  <th title="Sắp xếp" style="width: 40%" onclick="sortProductsTable('ten')">Tên <i class="fa fa-sort"></i></th>
                  <th title="Sắp xếp" style="width: 10%" onclick="sortProductsTable('gia')">Khuyến mãi <i class="fa fa-sort"></i></th>
                  <th title="Sắp xếp" style="width: 10%" onclick="sortProductsTable('khuyenmai')">Giá <i class="fa fa-sort"></i></th>
                  <th title="Sắp xếp" style="width: 10%" onclick="sortProductsTable('soluong')">Số Lượng <i class="fa fa-sort"></i></th>
                  <th style="width: 15%">Hành động</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // require_once('../BackEnd/ConnectionDB/DB_classes.php');
                // require_once(__DIR__ . '../BackEnd/ConnectionDB/DB_classes.php');
                require_once('../BackEnd/ConnectionDB/DB_classes.php');

                $sp = new SanPhamBUS();
                $i = 1;
                $dsSanPham = $sp->select_all();

                if (!empty($dsSanPham)) {
                    foreach ($dsSanPham as $row) {
                      if ($row['TrangThai'] != 0){
                        echo "<tr id='product-{$row['MaSP']}'>
                            <td>" . $i++ . "</td>
                            <td>" . $row['MaSP'] . "</td>
                            <td style='position: relative'>
                                <img class='hinhDaiDien' src='" . htmlspecialchars($row['HinhAnh']) . "' alt='Hình sản phẩm'>
                                <a title='Xem chi tiết' target='_blank' href='../user/detail.php?MaSP=" . urlencode($row['MaSP']) . "'>" . htmlspecialchars($row['TenSP']) . "</a>
                            </td>
                            <td>" . ($row['MaKM'] > 0 && isset($row['GiaTriKM']) ? $row['GiaTriKM'] . "%" : "Không KM") . "</td>
                            <td>" . number_format($row['GiaSP'], 0, ',', '.') . "đ</td>
                            <td>" . $row['SoLuong'] . "</td>
                            <td>
                                <button class='btn edit-btn' onclick=\"suaSanPham('{$row['MaSP']}'); document.getElementById('khungSuaSanPham').style.transform = 'scale(1)'; autoMaSanPham();\">Sửa</button>
                                <button class='btn delete-btn' onclick=\"xoaSanPham('{$row['MaSP']}', '{$row['TenSP']}')\">Xóa</button>
                            </td>
                        </tr>";
                      }
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align: center;'>Không có sản phẩm nào.</td></tr>";
                }
                ?>
              </tbody>
            </table>

            <div class="table-footer">
    <select name="kieuTimSanPham" id="kieuTimSanPham" onchange="timKiemSanPham()">
        <option value="ma">Tìm theo mã</option>
        <option value="ten">Tìm theo tên</option>
    </select>
    <input
        type="text"
        id="searchInput"
        placeholder="Tìm kiếm..."
        onkeyup="timKiemSanPham()"
    />
    <button onclick="document.getElementById('khungThemSanPham').style.transform = 'scale(1)'; autoMaSanPham()">
        <i class="fa fa-plus-square"></i>
        Thêm sản phẩm
    </button>
</div>




<!-- -----------------------------------------------------Khung sửa sản phẩm--------------------------------------------------- -->


<div id="khungSuaSanPham" class="overlay">
    <span class="close" onclick="this.parentElement.style.transform = 'scale(0)';">&times;</span>


<?php

// require_once(__DIR__ . '../../BackEnd/ConnectionDB/DB_classes.php');
require_once('../BackEnd/ConnectionDB/DB_classes.php');


$connect = Database::getConnection();
$spBUS = new SanPhamBUS();
$kmBUS = new KhuyenMaiBUS();
$thBUS = new ThuongHieuBUS();
$lspBUS = new LoaiSanPhamBUS();


if (isset($_POST['sbmSua'])) {
  $MaSP = $_POST['MaSP'];
  $TenSP = $_POST['TenSP'];
  $MaLoai = $_POST['LoaiSanPham'];
  $MaTH = $_POST['ThuongHieu'];
  $XuatXu = $_POST['XuatXu'];
  $SoLuong = $_POST['SoLuong'];
  $DungTich = $_POST['DungTich'];
  $LoaiDa = $_POST['LoaiDa'];
  $GiaSP = $_POST['GiaSP'];
  $MaKM = $_POST['KhuyenMai'];
  $TPChinh = $_POST['TPChinh'];
  $TPFull = $_POST['TPFull'];
  $MoTaSP = $_POST['MoTaSP'];
  $TrangThai = 1;

    // Lấy thông tin khuyến mãi
    $GiaTriKM = $_POST['GiaTriKM'] ?? 0;
    $TenKM = $_POST['TenKM'] ?? '';
    if ((!$TenKM || !$GiaTriKM) && $MaKM && is_numeric($MaKM)) {
        $km = $kmBUS->select_by_id('*', $MaKM);
        if ($km) {
            $GiaTriKM = $km['GiaTriKM'];
            $TenKM = $km['TenKM'];
        }
    }

    // Lấy thông tin loại sản phẩm
    $TenLoai = $_POST['TenLoai'] ?? '';
    if (!$TenLoai && $MaLoai&& is_numeric($MaLoai)) {
        $lsp = $lspBUS->select_by_id('*', $MaLoai);
        if ($lsp) {
            $TenLoai = $lsp['TenLoai'];
        }
    }

    // Lấy thông tin thương hiệu
    $TenTH = $_POST['TenTH'] ?? '';
    if (!$TenTH && $MaTH && is_numeric($MaTH)) {
        $th = $thBUS->select_by_id('*', $MaTH);
        if ($th) {
            $TenTH = $th['TenTH'];
        }
    }

    // Xử lý ảnh
    if (isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error'] === UPLOAD_ERR_OK && $_FILES['hinhanh']['name'] != '') {
        $ten_file = time() . '_' . basename($_FILES['hinhanh']['name']);
        $hinhanh_tmp = $_FILES['hinhanh']['tmp_name'];
        $hinhanh = '../image/admin/SanPham/' . $ten_file;
        move_uploaded_file($hinhanh_tmp, $hinhanh);
    } else {
        $hinhanh = $_POST['hinhDaiDienOld'] ?? '';
    }

    // Chuẩn bị mảng dữ liệu để cập nhật
    $sqlUpdate = "UPDATE sanpham SET 
        TenSP = '$TenSP',
        MaTH = '$MaTH',
        TenTH = '$TenTH',
        MaLoai = '$MaLoai',
        TenLoai = '$TenLoai',
        GiaSP = $GiaSP,
        hinhanh = '$hinhanh',
        XuatXu = '$XuatXu',
        SoLuong = $SoLuong,
        DungTich = '$DungTich',
        LoaiDa = '$LoaiDa',
        MaKM = $MaKM,
        TenKM = '$TenKM',
        GiaTriKM = '$GiaTriKM',
        TPChinh = '$TPChinh',
        TPFull = '$TPFull',
        MoTaSP = '$MoTaSP',
        TrangThai = $TrangThai
        WHERE MaSP = '$MaSP'";

    if (mysqli_query($connect, $sqlUpdate)) {
        echo "<script>alert('Cập nhật sản phẩm thành công!');
              window.location.href = 'admin.php?page=sanpham';
        </script>";
    } else {
        echo "<script>alert('Cập nhật thất bại: " . mysqli_error($connect) . "');
          window.location.href = 'admin.php?page=sanpham';
        </script>";
    }

    exit();
}

?>

<!-- Form sửa sản phẩm -->
<form method="POST" enctype="multipart/form-data">
    <table class="overlayTable table-outline table-content table-header hideImg">
        <tr><th colspan="2">Sửa Sản Phẩm</th></tr>

        <style>
            #khungSuaSanPham td {
                color: white;
            }
        </style>

        <tr><td>Mã sản phẩm:</td>
            <td><input type="text" name="MaSP" id="MaSP" value="<?php echo $sanpham['MaSP'] ?? ''; ?>" readonly></td>
        </tr>

        <tr><td>Tên sản phẩm:</td>
            <td><input type="text" name="TenSP" id="TenSP" value="<?php echo $sanpham['TenSP'] ?? ''; ?>"></td>
        </tr>
          <!-- Loại sản phẩm -->
        <tr><td>Loại sản phẩm:</td>
            <td>
                <select name="LoaiSanPham" id="LoaiSanPham">
                    <?php
                    // Lấy tất cả loại sản phẩm từ cơ sở dữ liệu
                    foreach ($lspBUS->select_all() as $lsp) {
                        // Kiểm tra xem mã loại sản phẩm hiện tại có trùng với sản phẩm không
                        $selected = ($lsp['MaLoai'] == ($sanpham['MaLoai'] ?? '')) ? "selected" : "";
                        echo "<option value='{$lsp['MaLoai']}' $selected data-tenloai='{$lsp['TenLoai']}'>
                                {$lsp['TenLoai']}
                            </option>";

                    }
                    ?>
                </select>
                <input type="hidden" id="TenLoai" name="TenLoai" value="">
            </td>
        </tr>
        <!-- Thương hiệu -->

        <tr><td>Thương hiệu:</td>
            <td>
                <select name="ThuongHieu" id="ThuongHieu">
                    <?php
                    // Lấy tất cả thương hiệu từ cơ sở dữ liệu
                    foreach ($thBUS->select_all() as $th) {
                        // Kiểm tra xem mã thương hiệu hiện tại có trùng với sản phẩm không
                        $selected = ($th['MaTH'] == ($sanpham['MaTH'] ?? '')) ? "selected" : "";
                        echo "<option value='{$th['MaTH']}' $selected data-tenth='{$th['TenTH']}'>
                                {$th['TenTH']}
                            </option>";
                    }
                    ?>
                </select>
                <input type="hidden" id="TenTH" name="TenTH" value="">
            </td>
        </tr>


        <tr><td>Xuất xứ:</td>
            <td><input type="text" name="XuatXu" id="XuatXu" value="<?php echo $sanpham['XuatXu'] ?? ''; ?>"></td>
        </tr>

        <tr><td>Số lượng:</td>
            <td><input type="number" name="SoLuong" id="SoLuong" value="<?php echo $sanpham['SoLuong'] ?? 0; ?>"></td>
        </tr>

        <tr><td>Dung tích:</td>
            <td><input type="text" name="DungTich" id="DungTich" value="<?php echo $sanpham['DungTich'] ?? ''; ?>"></td>
        </tr>

        <tr><td>Loại da:</td>
            <td><input type="text" name="LoaiDa" id="LoaiDa" value="<?php echo $sanpham['LoaiDa'] ?? ''; ?>"></td>
        </tr>

        <tr>
            <td>Hình ảnh hiện tại:</td>
            <td style="position: relative">
                <img class="hinhDaiDien" id="hinhDaiDienOld" src=""/>
                <a id="linkAnhCu" href="#" target="_blank" class="mauTrang" style="text-decoration: underline;">Xem ảnh gốc</a>
                <input type="hidden" id ="hinhDaiDienInputHidden" name="hinhDaiDienOld" value="<?php echo $sanpham['hinhanh'] ?? ''; ?>">
            </td>
        </tr>

        <tr>
            <td>Chọn ảnh mới:</td>
            <td style="position: relative">
                <input type="file" name="hinhanh" accept="image/*" onchange="xemTruocAnhSua(this)">
                <img class="hinhDaiDien" id="anhXemTruocSua" src="" style="left: 200px;">
            </td>
        </tr>

        <tr><td>Giá sản phẩm:</td>
            <td><input type="text" name="GiaSP" id="GiaSP" value="<?php echo $sanpham['GiaSP'] ?? ''; ?>"></td>
        </tr>

        <tr><td>Khuyến mãi:</td>
            <td>
                <select name="KhuyenMai" id="KhuyenMai">
                    <?php
                    foreach ($kmBUS->select_all() as $km) {
                      // Kiểm tra xem mã khuyến mãi hiện tại có trùng với sản phẩm không
                      $selected = ($km['MaKM'] == ($sanpham['MaKM'] ?? '')) ? "selected" : "";
                      echo "<option value='{$km['MaKM']}' $selected data-tenkm='{$km['TenKM']}' data-giatrikm='{$km['GiaTriKM']}'>
                              {$km['TenKM']} ({$km['GiaTriKM']}%)
                            </option>";
                  }
                    ?>
                </select>
                <input type="hidden" name="TenKM" id="TenKM">
                <input type="hidden" name="GiaTriKM" id="GiaTriKM">
            </td>
        </tr>

        <tr><td>Thành phần chính:</td>
            <td><input type="text" name="TPChinh" id="TPChinh" value="<?php echo $sanpham['TPChinh'] ?? ''; ?>"></td>
        </tr>

        <tr><td>Thành phần đầy đủ:</td>
            <td><textarea name="TPFull" id="TPFull" rows="4"><?php echo $sanpham['TPFull'] ?? ''; ?></textarea></td>
        </tr>

        <tr><td>Mô tả sản phẩm:</td>
            <td><textarea name="MoTaSP" id="MoTaSP" rows="4"><?php echo $sanpham['MoTaSP'] ?? ''; ?></textarea></td>
        </tr>

        <tr>
            <td colspan="2" class="table-footer">
                <button type="submit" name="sbmSua">LƯU THAY ĐỔI</button>
            </td>
        </tr>
    </table>
</form>
</div>





<!-- -----------------------------------------------------Khung thêm sản phẩm--------------------------------------------------- -->

        <div id="khungThemSanPham" class="overlay">
          <span
            class="close"
            onclick="this.parentElement.style.transform = 'scale(0)';"
            >&times;</span
          >
              <!--Code php thêm sản phẩm-->
          <?php 
            // require_once(__DIR__ . '/../../BackEnd/ConnectionDB/DB_classes.php');
            require_once('../BackEnd/ConnectionDB/DB_classes.php');


            $connect = Database::getConnection();

            $spBUS = new SanPhamBUS();
            $kmBUS = new KhuyenMaiBUS();
            $thBUS = new ThuongHieuBUS();
            $lspBUS = new LoaiSanPhamBUS();


            if(isset($_POST['sbm'])){
              // Tự động gán MaSP bằng số lượng thương hiệu hiện tại + 1
              $result = mysqli_query($connect, "SELECT COUNT(*) as total FROM sanpham");
              $row = mysqli_fetch_assoc($result);
              $MaSP = $row['total'] + 1;

              $TenSP = $_POST['TenSP'];
              $MaLoai = $_POST['loaisanpham'];
              $MaTH = $_POST['thuonghieu'];
              $XuatXu = $_POST['XuatXu'];
              $SoLuong = $_POST['SoLuong'];
              $DungTich = $_POST['DungTich'];
              $LoaiDa = $_POST['LoaiDa'];
              $ten_file = $_FILES['hinhanh']['name'];
              $hinhanh_tmp = $_FILES['hinhanh']['tmp_name'];
              $hinhanh = '../image/admin/SanPham/' . $ten_file; // Thêm đường dẫn vào trước
              $GiaSP = $_POST['GiaSP'];
              $MaKM = $_POST['KhuyenMai'];
              $TPChinh = $_POST['TPChinh'];
              $TPFull = $_POST['TPFull'];
              $MoTaSP = $_POST['MoTaSP'];
              $TrangThai = 1; 

              // Lấy thông tin khuyến mãi
              $GiaTriKM = $_POST['GiaTriKM'] ?? 0;
              $TenKM = $_POST['TenKM'] ?? '';
              if ((!$TenKM || !$GiaTriKM) && $MaKM && is_numeric($MaKM)) {
                  $km = $kmBUS->select_by_id('*', $MaKM);
                  if ($km) {
                      $GiaTriKM = $km['GiaTriKM'];
                      $TenKM = $km['TenKM'];
                  }
              }

              // Lấy thông tin loại sản phẩm
              $TenLoai = $_POST['TenLoai'] ?? '';
              if (!$TenLoai && $MaLoai&& is_numeric($MaLoai)) {
                  $lsp = $lspBUS->select_by_id('*', $MaLoai);
                  if ($lsp) {
                      $TenLoai = $lsp['TenLoai'];
                  }
              }

              // Lấy thông tin thương hiệu
              $TenTH = $_POST['TenTH'] ?? '';
              if (!$TenTH && $MaTH && is_numeric($MaTH)) {
                  $th = $thBUS->select_by_id('*', $MaTH);
                  if ($th) {
                      $TenTH = $th['TenTH'];
                  }
              }

              $checkQuery = "SELECT MaSP FROM sanpham WHERE MaSP = '$MaSP'";
              $checkResult = mysqli_query($connect, $checkQuery);

              if (mysqli_num_rows($checkResult) > 0) {
                  echo "<script>alert('Mã sản phẩm đã tồn tại. Vui lòng chọn mã khác.');</script>";
              } else {
                $sql = "INSERT INTO sanpham (
                  MaSP, TenSP, MaTH, TenTH, MaLoai, TenLoai, GiaSP, hinhanh, XuatXu, SoLuong, DungTich,
                  LoaiDa, MaKM, TenKM, GiaTriKM, TPChinh, TPFull, MoTaSP, TrangThai
              ) VALUES (
                  '$MaSP', '$TenSP', '$MaTH', '$TenTH', '$MaLoai', '$TenLoai', $GiaSP, '$hinhanh', '$XuatXu', $SoLuong, '$DungTich',
                  '$LoaiDa', $MaKM, '$TenKM', '$GiaTriKM', '$TPChinh', '$TPFull', '$MoTaSP', $TrangThai
              )";

                  if (mysqli_query($connect, $sql)) {
                      if (move_uploaded_file($hinhanh_tmp, '../image/admin/SanPham/' . $ten_file)) {
                          echo "<script>alert('Thêm sản phẩm và ảnh thành công!');
                                        window.location.href = 'admin.php?page=sanpham';
                          </script>";
                      } else {
                          echo "<script>alert('Thêm sản phẩm thành công, nhưng lưu ảnh thất bại!');
                                        window.location.href = 'admin.php?page=sanpham';
                          </script>";
                      }
                  } else {
                      echo "<script>alert('Thêm sản phẩm thất bại: " . mysqli_error($connect) . "');
                                    window.location.href = 'admin.php?page=sanpham';
                      </script>";
                  }
              }
              exit();
            }
            ?>

          <form method="POST" enctype="multipart/form-data">
            <table class="overlayTable table-outline table-content table-header hideImg">
              <tr>
                <th colspan="2">Thêm Sản Phẩm</th>
              </tr>
              <style>
                #khungThemSanPham td {
                  color: white;
                }
              </style>
              <tr>
                <td>Tên sản phẩm:</td>
                <td><input type="text" name="TenSP" require/></td>
              </tr>
              <tr>
                <td>Loại sản phẩm:</td>
                <td>
                    <select name="loaisanpham" onchange="autoMaSanPham(this.value)">
                    <option value=""> -- Select --</option>
                        <?php
                        foreach( $lspBUS->select_all() as $rowname1 => $row1){ ?>
                          <option value="<?php echo $row1['MaLoai'];?>"><?php echo $row1['TenLoai'];?></option>
                        <?php } ?>
                    </select>
                    <input type="hidden" id="TenLoai" name="TenLoai" value="">

                    <button type="button" onclick="document.getElementById('khungThemLoai').style.transform = 'scale(1)';">
                        <i class="fa fa-plus-square"></i>
                        Thêm loại SP
                    </button>
                </td>
              </tr>

              <tr>
                <td>Thương hiệu:</td>
                <td>
                    <select name="thuonghieu" onchange="autoMaSanPham(this.value)">
                    <option value=""> -- Select -- </option>
                        <?php
                        foreach( $thBUS->select_all() as $rowname2 => $row2){ ?>
                          <option value="<?php echo $row2['MaTH'];?>"><?php echo $row2['TenTH'];?></option>
                        <?php } ?>
                    </select>
                    <input type="hidden" id="TenTH" name="TenTH" value="">

                    <button type="button" onclick="document.getElementById('khungThemThuongHieu').style.transform = 'scale(1)';">
                        <i class="fa fa-plus-square"></i>
                        Thêm thương hiệu
                    </button>
                </td>
              </tr>

              <tr>
                <td>Xuất xứ thương hiệu:</td>
                <td><input type="text" name= "XuatXu" require/></td>
              </tr>
              <tr>
                <td>Số lượng sản phẩm:</td>
                <td><input type="text" name="SoLuong" require/></td>
              </tr>
              <tr>
                <td>Dung tích:</td>
                <td><input type="text" name="DungTich" require/></td>
              </tr>
              <tr>
                <td>Loại da:</td>
                <td><input type="text" name="LoaiDa" require/></td>
              </tr>

              <tr>
                  <td>Chọn ảnh:</td>
                  <td style="position: relative">
                      <input type="file" name="hinhanh" accept="image/*" onchange="xemTruocAnhThem(this)">
                      <img class="hinhDaiDien" id="anhXemTruocThem" src="" style="left: 200px;">
                      <input style="display: none;" type="text" id="hinhanh" value="">
                  </td>
              </tr>

              <tr>
                <td>Giá tiền:</td>
                <td><input type="text" name="GiaSP" require/></td>
              </tr>
              <tr>
                <td>Khuyến mãi:</td>
                <td><select name="KhuyenMai">
                <option value=""> -- Select -- </option>
                  <?php
                    foreach( $kmBUS->select_all() as $rowname3 => $row3){ ?>
                    <option value="<?php echo $row3['MaKM'];?>"><?php echo $row3['TenKM'] . '( ' . $row3['GiaTriKM'] . '% )';?></option>
                  <?php } ?>
                </select>
                <input type="hidden" name="TenKM" id="TenKM">
                <input type="hidden" name="GiaTriKM" id="GiaTriKM">

                <button type="button" onclick="document.getElementById('khungThemKM').style.transform = 'scale(1)';">
                    <i class="fa fa-plus-square"></i>
                    Thêm khuyến mãi
                </button>
              </td>
              </tr>
              <tr>
                <td>Thành phần chính:</td>
                <td>
                  <textarea
                    id="thanhphanchinh"
                    name="TPChinh"
                    row="5"
                    cols="40"
                    placeholder="Nhập thành phần chính"
                    require
                  ></textarea>
                </td>
              </tr>
              <tr>
                <td>Thành phần đầy đủ:</td>
                <td>
                  <textarea
                    id="thanhphandaydu"
                    name="TPFull"
                    row="5"
                    cols="40"
                    placeholder="Nhập thành phần đầy đủ"
                    require
                  ></textarea>
                </td>
              </tr>
              <tr>
                <td>Mô tả sản phẩm:</td>
                <td>
                  <textarea
                    id="moTaSanPham"
                    name="MoTaSP"
                    rows="5"
                    cols="40"
                    placeholder="Nhập mô tả sản phẩm"
                    require
                  ></textarea>
                </td>
              </tr>
              <tr>
                <td colspan="2" class="table-footer">
                  <button name="sbm" type="submit">
                    THÊM
                  </button>
                </td>
              </tr>
            </table>
          </form>
        </div>


<!-- -----------------------------------------------------Khung thêm Loại sản phẩm--------------------------------------------------- -->

<div id="khungThemLoai" class="overlay">
          <span
            class="close"
            onclick="this.parentElement.style.transform = 'scale(0)';"
            >&times;</span
          >
              <!--Code php thêm sản phẩm-->
          <?php 
            // require_once(__DIR__ . '/../../BackEnd/ConnectionDB/DB_classes.php');
            require_once('../BackEnd/ConnectionDB/DB_classes.php');

            $connect = Database::getConnection();

            $thBUS = new LoaiSanPhamBUS();


            if(isset($_POST['sbm3'])){
              // Tự động gán MaTH bằng số lượng thương hiệu hiện tại + 1
              $result = mysqli_query($connect, "SELECT COUNT(*) as total3 FROM loaisanpham");
              $row = mysqli_fetch_assoc($result);
              $MaLoai = $row['total3'] + 1;

              $TenLoai = $_POST['TenLoai'];


              $checkQuery = "SELECT MaLoai FROM loaisanpham WHERE MaLoai = '$MaLoai'";
              $checkResult = mysqli_query($connect, $checkQuery);

              if (mysqli_num_rows($checkResult) > 0) {
                  echo "<script>alert('Mã loại đã tồn tại. Vui lòng chọn mã khác.');</script>";
              } else {
                $sql = "INSERT INTO loaisanpham (
                  MaLoai, TenLoai
              ) VALUES (
                  '$MaLoai', '$TenLoai'
              )";

                  if (mysqli_query($connect, $sql)) {
                      echo "<script>alert('Thêm khuyến mãi thành công!');
                                    window.location.href = 'admin.php?page=sanpham';
                      </script>";
                  } else {
                      echo "<script>alert('Thêm khuyến mãi thất bại: " . mysqli_error($connect) . "');
                                    window.location.href = 'admin.php?page=sanpham';
                      </script>";
                  }
              }
              exit();
            }
            ?>

          <form method="POST" enctype="multipart/form-data">
            <table class="overlayTable table-outline table-content table-header hideImg">
              <tr>
                <th colspan="2">Thêm Khuyến Mãi</th>
              </tr>
              <style>
                #khungThemLoai td {
                  color: white;
                }
              </style>
              <tr>
                <td>Loại sản phẩm :</td>
                <td><input type="text" name="TenLoai" require/></td>
              </tr>

                <td colspan="2" class="table-footer">
                  <button name="sbm3" type="submit">
                    THÊM LOẠI SẢN PHẨM
                  </button>
                </td>
              </tr>
            </table>
          </form>
        </div>
<!-- -----------------------------------------------------Khung thêm khuyến mãi--------------------------------------------------- -->

<div id="khungThemKM" class="overlay">
          <span
            class="close"
            onclick="this.parentElement.style.transform = 'scale(0)';"
            >&times;</span
          >
              <!--Code php thêm sản phẩm-->
          <?php 
            // require_once(__DIR__ . '/../../BackEnd/ConnectionDB/DB_classes.php');
            require_once('../BackEnd/ConnectionDB/DB_classes.php');


            $connect = Database::getConnection();

            $thBUS = new ThuongHieuBUS();


            if(isset($_POST['sbm2'])){
              // Tự động gán MaTH bằng số lượng thương hiệu hiện tại + 1
              $result = mysqli_query($connect, "SELECT COUNT(*) as total1 FROM khuyenmai");
              $row = mysqli_fetch_assoc($result);
              $MaKM = $row['total1'] + 1;

              $TenKM = $_POST['TenKM'];
              $GiaTriKM = $_POST['GiaTriKM'];
              $NgayBD = date('Y-m-d H:i:s', strtotime($_POST['NgayBD']));
              $NgayKT = date('Y-m-d H:i:s', strtotime($_POST['NgayKT']));

              $checkQuery = "SELECT MaKM FROM khuyenmai WHERE MaKM = '$MaKM'";
              $checkResult = mysqli_query($connect, $checkQuery);

              if (mysqli_num_rows($checkResult) > 0) {
                  echo "<script>alert('Mã khuyến mãi đã tồn tại. Vui lòng chọn mã khác.');</script>";
              } else {
                $sql = "INSERT INTO khuyenmai (
                  MaKM, TenKM, GiaTriKM, NgayBD, NgayKT
              ) VALUES (
                  '$MaKM', '$TenKM', '$GiaTriKM', '$NgayBD', '$NgayKT'
              )";

                  if (mysqli_query($connect, $sql)) {
                      echo "<script>alert('Thêm khuyến mãi thành công!');
                                    window.location.href = 'admin.php?page=sanpham';
                      </script>";
                  } else {
                      echo "<script>alert('Thêm khuyến mãi thất bại: " . mysqli_error($connect) . "');
                                    window.location.href = 'admin.php?page=sanpham';
                      </script>";
                  }
              }
              exit();
            }
            ?>

          <form method="POST" enctype="multipart/form-data">
            <table class="overlayTable table-outline table-content table-header hideImg">
              <tr>
                <th colspan="2">Thêm Khuyến Mãi</th>
              </tr>
              <style>
                #khungThemKM td {
                  color: white;
                }
              </style>
              <tr>
                <td>Tên khuyến mãi:</td>
                <td><input type="text" name="TenKM" require/></td>
              </tr>

              <tr>
                <td>Phần trăm khuyến mãi:</td>
                <td><input type="text" name="GiaTriKM" require/></td>
              </tr>

              <tr>
                <td>Thời gian bắt đầu:</td>
                <td><input type="datetime-local" name="NgayBD" required/></td>
              </tr>

              <tr>
                <td>Thời gian kết thúc:</td>
                <td><input type="datetime-local" name="NgayKT" required/></td>
              </tr>

                <td colspan="2" class="table-footer">
                  <button name="sbm2" type="submit">
                    THÊM KHUYẾN MÃI
                  </button>
                </td>
              </tr>
            </table>
          </form>
        </div>
<!-- -----------------------------------------------------Khung thêm thương hiệu--------------------------------------------------- -->

<div id="khungThemThuongHieu" class="overlay">
          <span
            class="close"
            onclick="this.parentElement.style.transform = 'scale(0)';"
            >&times;</span
          >
              <!--Code php thêm sản phẩm-->
          <?php 
            // require_once(__DIR__ . '/../../BackEnd/ConnectionDB/DB_classes.php');
            require_once('../BackEnd/ConnectionDB/DB_classes.php');


            $connect = Database::getConnection();

            $thBUS = new ThuongHieuBUS();



            if(isset($_POST['sbm1'])){
              // Tự động gán MaTH bằng số lượng thương hiệu hiện tại + 1
              $result = mysqli_query($connect, "SELECT COUNT(*) as total2 FROM thuonghieu");
              $row = mysqli_fetch_assoc($result);
              $MaTH = $row['total2'] + 1;

              $TenTH = $_POST['TenTH'];
              $ten_fileTH = $_FILES['hinhanhTH']['name'];
              $hinhanh_tmpTH = $_FILES['hinhanhTH']['tmp_name'];
              $hinhanhTH = '../image/admin/ThuongHieu/' . $ten_fileTH;
              $XuatXuTH = $_POST['XuatXuTH'];
              $MoTaTH = $_POST['MoTaTH'];

              $sql = "INSERT INTO thuonghieu (
                MaTH, TenTH, LogoTH, XuatXu, Mota
              ) VALUES (
                '$MaTH', '$TenTH', '$hinhanhTH', '$XuatXuTH', '$MoTaTH'
              )";

              if (mysqli_query($connect, $sql)) {
                if (move_uploaded_file($hinhanh_tmpTH, '../image/admin/ThuongHieu/' . $ten_fileTH)) {
                  echo "<script>alert('Thêm thương hiệu và ảnh thành công!');
                        window.location.href = 'admin.php?page=sanpham';</script>";
                } else {
                  echo "<script>alert('Thêm thương hiệu thành công, nhưng lưu ảnh thất bại!');
                        window.location.href = 'admin.php?page=sanpham';</script>";
                }
              } else {
                echo "<script>alert('Thêm thương hiệu thất bại: " . mysqli_error($connect) . "');
                      window.location.href = 'admin.php?page=sanpham';</script>";
              }
              exit();
            }

            ?>

          <form method="POST" enctype="multipart/form-data">
            <table class="overlayTable table-outline table-content table-header hideImg">
              <tr>
                <th colspan="2">Thêm Thương Hiệu</th>
              </tr>
              <style>
                #khungThemThuongHieu td {
                  color: white;
                }
              </style>
              <tr>
                <td>Tên thương hiệu:</td>
                <td><input type="text" name="TenTH" require/></td>
              </tr>

              <tr>
                  <td>Chọn ảnh thương hiệu:</td>
                  <td style="position: relative">
                      <input type="file" name="hinhanhTH" accept="image/*" onchange="xemTruocAnhThemTH(this)">
                      <img class="hinhDaiDien" id="anhXemTruocThemTH" src="" style="left: 200px;">
                      <input style="display: none;" type="text" id="hinhanhTH" value="">
                  </td>
              </tr>

              <tr>
                <td>Xuất xứ thương hiệu:</td>
                <td><input type="text" name= "XuatXuTH" require/></td>
              </tr>

              <tr>
                <td>Mô tả thương hiệu :</td>
                <td>
                  <textarea
                    id="moTaSanPhamTH"
                    name="MoTaTH"
                    rows="5"
                    cols="40"
                    placeholder="Nhập mô tả thương hiệu..."
                    require
                  ></textarea>
                </td>
              </tr>
              <tr>
                <td colspan="2" class="table-footer">
                  <button name="sbm1" type="submit">
                    THÊM THƯƠNG HIỆU
                  </button>
                </td>
              </tr>
            </table>
          </form>
        </div>
      </div>
      <!-- // sanpham -->
