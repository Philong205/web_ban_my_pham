var TONGTIEN = 0;

window.onload = function () {
  document.getElementById("btnDangXuat").onclick = function () {
    checkDangXuat(() => {
      window.location.href = "login.php";
    });
  };

  getCurrentUser(
    (user) => {
      if (user != null && user.MaQuyen != 1) {
        addEventChangeTab();
        addThongKe();
        openTab("Home");
      }
      // else {
      //         document.body.innerHTML = `<h1 style="color:red; with:100%; text-align:center; margin: 50px;"> Truy cập bị từ chối.. </h1>`;
      //     }
    }
    // (e)=> {
    //     document.body.innerHTML = `<h1 style="color:red; with:100%; text-align:center; margin: 50px;"> Truy cập bị từ chối.. </h1>`;
    // }
  );
};

function addChart(id, chartOption) {
  var ctx = document.getElementById(id).getContext("2d");
  var chart = new Chart(ctx, chartOption);
}

// <!--___________________________________________________________________________________________________________________________-->
// <!-- ____________________________________________________Sản Phẩm_____________________________________________________________ -->
// <!--___________________________________________________________________________________________________________________________-->

//----------------------------------------------------------Tìm kiếm-----------------------------------------------------------------
function timKiemSanPham() {
  // Lấy giá trị từ dropdown và input tìm kiếm
  var kieuTim = document.getElementById("kieuTimSanPham").value;
  var searchValue = document.getElementById("searchInput").value.toLowerCase();

  // Lấy tất cả các hàng sản phẩm trong bảng
  var rows = document.querySelectorAll("#sanPham tbody tr");

  rows.forEach(function (row) {
    var cellValue = "";

    // Kiểm tra loại tìm kiếm (mã sản phẩm hoặc tên sản phẩm)
    if (kieuTim === "ma") {
      cellValue = row.querySelector("td:nth-child(2)").textContent; // Mã sản phẩm
    } else if (kieuTim === "ten") {
      cellValue = row
        .querySelector("td:nth-child(3)")
        .textContent.toLowerCase(); // Tên sản phẩm
    }

    // Kiểm tra nếu giá trị tìm kiếm có khớp
    if (cellValue.toLowerCase().includes(searchValue)) {
      row.style.display = ""; // Hiển thị dòng nếu có khớp
    } else {
      row.style.display = "none"; // Ẩn dòng nếu không khớp
    }
  });
}

//----------------------------------------------------------Sắp xếp sản phẩm ------------------------------------------------------

// Hàm QuickSort đơn giản để sắp xếp các dòng
function quickSort(arr, left, right, loai, getValue) {
  var i = left,
    j = right;
  var pivot = getValue(arr[Math.floor((left + right) / 2)], loai); // Chọn phần tử pivot giữa

  while (i <= j) {
    while (getValue(arr[i], loai) < pivot) i++; // Tìm phần tử lớn hơn hoặc bằng pivot
    while (getValue(arr[j], loai) > pivot) j--; // Tìm phần tử nhỏ hơn hoặc bằng pivot

    if (i <= j) {
      // Hoán đổi hai phần tử
      var temp = arr[i];
      arr[i] = arr[j];
      arr[j] = temp;

      i++;
      j--;
    }
  }

  // Tiếp tục sắp xếp phần bên trái và bên phải của pivot
  if (left < j) quickSort(arr, left, j, loai, getValue);
  if (i < right) quickSort(arr, i, right, loai, getValue);
}

// Hàm sắp xếp bảng sản phẩm
function sortProductsTable(loai) {
  var tbody = document
    .getElementById("sanPham")
    .getElementsByTagName("tbody")[0]; // Lấy phần thân bảng (tbody)
  var tr = Array.from(tbody.getElementsByTagName("tr")); // Chuyển danh sách các dòng thành mảng để dễ dàng sắp xếp

  // Sắp xếp các dòng dựa trên cột đã chọn (loai)
  quickSort(tr, 0, tr.length - 1, loai, getValueOfTypeInTable_SanPham);

  // Thêm các dòng đã sắp xếp vào lại tbody
  tr.forEach(function (row) {
    tbody.appendChild(row);
  });

  decrease = !decrease; // Đảo chiều sắp xếp nếu cần
}

// Lấy giá trị từ cột cụ thể (loai)
function getValueOfTypeInTable_SanPham(tr, loai) {
  var td = tr.getElementsByTagName("td");
  switch (loai) {
    case "stt":
      return Number(td[0].innerHTML); // Cột 1: Stt
    case "masp":
      return Number(td[1].innerHTML); // Cột 2: Mã sản phẩm
    case "ten":
      return td[2].getElementsByTagName("a")[0].innerHTML.toLowerCase(); // Cột 3: Tên sản phẩm (lấy từ thẻ a)
    case "gia":
      // Cột 5: Giá (xử lý loại bỏ ký tự không phải số, như dấu chấm và "đ")
      return parseInt(td[4].innerHTML.replace(/\D/g, "")); // Loại bỏ tất cả ký tự không phải là số (chấm, "đ", ...)
    case "khuyenmai":
      // Cột 4: Khuyến mãi (xử lý bỏ dấu "%" và chuyển thành số)
      // Loại bỏ dấu % và các ký tự không phải số, sau đó chuyển thành số nguyên
      var khuyenMai = td[3].innerHTML.replace("%", "").replace(/\D/g, "");
      return parseInt(khuyenMai); // Trả về phần trăm là một số nguyên
    case "soluong":
      return Number(td[5].innerHTML); // Cột 6: Số lượng
  }
  return false;
}
//----------------------------------------------------------Thêm sản phẩm-----------------------------------------------------------------
function xemTruocAnhThem(input) {
  const img = document.getElementById("anhXemTruocThem");

  if (input.files && input.files[0]) {
    const reader = new FileReader();

    reader.onload = function (e) {
      img.src = e.target.result;
      img.style.display = "block"; // Hiện ảnh ra
    };

    reader.readAsDataURL(input.files[0]);
  } else {
    img.src = "";
    img.style.display = "none"; // Ẩn ảnh nếu không có file
  }
}

//----------------------------------------------------------Thêm Thương thiệu-----------------------------------------------------------------
function xemTruocAnhThemTH(input) {
  const img = document.getElementById("anhXemTruocThemTH");

  if (input.files && input.files[0]) {
    const reader = new FileReader();

    reader.onload = function (e) {
      img.src = e.target.result;
      img.style.display = "block"; // Hiện ảnh ra
    };

    reader.readAsDataURL(input.files[0]);
  } else {
    img.src = "";
    img.style.display = "none"; // Ẩn ảnh nếu không có file
  }
}

//----------------------------------------------------------Sửa sản phẩm-----------------------------------------------------------------
function suaSanPham(MaSP) {
  fetch("../php/get_sanpham.php?MaSP=" + encodeURIComponent(MaSP))
    .then((response) => {
      if (!response.ok) throw new Error("Lỗi kết nối đến máy chủ");
      return response.json();
    })
    .then((sanpham) => {
      if (sanpham.error) {
        alert(sanpham.error);
        return;
      }

      // Gán giá trị vào form
      document.getElementById("MaSP").value = sanpham.MaSP || "";
      document.getElementById("TenSP").value = sanpham.TenSP || "";
      document.getElementById("GiaSP").value = sanpham.GiaSP || "";
      document.getElementById("LoaiSanPham").value = sanpham.MaLoai || "";
      document.getElementById("ThuongHieu").value = sanpham.MaTH || "";
      document.getElementById("XuatXu").value = sanpham.XuatXu || "";
      document.getElementById("SoLuong").value = sanpham.SoLuong || 0;
      document.getElementById("DungTich").value = sanpham.DungTich || "";
      document.getElementById("LoaiDa").value = sanpham.LoaiDa || "";
      document.getElementById("KhuyenMai").value = sanpham.MaKM || "";
      document.getElementById("TPChinh").value = sanpham.TPChinh || "";
      document.getElementById("TPFull").value = sanpham.TPFull || "";
      document.getElementById("MoTaSP").value = sanpham.MoTaSP || "";
      document.getElementById("hinhDaiDienInputHidden").value =
        sanpham.HinhAnh || "";

      // Cập nhật hình ảnh
      const hinhDaiDienOld = document.getElementById("hinhDaiDienOld");
      const linkAnhCu = document.getElementById("linkAnhCu");

      if (sanpham.HinhAnh) {
        const duongDan = decodeURIComponent(sanpham.HinhAnh);
        hinhDaiDienOld.src = duongDan;
        hinhDaiDienOld.style.display = "block";
        linkAnhCu.href = duongDan;
        linkAnhCu.style.display = "inline-block";
      } else {
        hinhDaiDienOld.style.display = "none";
        linkAnhCu.style.display = "none";
      }

      // Cập nhật khuyến mãi
      const selectKM = document.getElementById("KhuyenMai");
      const selectedOptionKM = selectKM.querySelector(
        `option[value="${sanpham.MaKM}"]`
      );

      if (selectedOptionKM) {
        document.getElementById("TenKM").value =
          selectedOptionKM.dataset.tenkm || "";
        document.getElementById("GiaTriKM").value =
          selectedOptionKM.dataset.giatrikm || "";
        selectKM.value = sanpham.MaKM;
      } else {
        document.getElementById("TenKM").value = "";
        document.getElementById("GiaTriKM").value = "";
      }

      // Cập nhật loại sản phẩm (TenLoai)
      const selectLSP = document.getElementById("LoaiSanPham");
      const selectedOptionLSP = selectLSP.querySelector(
        `option[value="${sanpham.MaLoai}"]`
      );

      if (selectedOptionLSP) {
        document.getElementById("TenLoai").value =
          selectedOptionLSP.dataset.tenloai || "";
        selectLSP.value = sanpham.MaLoai;
      } else {
        document.getElementById("TenLoai").value = "";
      }

      // Cập nhật thương hiệu (TenTH)
      const selectTH = document.getElementById("ThuongHieu");
      const selectedOptionTH = selectTH.querySelector(
        `option[value="${sanpham.MaTH}"]`
      );

      if (selectedOptionTH) {
        document.getElementById("TenTH").value =
          selectedOptionTH.dataset.tenth || "";
        selectTH.value = sanpham.MaTH;
      } else {
        document.getElementById("TenTH").value = "";
      }

      // Hiện form chỉnh sửa
      document.getElementById("khungSuaSanPham").style.transform = "scale(1)";
    })
    .catch((error) => {
      alert("Đã xảy ra lỗi: " + error.message);
    });
}

document.addEventListener("DOMContentLoaded", function () {
  const khuyenMaiSelect = document.getElementById("KhuyenMai");
  if (khuyenMaiSelect) {
    khuyenMaiSelect.addEventListener("change", function () {
      const selected = this.options[this.selectedIndex];
      const TenKMInput = document.getElementById("TenKM");
      const GiaTriKMInput = document.getElementById("GiaTriKM");
      if (TenKMInput) TenKMInput.value = selected.dataset.tenkm || "";
      if (GiaTriKMInput) GiaTriKMInput.value = selected.dataset.giatrikm || "";
    });
  }

  // Lắng nghe sự kiện change của select LoaiSanPham
  // Lắng nghe sự kiện change của select LoaiSanPham
  const loaiSanPhamSelect = document.getElementById("LoaiSanPham");

  // Kiểm tra xem phần tử LoaiSanPham có tồn tại không
  if (loaiSanPhamSelect) {
    loaiSanPhamSelect.addEventListener("change", function () {
      const selectedOption = this.options[this.selectedIndex];
      const TenLoaiInput = document.getElementById("TenLoai");

      // Kiểm tra xem phần tử TenLoai có tồn tại không trước khi gán giá trị
      if (TenLoaiInput) {
        TenLoaiInput.value = selectedOption.dataset.tenloai || ""; // Cập nhật TenLoai từ data-tenloai
      } else {
        console.error("Không tìm thấy phần tử input TenLoai");
      }
    });
  } else {
    console.error("Không tìm thấy phần tử select LoaiSanPham");
  }

  const thuongHieuSelect = document.getElementById("ThuongHieu");
  if (thuongHieuSelect) {
    thuongHieuSelect.addEventListener("change", function () {
      const selected = this.options[this.selectedIndex];
      const TenTHInput = document.getElementById("TenTH");
      if (TenTHInput) TenTHInput.value = selected.dataset.tenth || "";
    });
  }
});

function xemTruocAnhSua(input) {
  const img = document.getElementById("anhXemTruocSua");

  if (input.files && input.files[0]) {
    const reader = new FileReader();

    reader.onload = function (e) {
      img.src = e.target.result;
      img.style.display = "block"; // Hiện ảnh ra
    };

    reader.readAsDataURL(input.files[0]);
  } else {
    img.src = "";
    img.style.display = "none"; // Ẩn ảnh nếu không có file
  }
}

//----------------------------------------------------------Xóa sản phẩm-----------------------------------------------------------------
function xoaSanPham(maSP, tenSP) {
  console.log("Xóa sản phẩm:", maSP, tenSP);
  fetch(`../php/Xoa_sp.php?action=check&maSP=${encodeURIComponent(maSP)}`)
    .then((response) => response.json())
    .then((data) => {
      console.log("Dữ liệu trả về từ server:", data); // Kiểm tra dữ liệu trả về
      if (data.success) {
        if (data.sold) {
          if (
            confirm("Sản phẩm đã được bán, không thể xóa. Sản phẩm sẽ được ẩn.")
          ) {
            hideProduct(maSP);
          }
        } else {
          if (confirm(`Bạn có chắc chắn muốn xóa sản phẩm "${tenSP}"?`)) {
            deleteProduct(maSP);
          }
        }
      } else {
        alert("Lỗi kiểm tra sản phẩm: " + (data.message || "Không xác định"));
      }
    })
    .catch((error) => {
      console.error("Lỗi khi kiểm tra sản phẩm:", error);
      alert("Đã xảy ra lỗi khi kiểm tra sản phẩm!");
    });
}

function hideProduct(maSP) {
  fetch(
    `../php/Xoa_sp.php?action=update&maSP=${encodeURIComponent(maSP)}&status=0` // status = 0 để ẩn
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert("Sản phẩm đã được ẩn.");
        const productRow = document.querySelector(`#product-${maSP}`);
        if (productRow) productRow.style.display = "none"; // hoặc update lại giao diện
      } else {
        alert(
          "Không thể ẩn sản phẩm: " + (data.message || "Lỗi không xác định")
        );
      }
    })
    .catch((error) => {
      console.error("Lỗi khi ẩn sản phẩm:", error);
      alert("Đã xảy ra lỗi khi ẩn sản phẩm!");
    });
}

function deleteProduct(maSP) {
  fetch(`../php/Xoa_sp.php?action=delete&maSP=${encodeURIComponent(maSP)}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert("Sản phẩm đã được xóa.");
        const productRow = document.querySelector(`#product-${maSP}`);
        if (productRow) productRow.remove();
      } else {
        alert(
          "Không thể xóa sản phẩm: " + (data.message || "Lỗi không xác định")
        );
      }
    })
    .catch((error) => {
      console.error("Lỗi khi xóa sản phẩm:", error);
      alert("Đã xảy ra lỗi khi xóa sản phẩm!");
    });
}
// <!--___________________________________________________________________________________________________________________________-->
// <!-- ____________________________________________________Đơn hàng_____________________________________________________________ -->
// <!--___________________________________________________________________________________________________________________________-->

//----------------------------------------------------------Khung Hóa đơn-----------------------------------------------------------------

// function hienThiThongTinHoaDon(MaHD) {
//   fetch("../php/get_hoadon.php?MaHD=" + encodeURIComponent(MaHD))
//     .then((response) => response.json())
//     .then((hoadon) => {
//       if (hoadon.error) {
//         alert("Lỗi: " + hoadon.error);
//         return;
//       }

//       document.getElementById("HoaDon").value = hoadon.MaHD || "";
//       document.getElementById("TrangThaiDisplay").innerText =
//         hoadon.TrangThai || "";
//       document.getElementById("NguoiNhan").innerText = hoadon.NguoiNhan || "";
//       document.getElementById("DiaChi").innerText = hoadon.DiaChi || "";
//       document.getElementById("NgayLap").innerText = hoadon.NgayLap || "";
//       document.getElementById("TongTien").innerText =
//         Number(hoadon.TongTien).toLocaleString() + " VNĐ";

//       // Gán MaTT vào dropdown
//       const select = document.getElementById("updateTrangthai");
//       if (hoadon.MaTT) {
//         select.value = hoadon.MaTT;
//         document.getElementById("TrangThaiHidden").value =
//           select.options[select.selectedIndex].text;
//       }
//     })
//     .catch((error) => {
//       console.error("Lỗi khi lấy dữ liệu hóa đơn:", error);
//       alert("Không thể lấy chi tiết hóa đơn.");
//     });
// }

function hienThiThongTinHoaDon(MaHD) {
  fetch("../php/get_hoadon.php?MaHD=" + encodeURIComponent(MaHD))
    .then((response) => response.json())
    .then((hoadon) => {
      if (hoadon.error) {
        alert("Lỗi: " + hoadon.error);
        return;
      }

      document.getElementById("HoaDon").value = hoadon.MaHD || "";
      document.getElementById("TrangThaiDisplay").innerText =
        hoadon.TrangThai || "";
      document.getElementById("NguoiNhan").innerText = hoadon.NguoiNhan || "";
      document.getElementById("DiaChi").innerText = hoadon.DiaChi || "";
      document.getElementById("NgayLap").innerText = hoadon.NgayLap || "";
      document.getElementById("TongTien").innerText =
        Number(hoadon.TongTien).toLocaleString() + " VNĐ";

      // Gán MaTT vào dropdown (ép kiểu thành string)
      const select = document.getElementById("updateTrangthai");
      if (hoadon.MaTT != null) {
        select.value = String(hoadon.MaTT); // ép kiểu để chọn đúng option
        document.getElementById("TrangThaiHidden").value =
          select.options[select.selectedIndex].text;
      }
    })
    .catch((error) => {
      console.error("Lỗi khi lấy dữ liệu hóa đơn:", error);
      alert("Không thể lấy chi tiết hóa đơn.");
    });
}

function chitietHoaDon(MaHD) {
  fetch("../php/get_chitiethoadon.php?MaHD=" + encodeURIComponent(MaHD))
    .then((res) => res.json())
    .then((ds) => {
      const tbody = document.getElementById("danhSachSanPham");
      tbody.innerHTML = "";
      if (ds.length > 0) {
        ds.forEach((sp, i) => {
          const row = `<tr>
              <td>${i + 1}</td>
              <td>${sp.TenSP}</td>
              <td>${sp.SoLuong}</td>
              <td>${Number(sp.DonGia).toLocaleString()}</td>
              <td>${Number(sp.ThanhTien).toLocaleString()}</td>
            </tr>`;
          tbody.innerHTML += row;
        });
      } else {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;color:red;">Không có sản phẩm nào</td></tr>`;
      }
      hienThiThongTinHoaDon(MaHD);
      document.getElementById("khungHoaDon").style.display = "block";
    })
    .catch((error) => {
      console.error("Lỗi khi lấy chi tiết sản phẩm:", error);
      alert("Không thể lấy danh sách sản phẩm.");
    });
}

//----------------------------------------------------------Khung Update trạng thái-----------------------------------------------------------------

// function updateTrangThai(MaHD) {
//   hienThiThongTinHoaDon(MaHD);
//   document.getElementById("khungUpdateTrangThai").style.display = "block";
// }

// // Khi người dùng thay đổi dropdown, cập nhật tên trạng thái vào hidden input
// document.addEventListener("DOMContentLoaded", function () {
//   const select = document.getElementById("updateTrangthai");
//   select.addEventListener("change", function () {
//     const selectedText = select.options[select.selectedIndex].text;
//     document.getElementById("TrangThaiHidden").value = selectedText;
//   });
// });
function updateTrangThai(MaHD) {
  hienThiThongTinHoaDon(MaHD);
  const khung = document.getElementById("khungUpdateTrangThai");
  khung.style.transform = "scale(1)";
  khung.style.transition = "transform 0.3s";
}

// Khi người dùng thay đổi dropdown, cập nhật hidden input
document.addEventListener("DOMContentLoaded", function () {
  const select = document.getElementById("updateTrangthai");
  select.addEventListener("change", function () {
    document.getElementById("TrangThaiHidden").value =
      select.options[select.selectedIndex].text;
  });
});

//---------------------------------------------------------Lọc hóa đơn theo ngày-----------------------------------------------------------------

function locDonHangTheoKhoangNgay() {
  const fromDate = document.getElementById("fromDate").value;
  const toDate = document.getElementById("toDate").value;

  if (!fromDate || !toDate) {
    alert("Vui lòng chọn cả Từ ngày và Đến ngày.");
    return;
  }

  const url = new URL(window.location.href);
  url.searchParams.set("fromDate", fromDate);
  url.searchParams.set("toDate", toDate);
  window.location.href = url.toString();
}

//---------------------------------------------------------Tìm kiếm hóa đơn-----------------------------------------------------------------

function timKiemDonHang() {
  // Lấy giá trị từ dropdown và input tìm kiếm
  var kieuTim = document.getElementById("kieuTimDonHang").value;
  var searchValue = document
    .getElementById("searchDonHang")
    .value.toLowerCase();

  // Lấy tất cả các hàng trong bảng đơn hàng
  var rows = document.querySelectorAll("#donHang tbody tr");

  rows.forEach(function (row) {
    var cellValue = "";

    // Kiểm tra tìm kiếm theo loại (mã đơn, khách hàng, địa chỉ, trạng thái)
    if (kieuTim === "ma") {
      cellValue = row.querySelector("td:nth-child(2)").textContent; // Mã đơn
    } else if (kieuTim === "khach") {
      cellValue = row
        .querySelector("td:nth-child(3)")
        .textContent.toLowerCase(); // Tên khách
    } else if (kieuTim === "diachi") {
      // Tìm kiếm trong phần địa chỉ (quận hoặc thành phố)
      cellValue = row
        .querySelector("td:nth-child(5)")
        .textContent.toLowerCase(); // Địa chỉ
    } else if (kieuTim === "trangthai") {
      cellValue = row
        .querySelector("td:nth-child(8)")
        .textContent.toLowerCase(); // Trạng thái
    }

    // Kiểm tra nếu giá trị tìm kiếm có khớp
    if (cellValue.includes(searchValue)) {
      row.style.display = ""; // Hiển thị dòng nếu có khớp
    } else {
      row.style.display = "none"; // Ẩn dòng nếu không khớp
    }
  });
}

// Hàm hiển thị chi tiết quản trị viên
function xemChiTietQuanTri(MaAdmin) {
  fetch("../php/get_chitietquantri.php?MaAdmin=" + encodeURIComponent(MaAdmin))
    .then((res) => res.json())
    .then((admin) => {
      if (!admin || Object.keys(admin).length === 0) {
        alert("Không tìm thấy quản trị viên.");
        return;
      }

      document.getElementById("TenQuanTri").innerText = admin.Ho_Ten || "";
      document.getElementById("ChucVuQuanTri").innerText = admin.Chuc_Vu || "";
      document.getElementById("EmailQuanTri").innerText = admin.Email || "";
      document.getElementById("LienLacQuanTri").innerText =
        admin.Lien_Lac || "";
      document.getElementById("DiaChiQuanTri").innerText = admin.Dia_Chi || "";
      document.getElementById("GioiThieuQuanTri").innerText =
        admin.Gioi_Thieu || "";

      // Bổ sung hiển thị Lương
      const luongEl = document.getElementById("LuongQuanTri");
      if (luongEl) {
        const luong = admin.Luong ? Number(admin.Luong) : 0;
        // Hiển thị định dạng tiền VNĐ
        luongEl.innerText = new Intl.NumberFormat("vi-VN", {
          style: "currency",
          currency: "VND",
        }).format(luong);
      }

      document.getElementById("khungChiTietQuanTri").style.transform =
        "scale(1)";
    })
    .catch((error) => {
      console.error("Lỗi khi lấy chi tiết quản trị viên:", error);
      alert("Không thể lấy thông tin quản trị viên.");
    });
}

function xoaQuanTri(maAdmin, hoTen) {
  if (confirm(`Bạn có chắc muốn xóa quản trị viên "${hoTen}" không?`)) {
    // Chuyển hướng tới file PHP xóa
    window.location.href = `../php/xoa_quantri.php?Ma_Admin=${encodeURIComponent(
      maAdmin
    )}`;
  }
}

function timKiemQuanTri() {
  // Lấy giá trị từ dropdown và input tìm kiếm
  var kieuTim = document.getElementById("kieuTimAdmin").value; // ma, ten, email
  var searchValue = document.getElementById("searchInput").value.toLowerCase();

  // Lấy tất cả các hàng quản trị viên trong bảng
  var rows = document.querySelectorAll("#quantrivien tbody tr");

  rows.forEach(function (row) {
    var cellValue = "";

    // Kiểm tra loại tìm kiếm
    if (kieuTim === "Ma_Admin") {
      cellValue = row
        .querySelector("td:nth-child(1)")
        .textContent.toLowerCase(); // Mã quản trị
    } else if (kieuTim === "ten") {
      cellValue = row
        .querySelector("td:nth-child(2)")
        .textContent.toLowerCase(); // Họ tên
    } else if (kieuTim === "email") {
      cellValue = row
        .querySelector("td:nth-child(3)")
        .textContent.toLowerCase(); // Email
    }

    // Kiểm tra nếu giá trị tìm kiếm có khớp
    if (cellValue.includes(searchValue)) {
      row.style.display = ""; // Hiển thị dòng nếu có khớp
    } else {
      row.style.display = "none"; // Ẩn dòng nếu không khớp
    }
  });
}

function timKiemKhachHang() {
  // Lấy giá trị từ dropdown và input tìm kiếm
  var kieuTim = document.getElementById("kieuTimKhachHang").value; // ten, email, taikhoan
  var searchValue = document
    .querySelector("#khachHang input[type='text']")
    .value.toLowerCase();

  // Lấy tất cả các hàng khách hàng trong bảng
  var rows = document.querySelectorAll("#dsNguoiDung tr");

  rows.forEach(function (row) {
    var cellValue = "";

    // Kiểm tra loại tìm kiếm
    if (kieuTim === "ten") {
      cellValue = row
        .querySelector("td:nth-child(2)")
        .textContent.toLowerCase(); // Họ tên
    } else if (kieuTim === "email") {
      cellValue = row
        .querySelector("td:nth-child(3)")
        .textContent.toLowerCase(); // Email
    } else if (kieuTim === "taikhoan") {
      cellValue = row
        .querySelector("td:nth-child(4)")
        .textContent.toLowerCase(); // Tài khoản
    }

    // Kiểm tra nếu giá trị tìm kiếm có khớp
    if (cellValue.includes(searchValue)) {
      row.style.display = ""; // Hiển thị dòng nếu có khớp
    } else {
      row.style.display = "none"; // Ẩn dòng nếu không khớp
    }
  });
}

// Hàm hiển thị thông tin admin đang đăng nhập
// function hienThongTinAdmin(sessionAdmin) {
//   if (!sessionAdmin || Object.keys(sessionAdmin).length === 0) {
//     alert("Không có thông tin admin.");
//     return;
//   }

//   // Hình đại diện
//   const hinhEl = document.getElementById("HinhAdmin");
//   hinhEl.src = sessionAdmin.Hinh_Anh
//     ? "../image/QuanTri/" + sessionAdmin.Hinh_Anh
//     : "../image/QuanTri/default-avatar.jpg";

//   // Thông tin cơ bản
//   document.getElementById("TenAdmin").innerText = sessionAdmin.Ho_Ten || "";
//   document.getElementById("ChucVuAdmin").innerText = sessionAdmin.Chuc_Vu || "";
//   document.getElementById("EmailAdmin").innerText = sessionAdmin.Email || "";
//   document.getElementById("LienLacAdmin").innerText =
//     sessionAdmin.Lien_Lac || "";
//   document.getElementById("DiaChiAdmin").innerText = sessionAdmin.Dia_Chi || "";
//   document.getElementById("GioiThieuAdmin").innerText =
//     sessionAdmin.Gioi_Thieu || "";

//   // Hiển thị modal
//   document.getElementById("khungThongTinAdmin").style.transform = "scale(1)";
// }

function suaTH(ma) {
  fetch(`get_thuonghieu2.php?MaTH=${ma}`)
    .then((res) => res.json())
    .then((data) => {
      if (data.error) return alert(data.error);

      document.getElementById("MaTH_sua").value = data.MaTH;
      document.getElementById("TenTH_sua").value = data.TenTH;
      document.getElementById("XuatXu_sua").value = data.XuatXu;
      document.getElementById("Mota_sua").value = data.Mota;
      document.getElementById("LogoTH_show").src = data.LogoTH;

      document.getElementById("LogoTH_old").value = data.LogoTH;

      document.getElementById("khungSuaTH").style.transform = "scale(1)";
    });
}
