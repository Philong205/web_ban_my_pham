<?php
$conn = new mysqli("localhost","root","","web2");
$Ma = $_GET['MaTH'] ?? "";
if ($Ma != "") {
    $conn->query("DELETE FROM thuonghieu WHERE MaTH = $Ma");
}
header("Location: ../admin/admin.php?page=thuonghieu");
?>
