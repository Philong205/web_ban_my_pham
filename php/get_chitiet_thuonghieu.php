<?php
$conn = new mysqli("localhost","root","","web2");
$Ma = $_GET['MaTH'] ?? '';
$res = $conn->query("SELECT * FROM thuonghieu WHERE MaTH = $Ma");

echo json_encode($res->fetch_assoc());
?>
