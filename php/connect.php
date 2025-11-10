<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "web2chinh.sql"; 
$conn = new mysqli("localhost", "root", "", "web2");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
