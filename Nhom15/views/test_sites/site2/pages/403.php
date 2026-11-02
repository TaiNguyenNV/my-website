<section class="page-hero"><div class="container"><h1>Không đủ quyền truy cập</h1><p class="lead">Tài khoản của bạn không được phép xem trang này.</p></div></section>
<section class="section"><div class="container">
  <div class="card denied">
    <div class="denied-code">403</div>
    <p>Trang <b><?= e($PAGES[$page]) ?></b> yêu cầu quyền cao hơn vai trò hiện tại của bạn
       (<b><?= e(role()) ?></b>). Nếu cần truy cập, hãy liên hệ quản trị viên.</p>
    <div class="modal-actions" style="justify-content:center">
      <a class="btn" href="index.php">Về trang chủ site2</a>
      <a class="btn btn-line" href="<?= e(main_home_url()) ?>">← Quay lại trang chính</a>
    </div>
  </div>
</div></section>