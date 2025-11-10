<?php
// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "web2"); // sửa lại nếu cần

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý tìm kiếm (nếu có)
$type = $_GET['type'] ?? '';
$value = $_GET['value'] ?? '';
$allowedFields = ['TenND', 'Email', 'TaiKhoan']; // chỉ cho phép 3 trường này
$whereClause = '';

if (in_array($type, $allowedFields) && !empty($value)) {
    $safeValue = $conn->real_escape_string($value);
    $whereClause = "WHERE $type LIKE '%$safeValue%'";
}

// Truy vấn lấy danh sách người dùng
$sql = "SELECT * FROM nguoidung $whereClause";  // Đảm bảo bảng 'nguoidung' tồn tại trong SQL
$result = $conn->query($sql);

// Hiển thị từng dòng
$stt = 1;
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $stt++ . "</td>";
        echo "<td>" . htmlspecialchars($row['TenND'], ENT_QUOTES) . "</td>";
        echo "<td>" . htmlspecialchars($row['Email'], ENT_QUOTES) . "</td>";
        echo "<td>" . htmlspecialchars($row['TaiKhoan'], ENT_QUOTES) . "</td>";
        echo "<td>" . htmlspecialchars($row['MatKhau'], ENT_QUOTES) . "</td>";
        echo "<td>";
        
        // Nút Khóa hoặc Mở khóa
        if ($row['TrangThai'] == 0) {
            echo "<a href='..\php\mokhoa_nguoidung.php?id={$row['MaND']}' 
                      class='btn btn-secondary'
                      onclick='return confirm(\"Bạn có chắc muốn mở khóa người dùng này không?\")'>
                      Mở khóa
                  </a> " . "      ";
        } else {
            echo "<a href='..\php\khoa_nguoidung.php?id={$row['MaND']}' 
                      class='btn delete-btn btn-danger' 
                      onclick='return confirm(\"Bạn có chắc muốn khóa người dùng này không?\")'>
                      Khóa
                  </a> ". "      ";
        }

        // Nút Sửa
        echo "<button class='btn edit-btn btn-success' onclick=\"moModalSua(
            '" . $row['MaND'] . "',
            '" . htmlspecialchars($row['TenND'], ENT_QUOTES) . "',
            '" . htmlspecialchars($row['Email'], ENT_QUOTES) . "',
            '" . htmlspecialchars($row['TaiKhoan'], ENT_QUOTES) . "',
            '" . htmlspecialchars($row['MatKhau'], ENT_QUOTES) . "',
            '" . htmlspecialchars($row['SDT'], ENT_QUOTES) . "',
            '" . htmlspecialchars($row['DiaChi'], ENT_QUOTES) . "'
        ); document.getElementById('khungSuaNguoiDung').style.transform = 'scale(1)';\">Sửa</button>";

        echo "</td></tr>";
    }
} else {
    echo "<tr><td colspan='6'>Không có người dùng nào.</td></tr>";
}

$conn->close();
?>
