function timKiemNguoiDung(input) {
    // Lấy giá trị tìm kiếm và loại tìm kiếm
    const searchValue = input.value.toLowerCase();
    const kieuTim = document.getElementById("kieuTimKhachHang").value;

    // Lấy tất cả các hàng trong bảng khách hàng
    const rows = document.querySelectorAll("#khachHang tbody tr");

    rows.forEach(row => {
        let cellValue = "";

        // Kiểm tra loại tìm kiếm
        if (kieuTim === "ten") {
            cellValue = row.querySelector("td:nth-child(2)").textContent.toLowerCase(); // Tên khách hàng
        } else if (kieuTim === "email") {
            cellValue = row.querySelector("td:nth-child(3)").textContent.toLowerCase(); // Email
        } else if (kieuTim === "taikhoan") {
            cellValue = row.querySelector("td:nth-child(4)").textContent.toLowerCase(); // Tên tài khoản
        }

        // Kiểm tra nếu giá trị tìm kiếm có khớp
        if (cellValue.includes(searchValue)) {
            row.style.display = ""; // Hiển thị dòng nếu có khớp
        } else {
            row.style.display = "none"; // Ẩn dòng nếu không khớp
        }
    });
}
