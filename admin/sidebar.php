

<?php
// Đảm bảo đã có session
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
?>

<div class="sidebar">
  <div class="eden-name">
    <img src="../image/admin/link-hinh-logo.jpg" alt="Eden Beauty" />
    <h1>Admin</h1>
  </div>

  <ul class="nav">
    <li style="list-style: none;" class="nav-title">MENU</li>

    <li class="nav-item">
      <a href="?page=home" class="nav-link">
        <i class="fa fa-home"></i> Trang Chủ
      </a>
    </li>
    <li class="nav-item">
      <a href="?page=sanpham" class="nav-link">
        <i class="fa fa-th-large"></i> Sản Phẩm
      </a>
    </li>
    <li class="nav-item">
      <a href="?page=donhang" class="nav-link">
        <i class="fa fa-file-text-o"></i> Đơn Hàng
      </a>
    </li>
    <li class="nav-item">
      <a href="?page=khachhang" class="nav-link">
        <i class="fa fa-address-book-o"></i> Khách Hàng
      </a>
    </li>

    <!-- Hiển thị mục Quản trị viên CHỈ khi chức vụ là Quản trị viên -->
    <?php if (isset($_SESSION['Chuc_Vu']) && $_SESSION['Chuc_Vu'] === 'Quản trị viên'): ?>
      <li class="nav-item">
        <a href="?page=quantrivien" class="nav-link">
          <i class="fa fa-users"></i> Quản Trị Viên
        </a>
      </li>
    <?php endif; ?>

    <li class="nav-item"><hr /></li>
    <li class="nav-item">
      <a href="../php/logout.php" class="nav-link" onclick="logOutAdmin(); return true;">
        <i class="fa fa-arrow-left"></i> Đăng xuất
      </a>
    </li>
  </ul>
</div>
