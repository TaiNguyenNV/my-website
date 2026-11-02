</main>
<footer class="footer">
  <div class="container foot-grid">
    <div><div class="logo"><?= e(SITE_NAME) ?></div><p class="muted">Hạ tầng cloud nhanh, ổn định, giá hợp lý.</p></div>
    <div>
      <h4>Liên kết</h4>
      <?php foreach ($PAGES as $s => $l):
        $need = page_perm($s);
        if ($s !== 'dang-ky-goi' && $need && !can($need)) continue;
      ?>
        <a href="index.php?page=<?= $s ?>"><?= e($l) ?></a>
      <?php endforeach; ?>
      <a href="<?= e(main_home_url()) ?>">← Quay lại trang chính</a>
    </div>
    <div><h4>Liên hệ</h4><p class="muted">support@nhom15.vn<br>1900 1515<br>Hỗ trợ 24/7</p></div>
  </div>
  <div class="copy">© <?= date('Y') ?> <?= e(SITE_NAME) ?>. Bài tập Nhóm 15.</div>
</footer>
<?php include __DIR__ . '/modals.php'; ?>
<script>window.PACKAGES = <?= json_encode($PACKAGES, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>;</script>
<script src="<?= ASSET_JS ?>"></script>
</body>
</html>