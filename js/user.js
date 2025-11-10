function laySanPham(MaSP) {
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

      // Gán giá trị vào các thẻ HTML
      document.getElementById("MaSP").value = sanpham.MaSP || "";
      document.getElementById("detailName").textContent = sanpham.TenSP || "";
      // document.getElementById("detailPrice").textContent = formatCurrency(
      //   sanpham.GiaSP
      // );
      document.getElementById("LoaiSanPham").value = sanpham.MaLoai || "";
      document.getElementById("ThuongHieu").value = sanpham.MaTH || "";
      document.getElementById("XuatXu").textContent = sanpham.XuatXu || "";
      document.getElementById("SoLuong").value = sanpham.SoLuong || 0;
      document.getElementById("DungTich").textContent = sanpham.DungTich || "";
      document.getElementById("LoaiDa").textContent = sanpham.LoaiDa || "";
      document.getElementById("KhuyenMai").value = sanpham.MaKM || "";
      document.getElementById("TPChinh").textContent = sanpham.TPChinh || "";
      document.getElementById("TPFull").textContent = sanpham.TPFull || "";
      document.getElementById("MoTaSP").textContent = sanpham.MoTaSP || "";
      document.getElementById("detail-image").src =
        sanpham.HinhAnh || "no-image.png";

      // Hiển thị thông tin tên sản phẩm nhiều nơi
      document.getElementById("detailName2").textContent = sanpham.TenSP || "";
      document.getElementById("detailName3").textContent = sanpham.TenSP || "";

      // // Cập nhật TenTH (hiện ở 2 chỗ)
      // const TenTHText = document.getElementById("TenTH");
      // const TenTH2Text = document.getElementById("TenTH2");
      // if (TenTHText) TenTHText.textContent = sanpham.TenTH || "";
      // if (TenTH2Text) TenTH2Text.textContent = sanpham.TenTH || "";

      // Gán thương hiệu và xuất xứ
      const tenTHEl1 = document.getElementById("TenTH");

      if (tenTHEl1) {
        tenTHEl1.textContent = sanpham.TenTH || "";
        tenTHEl1.setAttribute("data-math", sanpham.MaTH); // gán để click vào xem chi tiết
      }

      const TenTH2Text = document.getElementById("TenTH2");
      if (TenTH2Text) {
        TenTH2Text.textContent = sanpham.TenTH || "";
        TenTH2Text.dataset.math = sanpham.MaTH || ""; // GÁN data-math CHO <a>
      }

      // Xuất xứ
      document.getElementById("XuatXu").textContent = sanpham.XuatXu || "";

      // Gán giá trị khuyến mãi nếu có select option tương ứng
      const selectKM = document.getElementById("KhuyenMai");
      const selectedOptionKM = selectKM?.querySelector(
        `option[value="${sanpham.MaKM}"]`
      );
      if (selectedOptionKM) {
        document.getElementById("TenKM").value =
          selectedOptionKM.dataset.tenkm || "";
        document.getElementById("GiaTriKM").value =
          selectedOptionKM.dataset.giatrikm || "";
      }

      // Gán TenLoai
      const selectLSP = document.getElementById("LoaiSanPham");
      const selectedOptionLSP = selectLSP?.querySelector(
        `option[value="${sanpham.MaLoai}"]`
      );
      if (selectedOptionLSP) {
        document.getElementById("TenLoai").value =
          selectedOptionLSP.dataset.tenloai || "";
      }

      // Gán TenTH
      const selectTH = document.getElementById("ThuongHieu");
      const selectedOptionTH = selectTH?.querySelector(
        `option[value="${sanpham.MaTH}"]`
      );
      if (selectedOptionTH) {
        document.getElementById("TenTH").value =
          selectedOptionTH.dataset.tenth || "";
      }

      // Nếu có thông tin mô tả, hiện phần chi tiết
      document.getElementById("product-detail").style.display = "block";
    })
    .catch((error) => {
      alert("Đã xảy ra lỗi: " + error.message);
    });
}

// Format giá tiền (VND)
function formatCurrency(number) {
  if (!number) return "";
  return Number(number).toLocaleString("vi-VN", {
    style: "currency",
    currency: "VND",
  });
}

//----------------------------------------------------------Lấy thương hiệu-----------------------------------------------------------------

function layThuonghieu(MaTH) {
  fetch("../php/get_thuonghieu.php?MaTH=" + encodeURIComponent(MaTH))
    .then((response) => {
      if (!response.ok) throw new Error("Lỗi kết nối đến máy chủ");
      return response.json();
    })
    .then((thuonghieu) => {
      if (thuonghieu.error) {
        alert(thuonghieu.error);
        return;
      }

      // Gán thông tin vào modal
      document.getElementById("TenTH").innerText = thuonghieu.TenTH;
      document.getElementById("brand-logo").src = thuonghieu.LogoTH;
      document.getElementById("brand-description").innerText = thuonghieu.TenTH;
      document.getElementById("brand-origin").innerText = thuonghieu.XuatXu;
      document.getElementById("brand-details").innerText = thuonghieu.Mota;

      // Mở modal + cuộn đến modal
      // const modal = document.getElementById("khungChiTietThuongHieu");
      // modal.classList.add("open");
      // modal.scrollIntoView({ behavior: "smooth" });
      document.getElementById("khungChiTietThuongHieu").classList.add("open");
    })
    .catch((error) => {
      alert("Đã xảy ra lỗi: " + error.message);
    });
}

//----------------------------------------------------------Nút xem tất cả-----------------------------------------------------------------

document.addEventListener("DOMContentLoaded", function () {
  const viewAllBtn = document.querySelector(".view-all-btn");
  viewAllBtn.addEventListener("click", function () {
    // Hiện các sản phẩm đang bị ẩn
    document.querySelectorAll(".hidden-product").forEach(function (item) {
      item.style.display = "block";
    });

    // Ẩn nút sau khi bấm
    viewAllBtn.style.display = "none";
  });
});
