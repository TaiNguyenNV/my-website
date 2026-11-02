<!-- Cửa sổ chi tiết gói -->
<div class="modal" id="pkgModal" aria-hidden="true">
  <div class="modal-box" role="dialog" aria-modal="true">
    <button class="modal-close" data-close aria-label="Đóng">✕</button>

    <div class="tabs" id="mTabs">
      <?php foreach ($PACKAGES as $id => $p): ?>
        <button type="button" data-tab="<?= e($id) ?>"><?= e($p['name']) ?></button>
      <?php endforeach; ?>
    </div>

    <div class="modal-top">
      <div>
        <h3 id="mName"></h3>
        <p id="mTag" class="muted"></p>
      </div>
      <div class="modal-price"><strong id="mPrice"></strong><span id="mPer">/tháng</span></div>
    </div>
    <p id="mDesc" class="mt"></p>

    <div class="switch">
      <button type="button" class="on" data-cycle="1">Theo tháng</button>
      <button type="button" data-cycle="12">Theo năm (-20%)</button>
    </div>
    <p class="save" id="mSave"></p>

    <div class="modal-cols">
      <div><h4>Thông số</h4><table class="spec" id="mSpecs"></table></div>
      <div><h4>Tính năng</h4><ul class="check" id="mFeatures"></ul></div>
    </div>

    <div class="best"><span>Phù hợp với</span><b id="mBest"></b></div>

    <div class="modal-actions">
      <a class="btn" id="mBuy" href="index.php?page=dang-ky-goi">Đăng ký gói này</a>
      <a class="btn btn-line" id="mConsult" href="index.php?page=ho-tro-ky-thuat#lien-he">Nhận tư vấn</a>
      <button type="button" class="btn btn-line" data-close>Đóng</button>
    </div>
  </div>
</div>