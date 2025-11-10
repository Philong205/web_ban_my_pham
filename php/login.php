<?php

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once ('../BackEnd/ConnectionDB/DB_driver.php');
require_once ('../BackEnd/ConnectionDB/DB_classes.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Vui lòng nhập đầy đủ email và mật khẩu!";
        header("Location: ../user/index.php");
        exit();
    }

    $db = new DB_driver();
    $conn = $db->get_connection();

    // Bảo vệ chống SQL Injection
    $email = mysqli_real_escape_string($conn, $email);

    $sql = "SELECT * FROM nguoidung WHERE Email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if ($user['TrangThai'] == 0) {
            $_SESSION['login_error'] = "Tài khoản của bạn đã bị khóa!";
            $error_message = "Tài khoản của bạn đã bị khóa!";
        } elseif ($password === $user['MatKhau']) {
            $_SESSION['user'] = [
                'id' => $user['MaND'],
                'email' => $user['Email'],
                'name' => $user['TenND'],
                'SDT' => $user['SDT'],
                'DiaChi' => $user['DiaChi']
            ];
            header("Location: ../user/index.php");
            exit();
        } else {
            $_SESSION['login_error'] = "Email hoặc mật khẩu không đúng!";
        }
    } else {
        $_SESSION['login_error'] = "Tài khoản không tồn tại!";
    }
    // Nếu có thông báo lỗi, hiển thị alert
    if (isset($error_message)) {
        echo "<script>alert('$error_message'); window.location.href = '../user/index.php';</script>";
        exit();
    }
    // header("Location: ../user/index.php");
    // exit();
}
?>
