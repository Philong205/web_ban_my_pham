<?php
// Thư viện xử lý Database
class DB_driver
{
    // thêm cho user
    protected $conn;//26
    public function get_connection() {
        return $this->__conn;
    }

    public function get_last_insert_id() {
        $this->connect();
        $result = mysqli_query($this->__conn, "SELECT LAST_INSERT_ID() AS id");
        $row = mysqli_fetch_assoc($result);
        return $row['id'];
    }

    // Thêm các phương thức transaction mới
    public function beginTransaction() {
        $this->connect();
        return mysqli_begin_transaction($this->__conn);
    }

    public function commit() {
        $this->connect();
        return mysqli_commit($this->__conn);
    }

    public function rollback() {
        $this->connect();
        return mysqli_rollback($this->__conn);
    }

    // Phương thức kiểm tra transaction có đang hoạt động
    public function inTransaction() {
        $this->connect();
        return mysqli_autocommit($this->__conn, false);
    }
    
    // Thực thi câu lệnh SQL (INSERT, UPDATE, DELETE)
    public function execute($sql)
    {
        $this->connect(); // Kết nối đến CSDL nếu chưa kết nối

        if (mysqli_query($this->__conn, $sql)) {
            return true; // Trả về true nếu thực thi thành công
        } else {
            return false; // Trả về false nếu có lỗi
        }
    }
    // Biến kết nối và thông tin cấu hình
    protected $__conn = null;
    protected $host = "localhost";
    protected $DbName = "web2";  // Tên cơ sở dữ liệu
    protected $user = "root";    // Tên người dùng
    protected $pass = "";        // Mật khẩu

    // Constructor: tự động kết nối khi khởi tạo đối tượng
    public function __construct() {
        $this->connect();
    }

    // Kết nối đến CSDL
    public function connect()
{
    if (!$this->__conn) {
        // Nếu đã có connection từ Database::getConnection() thì lấy luôn
        $this->__conn = Database::getConnection();

        // Nếu vẫn chưa có connection (hoặc muốn tự kết nối), thì tự new mysqli
        if (!$this->__conn) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Báo lỗi chi tiết
            $this->__conn = new mysqli($this->host, $this->user, $this->pass, $this->DbName);

            if ($this->__conn->connect_error) {
                die('Kết nối thất bại: ' . $this->__conn->connect_error);
            }

            $this->__conn->set_charset("utf8");
        }
    }
}

   // Ngắt kết nối CSDL
   public function dis_connect()
    {
        $this->__conn = null;
    }

    // Destructor: tự động "ngắt" khi huỷ đối tượng
    public function __destruct()
    {
        $this->dis_connect();
    }

    // Thêm mới bản ghi
    public function insert($table, $data)
    {
        $this->connect();

        $fields = array_keys($data);
        $values = array_map(function($value) {
            return "'" . mysqli_real_escape_string($this->__conn, $value) . "'";
        }, array_values($data));

        $sql = "INSERT INTO $table (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";

        return mysqli_query($this->__conn, $sql);
    }

    // Cập nhật bản ghi
    public function update($table, $data, $where)
    {
        $this->connect();

        $updates = [];
        foreach ($data as $key => $value) {
            $updates[] = "$key = '" . mysqli_real_escape_string($this->__conn, $value) . "'";
        }

        $sql = "UPDATE $table SET " . implode(', ', $updates) . " WHERE $where";

        return mysqli_query($this->__conn, $sql);
    }

    // Xóa bản ghi
    public function remove($table, $where)
    {
        $this->connect();

        $sql = "DELETE FROM $table WHERE $where";

        return mysqli_query($this->__conn, $sql);
    }

    // Xóa bản ghi
public function delete($table, $where, $params = [])
{
    $this->connect(); // Đảm bảo đã kết nối

    // Tạo câu lệnh SQL xóa với tham số thay thế
    $sql = "DELETE FROM $table WHERE $where";

    // Nếu có tham số, thực thi với tham số
    if (!empty($params)) {
        // Chuẩn bị câu lệnh với tham số thay thế
        $stmt = $this->__conn->prepare($sql);
        if ($stmt === false) {
            return false; // Nếu có lỗi khi chuẩn bị câu lệnh
        }
        
        // Liên kết tham số (ví dụ: s - string, i - integer, v.v.)
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);

        // Thực thi câu lệnh
        return $stmt->execute();
    } else {
        // Nếu không có tham số, thực thi trực tiếp câu lệnh SQL
        return mysqli_query($this->__conn, $sql);
    }
}

    // Lấy danh sách nhiều dòng
    public function get_list($sql)
    {
        $this->connect();

        $result = mysqli_query($this->__conn, $sql);

        if (!$result) {
            die('Lỗi truy vấn: ' . mysqli_error($this->__conn));
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        mysqli_free_result($result);

        return $data;
    }

    // Lấy 1 dòng
    public function get_row($sql)
    {
        $this->connect();

        $result = mysqli_query($this->__conn, $sql);

        if (!$result) {
            die('Lỗi truy vấn: ' . mysqli_error($this->__conn));
        }

        $row = mysqli_fetch_assoc($result);

        mysqli_free_result($result);

        return $row ?: false;
    }

    // Lấy danh sách thương hiệu (ví dụ)
    public function get_thuonghieu()
    {
        return $this->get_list("SELECT MaTH, TenTH FROM thuonghieu");
    }

    // Lấy danh sách loại sản phẩm (ví dụ)
    public function get_loaisanpham()
    {
        return $this->get_list("SELECT MaLoai, TenLoai FROM loaisanpham");
    }
}
?>
