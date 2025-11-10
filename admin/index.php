<?php
session_start();

// Kết nối database
$conn = new mysqli("localhost", "root", "", "web2");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$error = "";

// Nếu form được submit
if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email && $password) {
        $stmt = $conn->prepare("SELECT * FROM quan_tri WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        // Kiểm tra Email + Mật khẩu
        if ($admin && $password === $admin['Mat_Khau']) { 
            // Nếu dùng hash mật khẩu, thay bằng: password_verify($password, $admin['Mat_Khau'])
            $_SESSION['admin'] = [
                'Ma_Admin' => $admin['Ma_Admin'],
                'Ho_Ten'   => $admin['Ho_Ten'],
                'Email'    => $admin['Email'],
                'Hinh_Anh' => $admin['Hinh_Anh']
            ];
            header("Location: admin.php?page=home");
            exit;
        } else {
            // Thông báo chung, không tiết lộ email có tồn tại hay không
            $error = "Email hoặc mật khẩu không đúng!";
        }
    } else {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="../css/admin.css" />
<title>Eden Beauty</title>
</head>
<body>
<section>
  <div class="form-box">
    <div class="form-value">
      <form method="POST" action="">
        <h2>Đăng nhập</h2>
        <div class="inputbox">
          <ion-icon name="mail-outline"></ion-icon>
          <input type="email" name="email" required />
          <label>Email</label>
        </div>
        <div class="inputbox">
          <ion-icon name="lock-closed-outline"></ion-icon>
          <input type="password" name="password" required />
          <label>Password</label>
        </div>
        <div class="forget">
          <label><a href="#">Quên mật khẩu</a></label>
        </div>
        <button type="submit" name="login">LOG IN</button>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
      </form>
    </div>
  </div>
</section>

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
