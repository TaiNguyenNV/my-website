<footer class="footer">
  <div class="container foot-grid">
    <div>
      <div class="logo"><?= e(SITE_NAME) ?></div>
      <p class="muted">Hạ tầng cloud nhanh, ổn định, giá hợp lý.</p>
    </div>
    <div>
      <h4>Liên kết</h4>
      <?php foreach ($PAGES as $s => $l):
        $need = page_perm($s);
        if ($s !== 'dang-ky-goi' && $need && !can($need))
          continue;
        ?>
        <a href="index.php?page=<?= $s ?>"><?= e($l) ?></a>
      <?php endforeach; ?>
      <a href="<?= e(main_home_url()) ?>">← Quay lại trang chính</a>
    </div>
    <div>
      <h4>Liên hệ</h4>
      <p class="muted">support@nhom15.vn<br>1900 1515<br>Hỗ trợ 24/7</p>
    </div>
  </div>
  <div class="copy">© <?= date('Y') ?> <?= e(SITE_NAME) ?>. Bài tập Nhóm 15.</div>
</footer>
<?php include __DIR__ . '/modals.php'; ?>
<script>window.PACKAGES = <?= json_encode($PACKAGES, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>;</script>
<script src="<?= ASSET_JS ?>"></script>
<script src="../../../js/tracker.js"></script>
<script>
  if (window.WebTracker) {
    window.WebTracker.init({
      websiteId: 2,
      apiEndpoint: '../../../handle/api_track.php'
    });
  }
</script>

<!-- Script xử lý sự kiện nhấn đúp (dblclick) đã tối ưu async/await -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const packageItems = document.querySelectorAll('[data-package]');

    packageItems.forEach(item => {
      item.addEventListener('dblclick', async function (e) {
        e.preventDefault();
        const trackLabel = this.getAttribute('data-track-label') || 'Xem chi tiết';
        const eventType = this.getAttribute('data-event') || 'view_detail';
        const packageId = this.getAttribute('data-package');

        // Gửi request ngầm và ĐỢI server ghi nhận xong xuôi rồi mới chuyển trang
        try {
          await fetch('index.php?action=track', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
              'type': eventType,
              'detail': trackLabel
            })
          });
        } catch (error) {
          console.error('Lỗi tracking:', error);
        }

        // Chuyển hướng sang trang đăng ký gói tương ứng
        window.location.href = `index.php?page=dang-ky-goi&package=${packageId}`;
      });
    });
  });
</script>
</body>

</html>