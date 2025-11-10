<?php
require_once "../php/connect.php";

$type = $_POST['type'] ?? '';
$query = $_POST['query'] ?? '';

$columnMap = [
    "ten" => "TenND",
    "email" => "Email",
    "taikhoan" => "TaiKhoan"
];

$column = $columnMap[$type] ?? "TenND";

// Bảo vệ khỏi SQL Injection
$sql = "SELECT * FROM nguoidung WHERE $column LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $query . "%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$stt = 1;
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$stt}</td>";
    echo "<td>{$row['TenND']}</td>";
    echo "<td>{$row['Email']}</td>";
    echo "<td>{$row['TaiKhoan']}</td>";
    echo "<td>{$row['MatKhau']}</td>";
    echo "<td>";
        // Nút Khóa hoặc Đã khóa
        if ($row['TrangThai'] == 0) {
            echo "<a href='mokhoa_nguoidung.php?id={$row['MaND']}' 
                      class='btn btn-secondary' 
                      onclick='return confirm(\"Bạn có chắc muốn mở khóa người dùng này không?\")'>
                      Mở khóa
                  </a>";
        } else {
            echo "<a href='khoa_nguoidung.php?id={$row['MaND']}' 
                      class='btn btn-danger' 
                      onclick='return confirm(\"Bạn có chắc muốn khóa người dùng này không?\")'>
                      Khóa
                  </a>";
        }
        // Nút Sửa
        echo "<button class='btn btn-success' onclick=\"moModalSua(
            '" . $row['MaND'] . "',
            '" . htmlspecialchars($row['TenND'], ENT_QUOTES) . "',
            '" . htmlspecialchars($row['Email'], ENT_QUOTES) . "',
            '" . htmlspecialchars($row['TaiKhoan'], ENT_QUOTES) . "',
            '" . htmlspecialchars($row['MatKhau'], ENT_QUOTES) . "',
            '" . htmlspecialchars($row['SDT'], ENT_QUOTES) . "',
            '" . htmlspecialchars($row['DiaChi'], ENT_QUOTES) . "',

        )\">Sửa</button>";

        echo "</td>";
    $stt++;
}
?>
