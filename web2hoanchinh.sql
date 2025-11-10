SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;
SET time_zone = "+00:00";

-- -- ---------------------------------------------
-- -- Bảng: phanquyen
-- -- ---------------------------------------------

-- -- Bảng quyền
-- CREATE TABLE `quyen` (
--   `MaQuyen` INT(11) NOT NULL,
--   `TenQuyen` VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL,
--   PRIMARY KEY (`MaQuyen`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- -- Thêm dữ liệu mẫu cho bảng quyền
-- INSERT INTO `quyen` (`MaQuyen`, `TenQuyen`) VALUES
-- (1, 'Khách hàng'),
-- (2, 'Quản trị viên');

-- -- Bảng phân quyền: gán quyền cho người dùng (KHÔNG thêm CONSTRAINT ngay)
-- CREATE TABLE `phanquyen` (
--   `MaND` INT(11) NOT NULL,
--   `MaQuyen` INT(11) NOT NULL,
--   PRIMARY KEY (`MaND`, `MaQuyen`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- -- Gán quyền cho người dùng (lúc này INSERT vẫn được vì chưa có ràng buộc)
-- INSERT INTO `phanquyen` (`MaND`, `MaQuyen`) VALUES
-- (1, 1),  -- Admin
-- (2, 1),  -- Khách
-- (3, 1),
-- (4, 1),
-- (5, 2),
-- (6, 1),
-- (7, 1),
-- (8, 1);

-- ---------------------------------------------
-- Bảng: thuonghieu
-- ---------------------------------------------
CREATE TABLE `thuonghieu` (
  `MaTH` INT(11) NOT NULL,
  `TenTH` VARCHAR(70) COLLATE utf8_unicode_ci NOT NULL,
  `LogoTH` VARCHAR(200) COLLATE utf8_unicode_ci NOT NULL,
  `XuatXu` VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL,
  `Mota` TEXT COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`MaTH`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `thuonghieu` (`MaTH`, `TenTH`, `LogoTH`, `XuatXu`, `Mota`)
VALUES
(1, 'La Roche-Posay', '../image/admin/ThuongHieu/la_roche_posay_logo.png', 'Pháp', 'La Roche-Posay là thương hiệu dược mỹ phẩm thuộc tập đoàn L’Oréal, nổi bật với các sản phẩm dành cho da nhạy cảm và dễ kích ứng.'),
(2, 'Gamma Chemicals', '../image/admin/ThuongHieu/Gamma_Chemicals_logo.jpg', 'Việt Nam', 'Gamma Chemicals là công ty dược mỹ phẩm Việt Nam, nổi bật với sản phẩm Megaduo hỗ trợ điều trị mụn hiệu quả.'),
(3, 'Klairs', '../image/admin/ThuongHieu/klairs_logo.png', 'Hàn Quốc', 'Klairs là thương hiệu mỹ phẩm thuần chay của Hàn Quốc, chuyên các sản phẩm dịu nhẹ cho da nhạy cảm và da dễ kích ứng.'),
(4, 'Neutrogena', '../image/admin/ThuongHieu/neutrogena_logo.png', 'Mỹ', 'Neutrogena là thương hiệu mỹ phẩm nổi tiếng đến từ Mỹ, với các dòng sản phẩm dưỡng ẩm, chống nắng và chăm sóc da chuyên sâu.'),
(5, 'CeraVe', '../image/admin/ThuongHieu/cerave_logo.png', 'Mỹ', 'CeraVe là thương hiệu chăm sóc da nổi bật từ Mỹ, nổi tiếng với công thức chứa ceramide giúp phục hồi và bảo vệ hàng rào da.'),
(6, 'The Ordinary', '../image/admin/ThuongHieu/theordinary_logo.png', 'Canada', 'The Ordinary là thương hiệu thuộc DECIEM nổi tiếng với các sản phẩm dưỡng da tinh khiết và giá thành hợp lý'),
(7, 'Paulas Choice', '../image/admin/ThuongHieu/paulaschoice_logo.jpg', 'Mỹ', 'Paulas Choice là thương hiệu mỹ phẩm cao cấp của Mỹ, nổi tiếng về các sản phẩm trị liệu da và chống lão hóa'),
(8, 'Innisfree', '../image/admin/ThuongHieu/innisfree_logo.jpg', 'Hàn Quốc', 'Innisfree là thương hiệu thiên nhiên của Hàn Quốc, sử dụng nguyên liệu hữu cơ từ đảo Jeju'),
(9, 'Yves Saint Laurent', '../image/admin/ThuongHieu/ysl_logo.png', 'Pháp', 'Yves Saint Laurent là thương hiệu thời trang và nước hoa cao cấp đến từ Pháp'),
(10, 'Moroccanoil', '../image/admin/ThuongHieu/moroccanoil_logo.png', 'Israel', 'Moroccanoil là thương hiệu chăm sóc tóc nổi tiếng với thành phần dầu Argan giàu dưỡng chất');
-- ---------------------------------------------
-- Bảng: khuyenmai
-- ---------------------------------------------
CREATE TABLE `khuyenmai` (
  `MaKM` INT(11) NOT NULL,
  `TenKM` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
  `GiaTriKM` FLOAT NOT NULL,
  `NgayBD` DATETIME NOT NULL,
  `NgayKT` DATETIME NOT NULL,
  PRIMARY KEY (`MaKM`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `khuyenmai` (`MaKM`, `TenKM`, `GiaTriKM`, `NgayBD`, `NgayKT`) VALUES
(1, 'Không khuyến mãi', 0, '2019-04-08 00:00:00', '2022-04-17 00:00:00'),
(2, 'BlackFriday', 50, '2019-05-01 00:00:00', '2019-05-31 00:00:00'),
(3, 'Giá rẻ online', 30, '2019-05-01 00:00:00', '2019-05-31 00:00:00'),
(4, 'Đối tượng HSSV', 40, '2019-05-01 00:00:00', '2019-05-31 00:00:00'),
(5, 'Mới ra mắt', 70, '2019-05-01 00:00:00', '2019-05-31 00:00:00');


-- ---------------------------------------------
-- Bảng: loaisanpham
-- ---------------------------------------------
CREATE TABLE `loaisanpham` (
  `MaLoai` INT(11) NOT NULL,
  `TenLoai` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`MaLoai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `loaisanpham` (`MaLoai`, `TenLoai`) VALUES
(1, 'Sữa rửa mặt'),
(2, 'Kem chống nắng'),
(3, 'Toner'),
(4, 'Serum'),
(5, 'Dưỡng ẩm'),
(6, 'Tẩy trang'),
(7, 'Trị Mụn'),
(8, 'Nước hoa'),
(9, 'Dưỡng Tóc'),
(10, 'Make Up');

-- ---------------------------------------------
-- Bảng: sanpham
-- ---------------------------------------------


CREATE TABLE `sanpham` (
  `MaSP` INT(11) NOT NULL AUTO_INCREMENT,
  `TenSP` VARCHAR(3000) COLLATE utf8_unicode_ci NOT NULL,
  `MaLoai` INT(11) NOT NULL,
  `TenLoai` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
  `MaTH` INT(11) NOT NULL,
  `TenTH` VARCHAR(70) COLLATE utf8_unicode_ci NOT NULL,
  `GiaSP` INT(11) NOT NULL,
  `HinhAnh` VARCHAR(2000) COLLATE utf8_unicode_ci NOT NULL,
  `XuatXu` VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL,
  `SoLuong` INT(10) UNSIGNED NOT NULL DEFAULT 1,
  `DungTich` VARCHAR(10) COLLATE utf8_unicode_ci NOT NULL,
  `LoaiDa` VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL,
  `MaKM` INT(11) NOT NULL DEFAULT 0,
  `GiaTriKM` INT(11) NOT NULL DEFAULT 0,
  `TenKM` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
  `TPChinh` TEXT COLLATE utf8_unicode_ci NOT NULL,
  `TPFull` TEXT COLLATE utf8_unicode_ci NOT NULL,
  `MoTaSP` MEDIUMTEXT COLLATE utf8_unicode_ci NOT NULL,
  `TrangThai` INT(11) NOT NULL,
  PRIMARY KEY (`MaSP`),
  CONSTRAINT `fk_sp_thuonghieu` FOREIGN KEY (`MaTH`) REFERENCES `thuonghieu` (`MaTH`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sp_khuyenmai` FOREIGN KEY (`MaKM`) REFERENCES `khuyenmai` (`MaKM`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sp_loaisp` FOREIGN KEY (`MaLoai`) REFERENCES `loaisanpham` (`MaLoai`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `sanpham` (
  `MaSP`, `TenSP`,`MaLoai`, `MaTH`, `GiaSP`, `HinhAnh`, `XuatXu`, `SoLuong`,  
  `DungTich`, `LoaiDa`, `MaKM`, `TPChinh`, `TPFull`, `MoTaSP`, `TrangThai`) VALUES
(1, 'Gel Rửa Mặt La Roche-Posay Dành Cho Da Dầu, Nhạy Cảm', 1, 1, 350000, '../image/admin/SanPham/la roche-posay.jpg', 'Pháp', 200, '400ml', 'Da dầu, nhạy cảm', 2,
 'Zinc PCA', 'Nước tinh khiết, Zinc PCA, Coco-betaine, Sodium Laureth Sulfate', 'Làm sạch da dịu nhẹ, giảm bã nhờn và hỗ trợ ngừa mụn.', 1),

(2, 'Gel Dưỡng Megaduo Giảm Mụn, Mờ Thâm', 7, 2, 125000, '../image/admin/SanPham/megaduo.jpg', 'Việt Nam', 150, '15g', 'Da mụn', 4,
 'AHA, Azelaic Acid', 'Azelaic Acid, Glycolic Acid, Carbomer, Propylene Glycol', 'Giảm mụn, hỗ trợ làm sáng da và mờ vết thâm sau mụn.', 1),

(3, 'Nước Hoa Hồng Klairs Không Mùi Cho Da Nhạy Cảm', 3, 3, 295000, '../image/admin/SanPham/klairs.jpg', 'Hàn Quốc', 180, '180ml', 'Da nhạy cảm', 1,
 'Chiết xuất rau má', 'Chiết xuất rau má, Hyaluronic Acid, Beta Glucan, Glycerin', 'Cân bằng độ ẩm, làm dịu da và tăng độ đàn hồi.', 1),

(4, 'Kem Dưỡng Ẩm Neutrogena Cấp Nước Cho Da Dầu', 5, 4, 320000, '../image/admin/SanPham/neutrogena.jpg', 'Mỹ', 100, '50g', 'Da dầu', 5,
 'Hyaluronic Acid', 'Hyaluronic Acid, Dimethicone, Glycerin, Olive Extract', 'Dưỡng ẩm sâu, giúp da mềm mịn mà không gây nhờn rít.', 1),

(5, 'Kem Dưỡng CeraVe Cho Da Khô Đến Rất Khô', 5, 5, 310000, '../image/admin/SanPham/cerave.jpg', 'Mỹ', 80, '50ml', 'Da khô', 3,
 'Ceramide, Hyaluronic Acid', 'Ceramide NP, Hyaluronic Acid, Glycerin, Cholesterol', 'Phục hồi hàng rào bảo vệ da, giảm khô ráp và bong tróc.', 1),

(6, 'Kem Chống Nắng The Ordinary Mineral UV Filters SPF 30', 2, 6, 350000, '../image/admin/SanPham/theordinary_sunscreen.jpg', 'Canada', 100, '50ml', 'Mọi loại da', 1, 
 'Zinc Oxide', 'Zinc Oxide, Titanium Dioxide, Squalane', 'Bảo vệ da khỏi tia UV, không gây bết dính', 1),

(7, 'Toner Paulas Choice Skin Recovery Calming', 3, 7, 420000, '../image/admin/SanPham/paulaschoice_toner.jpg', 'Mỹ', 80, '190ml', 'Da khô', 2, 
 'Antioxidants', 'Chamomile Extract, Vitamin E, Allantoin', 'Làm dịu da, cấp ẩm và bảo vệ hàng rào da', 1),

(8, 'Serum Klairs Freshly Juiced Vitamin Drop', 4, 3, 460000, '../image/admin/SanPham/klairs_serum.jpg', 'Hàn Quốc', 120, '35ml', 'Da xỉn màu', 1, 
 'Vitamin C', 'Ascorbic Acid, Centella Asiatica Extract', 'Giúp sáng da và làm đều màu da', 1),

(9, 'Nước Tẩy Trang Innisfree Green Tea Cleansing Water', 6, 8, 280000, '../image/admin/SanPham/innisfree_cleansingwater.jpg', 'Hàn Quốc', 90, '300ml', 'Da dầu', 3, 
 'Chiết xuất trà xanh', 'Camellia Sinensis Leaf Extract, Glycerin', 'Làm sạch da dịu nhẹ, chống oxy hóa', 1),

(10, 'Nước Hoa YSL Libre Eau de Parfum', 8, 9, 3200000, '../image/admin/SanPham/ysl_libre.jpg', 'Pháp', 50, '50ml', 'Mọi loại da', 5, 
 'Tinh dầu hoa oải hương', 'Lavender Essence, Orange Blossom, Musk Accord', 'Nước hoa nữ hiện đại và quyến rũ', 1),

(11, 'Dầu Dưỡng Moroccanoil Treatment Original', 9, 10, 890000, '../image/admin/SanPham/moroccanoil_treatment.jpg', 'Israel', 60, '100ml', '---', 1, 
 'Dầu Argan', 'Argania Spinosa Kernel Oil, Linseed Extract', 'Giúp tóc mềm mượt, chống xoăn rối', 1),

(12, 'Son YSL Rouge Pur Couture The Slim', 10, 9, 980000, '../image/admin/SanPham/ysl_slim.jpg', 'Pháp', 70, '2.2g', 'Mọi loại da', 4, 
 'Tinh chất dưỡng môi', 'Vitamin E, Shea Butter', 'Son lì cao cấp, lâu trôi và dưỡng môi', 1),

(13, 'Sữa Rửa Mặt La Roche-Posay Effaclar Purifying Foaming Gel', 1, 1, 380000, '../image/admin/SanPham/la_roche_posay_gel_1.jpg', 'Pháp', 200, '400ml', 'Da dầu, da mụn', 1,
 'Zinc PCA', 'Zinc PCA, Nước tinh khiết, Coco-betaine', 'Làm sạch da, giảm bã nhờn và hỗ trợ điều trị mụn.', 1),

(14, 'Sữa Rửa Mặt CeraVe Hydrating Cleanser', 1, 5, 320000, '../image/admin/SanPham/cerave_hydrating_cleanser.jpg', 'Mỹ', 180, '473ml', 'Da khô, nhạy cảm', 1,
 'Ceramide', 'Ceramide NP, Hyaluronic Acid, Glycerin', 'Làm sạch da nhẹ nhàng, cấp ẩm cho da khô và nhạy cảm.', 1),

(15, 'Sữa Rửa Mặt Neutrogena Hydro Boost Water Gel Cleanser', 1, 4, 280000, '../image/admin/SanPham/neutrogena_hydroboost_cleanser.jpg', 'Mỹ', 220, '200ml', 'Da dầu, da khô', 1,
 'Hyaluronic Acid', 'Hyaluronic Acid, Glycerin', 'Làm sạch và cấp ẩm cho da suốt cả ngày.', 1),

(16, 'Sữa Rửa Mặt Klairs Rich Moist Foaming Cleanser', 1, 3, 290000, '../image/admin/SanPham/klairs_foaming_cleanser.jpg', 'Hàn Quốc', 150, '150ml', 'Da khô, nhạy cảm', 1,
 'Hyaluronic Acid', 'Hyaluronic Acid, Glycerin', 'Làm sạch da dịu nhẹ, giúp da mềm mịn và không khô căng.', 1),

(17, 'Sữa Rửa Mặt The Ordinary Squalane Cleanser', 1, 6, 330000, '../image/admin/SanPham/theordinary_squalane_cleanser.jpg', 'Canada', 200, '150ml', 'Da khô, da nhạy cảm', 1,
 'Squalane', 'Squalane, Glycerin', 'Làm sạch da và loại bỏ lớp trang điểm mà không gây khô da.', 1),

(18, 'Sữa Rửa Mặt Innisfree Green Tea Foam Cleanser', 1, 8, 240000, '../image/admin/SanPham/innisfree_green_tea_cleanser.jpg', 'Hàn Quốc', 180, '150ml', 'Da dầu, da nhạy cảm', 1,
 'Chiết xuất trà xanh', 'Camellia Sinensis Leaf Extract, Glycerin', 'Làm sạch da dịu nhẹ, giúp da tươi mới và không bết dính.', 1),

(19, 'Sữa Rửa Mặt Paula\s Choice Resist Optimal Results Hydrating Cleanser', 1, 7, 420000, '../image/admin/SanPham/paulaschoice_hydrating_cleanser.jpg', 'Mỹ', 150, '190ml', 'Da nhạy cảm, da khô', 1,
 'Ceramide', 'Ceramide NP, Hyaluronic Acid, Glycerin', 'Làm sạch và bảo vệ độ ẩm cho da khô, da nhạy cảm.', 1),

(20, 'Sữa Rửa Mặt La Roche-Posay Toleriane Purifying Foaming Cream', 1, 1, 400000, '../image/admin/SanPham/la_roche_posay_gel_2.jpeg', 'Pháp', 220, '150ml', 'Da nhạy cảm', 1,
 'Glycerin', 'Glycerin, Aqua, Poloxamer', 'Làm sạch da dịu nhẹ, bảo vệ và duy trì sự cân bằng độ ẩm tự nhiên của da.', 1),

(21, 'Sữa Rửa Mặt Neutrogena Oil-Free Acne Wash', 1, 4, 250000, '../image/admin/SanPham/neutrogena_acne_wash.jpg', 'Mỹ', 180, '200ml', 'Da dầu, mụn', 1,
 'Salicylic Acid', 'Salicylic Acid, Glycerin', 'Giảm mụn, làm sạch da sâu mà không gây khô da.', 1),

(22, 'Sữa Rửa Mặt CeraVe Foaming Facial Cleanser', 1, 5, 310000, '../image/admin/SanPham/cerave_foaming_cleanser.jpg', 'Mỹ', 200, '236ml', 'Da dầu, da nhạy cảm', 1,
 'Ceramide', 'Ceramide NP, Niacinamide, Hyaluronic Acid', 'Làm sạch da hiệu quả, không làm khô hay căng da.', 1);
UPDATE sanpham sp
LEFT JOIN khuyenmai km ON sp.MaKM = km.MaKM
LEFT JOIN thuonghieu th ON sp.MaTH = th.MaTH
LEFT JOIN loaisanpham lsp ON sp.MaLoai = lsp.MaLoai
SET
  sp.TenKM = km.TenKM,
  sp.GiaTriKM = IFNULL(km.GiaTriKM, 0),
  sp.TenTH = th.TenTH,
  sp.TenLoai = lsp.TenLoai;

-- ---------------------------------------------
-- Bảng: nguoidung
-- ---------------------------------------------
CREATE TABLE `nguoidung` (
  `MaND` int(11) NOT NULL,
  `TenND` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `TaiKhoan` varchar(100) NOT NULL,
  `MatKhau` varchar(100) NOT NULL,
  `SDT` varchar(20) NOT NULL,
  `DiaChi` varchar(700) NOT NULL,
  `TrangThai` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`MaND`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoidung`
--

INSERT INTO `nguoidung` (`MaND`, `TenND`, `Email`, `TaiKhoan`, `MatKhau`, `SDT`, `DiaChi`, `TrangThai`) VALUES
(1, 'Nguyễn Thị Lan', 'lan.nguyen@gmail.com', 'lannguyen113', 'lanthi89', '0123456789', 'Số 12, Đường Trần Hưng Đạo, Quận 1, TP. Hồ Chí Minh', 0),
(2, 'Trần Văn Minh', 'minh.tran@gmail.com', 'tranminh90', 'minhtran90', '0987654321', 'Số 45, Đường Nguyễn Huệ, Quận Hải Châu, TP. Đà Nẵng', 1),
(3, 'Lê Thị Hồng', 'hong.le@gmail.com', 'lehong92', 'hongle92', '0912345678', 'Số 78, Đường Lý Thường Kiệt, Quận 10, TP. Hồ Chí Minh', 1),
(4, 'Phạm Quốc Huy', 'huy.pham@gmail.com', 'phamquochuy', 'quochuy123', '0909123456', 'Số 22, Đường Cách Mạng Tháng 8, Thành phố Cần Thơ', 1),
(5, 'Đỗ Thị Mai', 'mai.do@gmail.com', 'domaimai', 'mai123do', '0978123456', 'Số 35, Đường Võ Thị Sáu, TP. Vũng Tàu, Tỉnh Bà Rịa - Vũng Tàu', 1),
(6, 'Ngô Văn An', 'an.ngo@gmail.com', 'ngovan123', 'anngo88', '0932123456', 'Số 9, Đường Điện Biên Phủ, Thành phố Buôn Ma Thuột, Tỉnh Đắk Lắk', 1),
(7, 'Bùi Thị Thanh', 'thanh.bui@gmail.com', 'buithithanh', 'thanhbui99', '0967123456', 'Số 18, Đường Nguyễn Trãi, Quận 5, TP. Hồ Chí Minh', 1),
(8, 'Võ Minh Tuấn', 'tuan.vo@gmail.com', 'vominhtuan', 'tuanvo01', '0956123456', 'Số 50, Đường Phan Đình Phùng, TP. Đà Lạt, Tỉnh Lâm Đồng', 1);

-- Quản trị viên
CREATE TABLE `quan_tri` (
  `Ma_Admin` varchar(5) NOT NULL,        
  `Ho_Ten` varchar(255) NOT NULL,      
  `Email` varchar(255) NOT NULL,         
  `Mat_Khau` varchar(255) NOT NULL,      
  `Hinh_Anh` text NOT NULL,              
  `Lien_Lac` varchar(255) NOT NULL,     
  `Dia_Chi` text NOT NULL,
  `Chuc_Vu` varchar(255) NOT NULL,      
  `Gioi_Thieu` text NOT NULL,
  PRIMARY KEY (`Ma_Admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Đổ dữ liệu cho bảng `quan_tri`
INSERT INTO `quan_tri` 
(`Ma_Admin`, `Ho_Ten`, `Email`, `Mat_Khau`, `Hinh_Anh`, `Lien_Lac`, `Dia_Chi`, `Chuc_Vu`, `Gioi_Thieu`) 
VALUES
('1', 'Yves', 'test@abc.com', 'admin', 'yves.jpg', '012345678', '255 An Duong Vuong Str D5 HCMC', 'Quản trị viên', 'Nhân Viên Mới'),
('2', 'Hắc Tún', 'admin@admin.com', 'admin', 'irene.jpeg', '0586128566', '80 79Str. Tan Quy D7', 'Nhân viên ', 'Test'),
('3', 'test', 'chu@chu.com', 'admin', '98489932_973836263052754_3130673838979809280_n.jpg', 'test', 'test', 'Nhân Viên', 'test');

-- ---------------------------------------------
-- Bảng: giohang (Shopping Cart)
-- ---------------------------------------------
CREATE TABLE `giohang` (
  `MaGioHang` INT(11) NOT NULL AUTO_INCREMENT,
  `MaND` INT(11) NOT NULL,
  `NgayTao` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `NgayCapNhat` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`MaGioHang`),
  UNIQUE KEY `unique_user_cart` (`MaND`), -- Mỗi người dùng chỉ có 1 giỏ hàng
  CONSTRAINT `fk_giohang_nguoidung` FOREIGN KEY (`MaND`) REFERENCES `nguoidung` (`MaND`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ---------------------------------------------
-- Bảng: giohang_chitiet (Cart Items)
-- ---------------------------------------------
CREATE TABLE `giohang_chitiet` (
  `MaGioHang` INT(11) NOT NULL,
  `MaSP` INT(11) NOT NULL,
  `SoLuong` INT(11) NOT NULL DEFAULT 1,
  `DonGia` INT(11) NOT NULL, -- Lưu giá tại thời điểm thêm vào giỏ
  `NgayThem` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`MaGioHang`, `MaSP`),
  CONSTRAINT `fk_giohangct_giohang` FOREIGN KEY (`MaGioHang`) REFERENCES `giohang` (`MaGioHang`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_giohangct_sanpham` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
-- Tạo giỏ hàng cho các người dùng hiện có
INSERT INTO `giohang` (`MaND`, `NgayTao`) 
SELECT `MaND`, NOW() FROM `nguoidung`;

-- Thêm sản phẩm vào giỏ hàng của một số người dùng
INSERT INTO `giohang_chitiet` (`MaGioHang`, `MaSP`, `SoLuong`, `DonGia`) VALUES
(1, 1, 2, (SELECT `GiaSP` FROM `sanpham` WHERE `MaSP` = 1)),
(1, 3, 1, (SELECT `GiaSP` FROM `sanpham` WHERE `MaSP` = 3)),
(2, 5, 1, (SELECT `GiaSP` FROM `sanpham` WHERE `MaSP` = 5)),
(3, 2, 3, (SELECT `GiaSP` FROM `sanpham` WHERE `MaSP` = 2)),
(3, 7, 1, (SELECT `GiaSP` FROM `sanpham` WHERE `MaSP` = 7));
-- -------------------------------
-- Bảng địa chỉ người dùng
-- -------------------------------
CREATE TABLE `diachi` (
  `MaDC` INT(11) NOT NULL AUTO_INCREMENT,
  `MaND` INT(11) NOT NULL,
  `DiaChi` VARCHAR(255) NOT NULL COMMENT 'Địa chỉ dạng text đơn giản',
  `MacDinh` TINYINT(1) DEFAULT 0 COMMENT '1 là địa chỉ mặc định',
  PRIMARY KEY (`MaDC`),
  FOREIGN KEY (`MaND`) REFERENCES `nguoidung` (`MaND`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DELIMITER $$

CREATE TRIGGER after_insert_nguoidung
AFTER INSERT ON nguoidung
FOR EACH ROW
BEGIN
  INSERT INTO diachi (MaND, DiaChi, MacDinh)
  VALUES (NEW.MaND, NEW.DiaChi, 1);
END$$

DELIMITER ;

-- Cập nhật dữ liệu mẫu
INSERT INTO `diachi` (`MaND`, `DiaChi`, `MacDinh`) VALUES
(1, 'Số 12, Đường Trần Hưng Đạo, Quận 1, TP. Hồ Chí Minh', 1),
(2, 'Số 45, Đường Nguyễn Huệ, Quận Hải Châu, TP. Đà Nẵng', 1),
(3, 'Số 78, Đường Lý Thường Kiệt, Quận 10, TP. Hồ Chí Minh', 1),
(4, 'Số 22, Đường Cách Mạng Tháng 8, Thành phố Cần Thơ', 1),
(5, 'Số 35, Đường Võ Thị Sáu, TP. Vũng Tàu, Tỉnh Bà Rịa - Vũng Tàu', 1),
(6, 'Số 9, Đường Điện Biên Phủ, Thành phố Buôn Ma Thuột, Tỉnh Đắk Lắk', 1),
(7, 'Số 18, Đường Nguyễn Trãi, Quận 5, TP. Hồ Chí Minh', 1),
(8, 'Số 50, Đường Phan Đình Phùng, TP. Đà Lạt, Tỉnh Lâm Đồng', 1);

-- ---------------------------------------------
-- Bảng: Trạng thái đơn hàng (TẠO TRƯỚC)
-- ---------------------------------------------
CREATE TABLE `trangthaidonhang` (
  `MaTT` INT(11) NOT NULL,
  `TrangThai` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`MaTT`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `trangthaidonhang` (`MaTT`, `TrangThai`) VALUES
(1, 'Chưa xác nhận'),
(2, 'Đã xác nhận'),
(3, 'Đang giao hàng'),
(4, 'Đã giao hàng'),
(5, 'Đã hủy');


-- ---------------------------------------------
-- Bảng: hoadon
-- ---------------------------------------------
CREATE TABLE `hoadon` (
  `MaHD` INT(11) NOT NULL AUTO_INCREMENT,
  `MaND` INT(11) NOT NULL,
  `MaTT` INT(11) NOT NULL DEFAULT 1,
  `TrangThai` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
  `NgayLap` DATETIME NOT NULL,
  `NguoiNhan` VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL,
  `SDT` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL,
  `DiaChi` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
  `PhuongThucTT` VARCHAR(40) COLLATE utf8_unicode_ci NOT NULL,
  `TongTien` INT(11) NOT NULL,
  PRIMARY KEY (`MaHD`),
  CONSTRAINT `fk_hoadon_MaND` FOREIGN KEY (`MaND`) REFERENCES `nguoidung` (`MaND`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_hoadon_MaTT` FOREIGN KEY (`MaTT`) REFERENCES `trangthaidonhang` (`MaTT`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Trigger: Tự động cập nhật cột TrangThai khi thêm hóa đơn
DELIMITER //
CREATE TRIGGER trg_hoadon_before_insert
BEFORE INSERT ON hoadon
FOR EACH ROW
BEGIN
  DECLARE v_tt VARCHAR(100);
  SELECT TrangThai INTO v_tt FROM trangthaidonhang WHERE MaTT = NEW.MaTT;
  SET NEW.TrangThai = v_tt;
END;
//
DELIMITER ;

-- Trigger: Tự động cập nhật lại cột TrangThai khi MaTT thay đổi
DELIMITER //

CREATE TRIGGER trg_hoadon_before_update
BEFORE UPDATE ON hoadon
FOR EACH ROW
BEGIN
  DECLARE v_tt VARCHAR(100);

  IF NEW.MaTT <> OLD.MaTT THEN
    SELECT TrangThai INTO v_tt FROM trangthaidonhang WHERE MaTT = NEW.MaTT;
    SET NEW.TrangThai = v_tt;
  END IF;
END;
//

DELIMITER ;


-- Thêm dữ liệu vào bảng hoadon
INSERT INTO `hoadon` (`MaHD`, `MaND`, `MaTT`, `NgayLap`, `NguoiNhan`, `SDT`, `DiaChi`, `PhuongThucTT`) VALUES
(1, 2, 4, '2019-08-20 13:20:56', 'Nguyễn Thị Lan', '0123456789', 'Số 12, Đường Trần Hưng Đạo, Quận 1, TP. Hồ Chí Minh', 'Thanh toán khi nhận hàng'),
(2, 3, 2, '2019-09-15 16:45:30', 'Lê Thị Hồng', '0912345678', 'Số 78, Đường Lý Thường Kiệt, Quận 10, TP. Hồ Chí Minh', 'Chuyển khoản ngân hàng'),
(3, 7, 2, '2025-04-27 10:15:00', 'Lê Thị Hồng', '0912345678', 'Số 78, Đường Lý Thường Kiệt, Quận 10, TP. Hồ Chí Minh', 'Thanh toán khi nhận hàng'),
(4, 4, 3, '2025-04-27 14:30:00', 'Phạm Quốc Huy', '0909123456', 'Số 22, Đường Cách Mạng Tháng 8, Thành phố Cần Thơ', 'Thanh toán qua thẻ'),
(5, 5, 1, '2025-04-27 16:00:00', 'Đỗ Thị Mai', '0978123456', 'Số 35, Đường Võ Thị Sáu, TP. Vũng Tàu, Tỉnh Bà Rịa - Vũng Tàu', 'Chuyển khoản ngân hàng'),
(6, 6, 4, '2025-04-27 18:45:00', 'Ngô Văn An', '0932123456', 'Số 9, Đường Điện Biên Phủ, Thành phố Buôn Ma Thuột, Tỉnh Đắk Lắk', 'Thanh toán khi nhận hàng');

-- ---------------------------------------------
-- Bảng: chitiethoadon
-- ---------------------------------------------
CREATE TABLE `chitiethoadon` (
  `MaHD` INT(11) NOT NULL,
  `MaSP` INT(11) NOT NULL, 
  `TenSP` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  `SoLuong` INT(11) NOT NULL,
  `DonGia` INT(11) NOT NULL,
  `ThanhTien` INT(11) NOT NULL,
  PRIMARY KEY (`MaHD`, `MaSP`),
  CONSTRAINT `fk_sp_hoadon` FOREIGN KEY (`MaHD`) REFERENCES `hoadon` (`MaHD`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sp_sanpham` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



-- Thêm dữ liệu vào bảng chitiethoadon
INSERT INTO `chitiethoadon` (`MaHD`, `MaSP`, `SoLuong`) VALUES
(1, 1, 2),
(1, 2, 1),
(2, 3, 1),
(3, 5, 2),  
(3, 8, 1),  
(4, 7, 1),  
(4, 9, 1),  
(5, 6, 1),  
(5, 10, 1),  
(6, 2, 3),  
(6, 5, 1);  

-- Cập nhật bảng chitiethoadon với thông tin về giá và tính thành tiền
UPDATE chitiethoadon cthd
LEFT JOIN sanpham sp ON cthd.MaSP = sp.MaSP
LEFT JOIN hoadon hd ON cthd.MaHD = hd.MaHD
SET
  cthd.DonGia = IFNULL(sp.GiaSP, 0),
  cthd.TenSP = sp.TenSP,
  cthd.ThanhTien = cthd.SoLuong * IFNULL(sp.GiaSP, 0);

-- Cập nhật bảng hoadon với thông tin từ các bảng khác
UPDATE hoadon hd
LEFT JOIN trangthaidonhang tt ON hd.MaTT = tt.MaTT
LEFT JOIN nguoidung nd ON hd.MaND = nd.MaND
SET
  hd.NguoiNhan = nd.TenND,
  hd.SDT = nd.SDT,
  -- hd.TrangThai = IFNULL(tt.TrangThai, hd.TrangThai),
  hd.TongTien = (
    SELECT IFNULL(SUM(cthd.ThanhTien), 0)
    FROM chitiethoadon cthd
    WHERE cthd.MaHD = hd.MaHD
  )+ 30000;

UPDATE sanpham sp
LEFT JOIN (
  SELECT MaSP, SUM(SoLuong) AS TongSoLuongBan
  FROM chitiethoadon
  GROUP BY MaSP
) AS b ON sp.MaSP = b.MaSP
SET sp.SoLuong = GREATEST(0, sp.SoLuong - IFNULL(b.TongSoLuongBan, 0));


DELIMITER //

CREATE TRIGGER trg_chitiethoadon_after_insert
AFTER INSERT ON chitiethoadon
FOR EACH ROW
BEGIN
  UPDATE sanpham
  SET SoLuong = GREATEST(0, SoLuong - NEW.SoLuong)
  WHERE MaSP = NEW.MaSP;
END;
//

DELIMITER ;


-- ---------------------------------------------
-- Kết thúc
-- ---------------------------------------------

-- SHOW TABLES LIKE 'chitiethoadon';

-- tự động thêm mã người dùng 
ALTER TABLE nguoidung
MODIFY COLUMN MaND INT(11) NOT NULL AUTO_INCREMENT;

