<?php
// $conn = new mysqli("localhost","root","","web2");
// if($conn->connect_error) die("Kết nối thất bại: ".$conn->connect_error);

// $TenKM = $_POST['TenKM'] ?? '';
// $GiaTriKM = $_POST['GiaTriKM'] ?? 0;
// $NgayBD = $_POST['NgayBD'] ?? '';
// $NgayKT = $_POST['NgayKT'] ?? '';

// // Tạo mã người dùng dựa trên số lượng hiện tại
// $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM khuyenmai");
// $row = mysqli_fetch_assoc($result);
// $MaKM = $row['total'] + 1; // ví dụ: nếu có 8 người dùng, mã mới = 9


// $stmt = $conn->prepare("INSERT INTO khuyenmai (TenKM,GiaTriKM,NgayBD,NgayKT) VALUES (?,?,?,?)");
// $stmt->bind_param("sdss",$TenKM,$GiaTriKM,$NgayBD,$NgayKT);
// if($stmt->execute()) header("Location: ../admin/admin.php?page=khuyenmai");
// else echo "❌ Lỗi thêm: ".$stmt->error;

// $stmt->close();
// $conn->close();
?>

<?php
$conn = new mysqli("localhost","root","","web2");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$TenKM    = $_POST['TenKM'] ?? '';
$GiaTriKM = $_POST['GiaTriKM'] ?? 0;
$NgayBD   = $_POST['NgayBD'] ?? '';
$NgayKT   = $_POST['NgayKT'] ?? '';

// Lấy số lượng hiện tại
$result = $conn->query("SELECT COUNT(*) AS total FROM khuyenmai");
$row = $result->fetch_assoc();
$MaKM = $row['total'] + 1;

// Kiểm tra xem mã này đã tồn tại chưa
$check = $conn->query("SELECT MaKM FROM khuyenmai WHERE MaKM = '$MaKM'");
if ($check->num_rows > 0) {
    // Nếu tồn tại → tìm mã lớn nhất rồi +1
    $maxRow = $conn->query("SELECT MAX(MaKM) AS max_id FROM khuyenmai")->fetch_assoc();
    $MaKM = $maxRow['max_id'] + 1;
}

// Thêm dữ liệu
$stmt = $conn->prepare("INSERT INTO khuyenmai (MaKM, TenKM, GiaTriKM, NgayBD, NgayKT) VALUES (?,?,?,?,?)");
$stmt->bind_param("isdss", $MaKM, $TenKM, $GiaTriKM, $NgayBD, $NgayKT);

if ($stmt->execute()) {
    header("Location: ../admin/admin.php?page=khuyenmai");
    exit();
} else {
    echo "❌ Lỗi thêm: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

