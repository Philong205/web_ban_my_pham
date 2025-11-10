<?php
require_once("DB_business.php");

// Lớp kết nối CSDL
class Database {
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {
            self::$conn = new mysqli("localhost", "root", "", "web2");
            if (self::$conn->connect_error) {
                die("Kết nối thất bại: " . self::$conn->connect_error);
            }
            self::$conn->set_charset("utf8"); // Đặt charset UTF-8
        }
        return self::$conn;
    }
}

// Hiển thị bảng dữ liệu
function show_DataBUS_as_Table($bus) {
    echo "<table cellspacing='15'>";
    foreach ($bus->select_all() as $row) {
        echo "<tr>";
        foreach ($row as $col) {
            echo "<td>" . htmlspecialchars($col) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

// ===== Các lớp nghiệp vụ =====

// Lớp sản phẩm
class SanPhamBUS extends DB_business {
    function __construct() {
        $this->__conn = Database::getConnection();
        $this->setTable("SanPham", "MaSP");
    }

    function capNhapTrangThai($trangthai, $id) {
        $sanpham = $this->select_by_id("*", $id);
        $sanpham["TrangThai"] = $trangthai;
        return $this->update_by_id($sanpham, $id);
    }

    public function updateProductStatus($maSP, $status) {
        try {
            $sql = "UPDATE SanPham SET TrangThai = ? WHERE MaSP = ?";
            $stmt = $this->__conn->prepare($sql);
            if (!$stmt) throw new Exception("Prepare failed: " . $this->__conn->error);
            $stmt->bind_param("is", $status, $maSP);
            $stmt->execute();
            return $stmt->affected_rows > 0;
        } catch (Exception $e) {
            error_log("Update Product Status Error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteProduct($maSP) {
        try {
            $sql = "DELETE FROM SanPham WHERE MaSP = ?";
            $stmt = $this->__conn->prepare($sql);
            if (!$stmt) throw new Exception("Prepare failed: " . $this->__conn->error);
            $stmt->bind_param("s", $maSP);
            $stmt->execute();
            return $stmt->affected_rows > 0;
        } catch (Exception $e) {
            error_log("Delete Product Error: " . $e->getMessage());
            return false;
        }
    }

    public function select_by_loai($MaLoai) {
        $query = "
            SELECT sp.MaSP, sp.TenSP, sp.MaLoai, sp.MaTH, sp.GiaSP, sp.HinhAnh, sp.XuatXu, 
                   sp.SoLuong, sp.DungTich, sp.LoaiDa, sp.MaKM, km.TenKM, km.GiaTriKM, sp.TPChinh, 
                   sp.TPFull, sp.MoTaSP, sp.TrangThai, lsp.TenLoai, th.TenTH
            FROM sanpham sp
            LEFT JOIN loaisanpham lsp ON sp.MaLoai = lsp.MaLoai
            LEFT JOIN thuonghieu th ON sp.MaTH = th.MaTH
            LEFT JOIN khuyenmai km ON sp.MaKM = km.MaKM
            WHERE sp.MaLoai = ? AND sp.TrangThai != 0
        ";
    
        $stmt = Database::getConnection()->prepare($query);
        $stmt->bind_param('i', $MaLoai); // 'i' là kiểu dữ liệu integer
        $stmt->execute();
        
        $result = $stmt->get_result(); // Lấy kết quả truy vấn
        $sanpham = [];
        
        while ($row = $result->fetch_assoc()) { // Dùng fetch_assoc() để lấy từng dòng dữ liệu
            // Kiểm tra sự tồn tại của 'TenKM' và 'GiaTriKM' để tránh lỗi "undefined array key"
            $row['TenKM'] = isset($row['TenKM']) ? $row['TenKM'] : ''; // Nếu không có thì gán là chuỗi rỗng
            $row['GiaTriKM'] = isset($row['GiaTriKM']) ? $row['GiaTriKM'] : 0; // Nếu không có thì gán là 0
            
            $sanpham[] = $row;
        }
        
        return $sanpham; // Trả về mảng kết quả
    }

    public function select_best_selling() {
        $sql = "
            SELECT sp.*, 
                   IFNULL(SUM(cthd.SoLuong), 0) AS DaBan
            FROM sanpham sp
            LEFT JOIN chitiethoadon cthd ON sp.MaSP = cthd.MaSP
            WHERE sp.TrangThai != 0
            GROUP BY sp.MaSP
            ORDER BY DaBan DESC
        ";
    
        $result = $this->__conn->query($sql);
        $sanpham = [];
    
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $sanpham[] = $row;
            }
        }
    
        return $sanpham;
    }
    public function select_all_order_by_discount() {
        $query = "SELECT * FROM sanpham WHERE GiaTriKM >= 50 AND TrangThai != 0 ORDER BY GiaTriKM DESC";
        return $this->get_list($query);
    }

    public function timKiemKeyWord($keyword = '') {
        // Kết nối
        $conn = Database::getConnection();
    
        // Câu truy vấn ban đầu
        $query = "SELECT sp.*, km.GiaTriKM
                  FROM sanpham sp
                  LEFT JOIN khuyenmai km ON sp.MaKM = km.MaKM
                  WHERE sp.TrangThai != 0";
    
        // Thêm điều kiện tìm kiếm nếu có từ khóa
        $params = [];
        $types = '';
    
        if (!empty($keyword)) {
            $query .= " AND sp.TenSP LIKE ?";
            $params[] = "%" . $keyword . "%";
            $types .= 's'; // string
        }
    
        // Chuẩn bị statement
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            error_log("Lỗi prepare trong timKiemKeyWord: " . $conn->error);
            return [];
        }
    
        // Bind param nếu có
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
    
        // Thực thi và lấy kết quả
        $stmt->execute();
        $result = $stmt->get_result();
    
        $sanpham = [];
        while ($row = $result->fetch_assoc()) {
            $sanpham[] = $row;
        }
    
        return $sanpham;
    }
    
    

    public function timKiemNangCao($keyword = '', $MaLoai = null, $minPrice = null, $maxPrice = null, $sortOrder = 'default', $page = 1, $limit = 8) {
        // Bắt đầu câu truy vấn
        $query = "SELECT sp.*, km.GiaTriKM
                  FROM sanpham sp
                  LEFT JOIN khuyenmai km ON sp.MaKM = km.MaKM
                  WHERE sp.TrangThai != 0"; // Điều kiện sản phẩm không bị ẩn (TrangThai != 0)
    
        // Mảng lưu các tham số để chuẩn bị bind_param
        $params = [];
        $types = ""; // Chuỗi kiểu dữ liệu cho bind_param
    
        // Điều kiện tìm kiếm theo tên sản phẩm
        if (!empty($keyword)) {
            $query .= " AND sp.TenSP LIKE ?";
            $params[] = "%" . $keyword . "%";
            $types .= "s"; // 's' cho chuỗi
        }
    
        // Điều kiện tìm kiếm theo mã loại sản phẩm
        if (!empty($MaLoai)) {
            $query .= " AND sp.MaLoai = ?";
            $params[] = $MaLoai;
            $types .= "i"; // 'i' cho integer
        }
    
        // Điều kiện tìm kiếm theo giá thấp nhất
        if ($minPrice !== null && $minPrice >= 0) {
            $query .= " AND sp.GiaSP >= ?";
            $params[] = $minPrice;
            $types .= "d"; // 'd' cho decimal
        }
    
        // Điều kiện tìm kiếm theo giá cao nhất
        if ($maxPrice !== null && $maxPrice >= 0) {
            $query .= " AND sp.GiaSP <= ?";
            $params[] = $maxPrice;
            $types .= "d"; // 'd' cho decimal
        }
    
        // Sắp xếp theo giá nếu có yêu cầu
        if ($sortOrder == 'price-asc') {
            $query .= " ORDER BY sp.GiaSP ASC";
        } elseif ($sortOrder == 'price-desc') {
            $query .= " ORDER BY sp.GiaSP DESC";
        }
    
        // Tính toán OFFSET và LIMIT cho phân trang
        $offset = ($page - 1) * $limit;
        $query .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii"; // 'i' cho integer (limit, offset)
    
        // Chuẩn bị câu truy vấn
        $stmt = $this->__conn->prepare($query);
        if ($stmt === false) {
            die("Lỗi trong việc chuẩn bị truy vấn: " . $this->__conn->error);
        }
    
        // Liên kết các tham số vào câu truy vấn
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params); // Liên kết tham số
        }
    
        // Thực thi truy vấn
        $stmt->execute();
    
        // Lấy kết quả từ truy vấn
        $result = $stmt->get_result();
        
        // Mảng lưu kết quả
        $sanpham = [];
        
        // Duyệt qua các dòng kết quả và đưa vào mảng
        while ($row = $result->fetch_assoc()) {
            $sanpham[] = $row;
        }
    
        // Trả về mảng kết quả
        return $sanpham;
    }

    // Hàm để lấy tổng số sản phẩm
    public function getTotalProducts($keyword = '', $MaLoai = NULL, $minPrice = NULL, $maxPrice = NULL) {
        // Xây dựng truy vấn SQL
        $query = "SELECT COUNT(*) FROM sanpham WHERE TenSP LIKE ?";
        
        // Thêm các điều kiện nếu có
        if ($MaLoai !== NULL) {
            $query .= " AND MaLoai = ?";
        }
        if ($minPrice !== NULL) {
            $query .= " AND GiaSP >= ?";
        }
        if ($maxPrice !== NULL) {
            $query .= " AND GiaSP <= ?";
        }

        $stmt = Database::getConnection()->prepare($query);
        
        // Liên kết các tham số vào câu truy vấn
        $params = [];
        $types = "s"; // 's' cho kiểu dữ liệu string (Tên sản phẩm)
        $params[] = "%$keyword%";

        if ($MaLoai !== NULL) {
            $params[] = $MaLoai;
            $types .= "i"; // 'i' cho kiểu integer
        }
        if ($minPrice !== NULL) {
            $params[] = $minPrice;
            $types .= "d"; // 'd' cho kiểu decimal
        }
        if ($maxPrice !== NULL) {
            $params[] = $maxPrice;
            $types .= "d"; // 'd' cho kiểu decimal
        }
        
        // Liên kết các tham số
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        // Lấy kết quả
        $result = $stmt->get_result();
        $row = $result->fetch_row();
        
        return $row[0]; // Trả về tổng số sản phẩm
    }
    

    


}

// Lớp hóa đơn
class HoaDonBUS extends DB_business {
    public function __construct() {
        $this->__conn = Database::getConnection();
        $this->setTable("HoaDon", "MaHD");
    }

    public function checkProductSold($maSP) {
        try {
            $sql = "SELECT * FROM HoaDon WHERE MaSP = ?";
            $stmt = $this->__conn->prepare($sql);
            if (!$stmt) throw new Exception("Prepare failed: " . $this->__conn->error);
            $stmt->bind_param("i", $maSP);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (Exception $e) {
            error_log("Check Product Sold Error: " . $e->getMessage());
            return false;
        }
    }
    
    public function select_by_date_range($fromDate, $toDate) {
        $db = new DB_driver();
        $query = "SELECT hd.*, tt.TenTT as TrangThai 
                  FROM hoadon hd
                  JOIN trangthaidonhang tt ON hd.MaTT = tt.MaTT
                  WHERE DATE(NgayLap) BETWEEN ? AND ?";
        return $db->get_list($query, [$fromDate, $toDate]);
    }
}

// Lớp chi tiết sản phẩm
class ChiTietSanPhamBUS extends DB_business {
    function __construct() {
        $this->__conn = Database::getConnection();
        $this->setTable("ChiTietSanPham", "MaSP");
    }
}

// Lớp người dùng
class NguoiDungBUS extends DB_business {
    function __construct() {
        $this->__conn = Database::getConnection();
        $this->setTable("NguoiDung", "MaND");
    }

    function add_new($data) {
        parent::add_new($data);
    }

    public function getUserById($MaND) {
    $sql = "SELECT * FROM nguoidung WHERE MaND = '$MaND'";
    $result = mysqli_query($this->__conn, $sql);
    return mysqli_fetch_assoc($result);
}
}

// Lớp tài khoản
class TaiKhoanBUS extends DB_business {
    function __construct() {
        $this->__conn = Database::getConnection();
        $this->setTable("TaiKhoan", "TenTaiKhoan");
    }
}

// Lớp phân quyền
class PhanQuyenBUS extends DB_business {
    function __construct() {
        $this->__conn = Database::getConnection();
        $this->setTable("PhanQuyen", "MaQuyen");
    }
}

// Lớp khuyến mãi
class KhuyenMaiBUS extends DB_business {
    function __construct() {
        $this->__conn = Database::getConnection();
        $this->setTable("KhuyenMai", "MaKM");
    }
}

// Lớp thương hiệu
class ThuongHieuBUS extends DB_business {
    function __construct() {
        $this->__conn = Database::getConnection();
        $this->setTable("ThuongHieu", "MaTH");
    }
}

// Lớp loại sản phẩm
class LoaiSanPhamBUS extends DB_business {
    function __construct() {
        $this->__conn = Database::getConnection();
        $this->setTable("LoaiSanPham", "MaLoai");
    }
}

class TrangThaiBUS extends DB_business {
    function __construct() {
        $this->__conn = Database::getConnection();
        $this->setTable("TrangThaiDonHang", "MaTT");
    }
}

// Lớp chi tiết hóa đơn
class ChiTietHoaDonBUS extends DB_business {
    protected $key2;

    function __construct() {
        $this->__conn = Database::getConnection();
        $this->setTable("ChiTietHoaDon", "MaHD");
        $this->key2 = "MaSP";
    }

    function select_list($sql)
{
    return $this->get_list($sql);
}


    function delete_by_2id($id, $id2) {
        $where = $this->_key . "='" . $id . "' AND " . $this->key2 . "='" . $id2 . "'";
        return $this->remove($this->_table_name, $where);
    }

    function update_by_2id($data, $id, $id2) {
        $where = $this->_key . "='" . $id . "' AND " . $this->key2 . "='" . $id2 . "'";
        return $this->update($this->_table_name, $data, $where);
    }

    function select_by_2id($select, $id, $id2) {
        $sql = "SELECT $select FROM " . $this->_table_name . " WHERE " . $this->_key . " = '$id' AND " . $this->key2 . " = '$id2'";
        return $this->get_row($sql);
    }

    function select_all_in_hoadon($id) {
        $sql = "SELECT * FROM " . $this->_table_name . " WHERE " . $this->_key . " = '$id'";
        return $this->get_list($sql);
    }
}

//giỏ hàng
class GioHangBUS extends DB_business {
    function __construct() {
        $this->__conn = Database::getConnection();
        $this->setTable("giohang", "MaGioHang");
    }
    function add_new($data) {
        parent::add_new($data);
    }
}

// Quản trị viên 

class QuanTriBUS extends DB_business {

    function __construct() {
        $this->__conn = Database::getConnection();
        $this->setTable("quan_tri", "Ma_Admin"); // Bảng và khóa chính
    }

    // Nếu có cột TrangThai, cập nhật trạng thái quản trị viên
    public function capNhapTrangThai($trangthai, $maAdmin) {
        $quantri = $this->select_by_id("*", $maAdmin);
        if ($quantri) {
            $quantri["TrangThai"] = $trangthai; // Cột TrangThai cần có
            return $this->update_by_id($quantri, $maAdmin);
        }
        return false;
    }

    //  Lấy 1 quản trị viên theo mã (thêm mới)
    public function select_by_id($columns = "*", $maAdmin) {
        try {
            $sql = "SELECT $columns FROM quan_tri WHERE Ma_Admin = ?";
            $stmt = $this->__conn->prepare($sql);
            if (!$stmt) throw new Exception("Prepare failed: " . $this->__conn->error);
            $stmt->bind_param("s", $maAdmin);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_assoc(); // trả về 1 dòng (mảng kết hợp)
        } catch (Exception $e) {
            error_log("Select Admin By ID Error: " . $e->getMessage());
            return null;
        }
    }

    // Cập nhật trạng thái quản trị viên trực tiếp bằng SQL
    public function updateAdminStatus($maAdmin, $status) {
        try {
            $sql = "UPDATE quan_tri SET TrangThai = ? WHERE Ma_Admin = ?";
            $stmt = $this->__conn->prepare($sql);
            if (!$stmt) throw new Exception("Prepare failed: " . $this->__conn->error);
            $stmt->bind_param("is", $status, $maAdmin);
            $stmt->execute();
            return $stmt->affected_rows > 0;
        } catch (Exception $e) {
            error_log("Update Admin Status Error: " . $e->getMessage());
            return false;
        }
    }

    // Xóa quản trị viên
    public function deleteAdmin($maAdmin) {
        try {
            $sql = "DELETE FROM quan_tri WHERE Ma_Admin = ?";
            $stmt = $this->__conn->prepare($sql);
            if (!$stmt) throw new Exception("Prepare failed: " . $this->__conn->error);
            $stmt->bind_param("s", $maAdmin);
            $stmt->execute();
            return $stmt->affected_rows > 0;
        } catch (Exception $e) {
            error_log("Delete Admin Error: " . $e->getMessage());
            return false;
        }
    }

    // Thêm quản trị viên
    public function insertAdmin($data) {
        try {
            $sql = "INSERT INTO quan_tri (Ma_Admin, Ho_Ten, Email, Mat_Khau, Hinh_Anh, Lien_Lac, Dia_Chi, Chuc_Vu, Gioi_Thieu) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->__conn->prepare($sql);
            if (!$stmt) throw new Exception("Prepare failed: " . $this->__conn->error);
            $stmt->bind_param(
                "sssssssss",
                $data['Ma_Admin'],
                $data['Ho_Ten'],
                $data['Email'],
                $data['Mat_Khau'],
                $data['Hinh_Anh'],
                $data['Lien_Lac'],
                $data['Dia_Chi'],
                $data['Chuc_Vu'],
                $data['Gioi_Thieu']
            );
            $stmt->execute();
            return $stmt->affected_rows > 0;
        } catch (Exception $e) {
            error_log("Insert Admin Error: " . $e->getMessage());
            return false;
        }
    }

    // Cập nhật thông tin quản trị viên
    public function updateAdmin($data, $maAdmin) {
        try {
            $sql = "UPDATE quan_tri SET Ho_Ten=?, Email=?, Mat_Khau=?, Hinh_Anh=?, Lien_Lac=?, Dia_Chi=?, Chuc_Vu=?, Gioi_Thieu=? 
                    WHERE Ma_Admin=?";
            $stmt = $this->__conn->prepare($sql);
            if (!$stmt) throw new Exception("Prepare failed: " . $this->__conn->error);
            $stmt->bind_param(
                "sssssssss",
                $data['Ho_Ten'],
                $data['Email'],
                $data['Mat_Khau'],
                $data['Hinh_Anh'],
                $data['Lien_Lac'],
                $data['Dia_Chi'],
                $data['Chuc_Vu'],
                $data['Gioi_Thieu'],
                $maAdmin
            );
            $stmt->execute();
            return $stmt->affected_rows > 0;
        } catch (Exception $e) {
            error_log("Update Admin Error: " . $e->getMessage());
            return false;
        }
    }
}


 ?>
