<section class="page-hero"><div class="container"><h1>Bảng giá</h1><p class="lead">Nhấn vào từng dòng để xem chi tiết gói.</p></div></section>
<section class="section"><div class="container">
  <div class="table-wrap">
  <table class="price-table">
    <thead><tr><th>Gói</th><th>CPU</th><th>RAM</th><th>SSD</th><th>Giá/tháng</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($PACKAGES as $id => $p): ?>
      <tr data-package="<?= e($id) ?>" tabindex="0">
        <td><span class="badge sm t-<?= e($id) ?>"><?= e(substr($p['name'], 0, 1)) ?></span> <b><?= e($p['name']) ?></b></td>
        <td><?= e($p['specs']['CPU']) ?></td><td><?= e($p['specs']['RAM']) ?></td><td><?= e($p['specs']['SSD']) ?></td>
        <td class="price-cell"><?= vnd($p['price']) ?></td>
        <td class="row-actions">
          <span class="link">Chi tiết</span>
          <a class="btn btn-sm" href="index.php?page=dang-ky-goi&package=<?= e($id) ?>">Đăng ký</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <p class="muted note">* Giá chưa bao gồm VAT 10%. Thanh toán theo năm giảm 20%.</p>
</div></section>