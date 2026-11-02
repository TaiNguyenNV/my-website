<?php
$docs = doc_list();
usort($docs, function ($a, $b) { return strcmp($a['name'], $b['name']); });
?>
<section class="page-hero"><div class="container">
  <h1>Tải dữ liệu</h1>
  <p class="lead">Tải bảng giá, dữ liệu đơn hàng và tài liệu hướng dẫn về máy.</p>
</div></section>

<section class="section"><div class="container">
  <h2>Xuất dữ liệu (CSV)</h2>
  <div class="dl-grid">
    <div class="card dl-card">
      <h3>Bảng giá các gói</h3>
      <p class="muted">Thông số và giá từng gói Cloud. Mở được bằng Excel.</p>
      <a class="btn btn-sm" href="index.php?action=download&type=bang-gia" data-track="download_bang-gia">Tải bảng giá</a>
    </div>

    <div class="card dl-card">
      <h3>Đơn hàng</h3>
      <?php if (can('export.orders')): ?>
        <p class="muted"><?= can('order.view_all') ? 'Toàn bộ đơn hàng trên site2.' : 'Các đơn hàng của bạn.' ?></p>
        <a class="btn btn-sm" href="index.php?action=download&type=don-hang" data-track="download_don-hang">Tải đơn hàng</a>
      <?php else: ?>
        <p class="muted">Đăng nhập để tải dữ liệu đơn hàng của bạn.</p>
        <a class="btn btn-sm btn-line" href="<?= e(main_login_url($_SERVER['REQUEST_URI'] ?? '')) ?>">Đăng nhập</a>
      <?php endif; ?>
    </div>

    <?php if (can('export.log')): ?>
    <div class="card dl-card">
      <h3>Nhật ký hệ thống</h3>
      <p class="muted">Toàn bộ hoạt động đã ghi nhận (chỉ quản trị viên).</p>
      <a class="btn btn-sm" href="index.php?action=download&type=nhat-ky" data-track="download_nhat-ky">Tải nhật ký</a>
    </div>
    <?php endif; ?>
  </div>

  <h2 class="mt">Tài liệu</h2>
  <?php if (!current_user()): ?>
    <div class="card center">
      <p class="muted">Vui lòng đăng nhập để tải tài liệu.</p>
      <a class="btn mt" href="<?= e(main_login_url($_SERVER['REQUEST_URI'] ?? '')) ?>">Đăng nhập</a>
    </div>
  <?php elseif (!$docs): ?>
    <div class="card center"><p class="muted">Chưa có tài liệu nào. Đặt file vào thư mục <code>site2/downloads/</code> để hiển thị ở đây.</p></div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="price-table">
        <thead><tr><th>Tên file</th><th>Dung lượng</th><th>Cập nhật</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($docs as $d): ?>
          <tr>
            <td><b><?= e($d['name']) ?></b></td>
            <td><?= e(fmt_size($d['size'])) ?></td>
            <td><?= e(date('d/m/Y H:i', $d['time'])) ?></td>
            <td class="row-actions">
              <?php if (strtolower(pathinfo($d['name'], PATHINFO_EXTENSION)) === 'txt'): ?>
                <a class="btn btn-sm btn-line" target="_blank" rel="noopener" href="index.php?action=download&type=tai-lieu&view=1&file=<?= urlencode($d['name']) ?>">Xem</a>
              <?php endif; ?>
              <a class="btn btn-sm" href="index.php?action=download&type=tai-lieu&file=<?= urlencode($d['name']) ?>">Tải xuống</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div></section>