<?php
require_once "../php/connect.php";

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

if (empty($from) || empty($to)) {
    echo "<p style='color:red'>Vui lòng chọn đầy đủ ngày bắt đầu và kết thúc.</p>";
    exit;
}

$from_date = date('Y-m-d', strtotime($from));
$to_date = date('Y-m-d', strtotime($to));

$sql = "
SELECT nd.MaND, nd.TenND, nd.Email, SUM(hd.TongTien) AS TongMua
FROM hoadon hd
JOIN nguoidung nd ON hd.MaND = nd.MaND
WHERE hd.NgayLap BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59'
GROUP BY nd.MaND
ORDER BY TongMua DESC
LIMIT 5
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<h3>" . htmlspecialchars($row['TenND']) . " (" . $row['Email'] . ") - Tổng mua: " . number_format($row['TongMua'], 0, ',', '.') . "đ</h3>";

        // Truy vấn đơn hàng từng khách
        $mand = $row['MaND'];
        $sql_dh = "
            SELECT MaHD, NgayLap, TongTien 
            FROM hoadon 
            WHERE MaND = '$mand' AND NgayLap BETWEEN '$from_date 00:00:00' AND '$to_date 23:59:59'
        ";
        $donhangs = $conn->query($sql_dh);
        if ($donhangs->num_rows > 0) {
            echo "<ul>";
            while ($dh = $donhangs->fetch_assoc()) {
                echo "<li><a href='chitiet_donhang.php?mahd=" . $dh['MaHD'] . "' target='_blank'>Đơn #" . $dh['MaHD'] . "</a> - " . $dh['NgayLap'] . " - " . number_format($dh['TongTien'], 0, ',', '.') . "đ</li>";
            }
            echo "</ul>";
        }
    }
} else {
    echo "<p>Không có dữ liệu trong khoảng thời gian này.</p>";
}

$conn->close();
?>
