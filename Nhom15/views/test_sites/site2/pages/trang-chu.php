<section class="hero">
  <div class="container hero-grid">
    <div>
      <span class="pill"><i class="dot"></i> Cloud NVMe thế hệ mới</span>
      <h1>Hạ tầng Cloud <em>ổn định</em> cho mọi dự án</h1>
      <p class="lead">Triển khai website và ứng dụng trong vài phút với uptime 99.9%, bảo mật nhiều lớp và hỗ trợ kỹ thuật 24/7.</p>
      <div class="hero-btns">
        <a class="btn" href="index.php?page=dang-ky-goi">Đăng ký ngay</a>
        <a class="btn btn-line" href="#goi">Xem các gói</a>
      </div>
      <div class="stats">
        <div><b>99.9%</b><span>Uptime</span></div>
        <div><b>10K+</b><span>Khách hàng</span></div>
        <div><b>24/7</b><span>Hỗ trợ</span></div>
      </div>
    </div>
    <div class="hero-visual" aria-hidden="true">
      <div class="mock">
        <div class="mock-head"><span class="badge-on">Đang hoạt động</span><small>srv-hn-01</small></div>
        <div class="meter"><label>CPU <b>32%</b></label><div><i style="width:32%"></i></div></div>
        <div class="meter"><label>RAM <b>48%</b></label><div><i style="width:48%"></i></div></div>
        <div class="meter"><label>Ổ đĩa NVMe <b>21%</b></label><div><i style="width:21%"></i></div></div>
        <div class="mock-foot"><span>Backup gần nhất</span><b>Hôm nay 03:00</b></div>
      </div>
      <div class="chip c1">SSL đã bật</div>
      <div class="chip c2">Chống DDoS</div>
    </div>
  </div>
</section>

<section class="section" id="goi">
  <div class="container">
    <h2 class="title">Chọn gói phù hợp với bạn</h2>
    <p class="sub">Nhấn vào bất kỳ gói nào để xem chi tiết, hoặc đăng ký ngay.</p>
    <div class="grid-4">
      <?php foreach ($PACKAGES as $id => $p): ?>
      <article class="card pkg <?= !empty($p['hot']) ? 'hot' : '' ?>" data-package="<?= e($id) ?>" tabindex="0" role="button">
        <?php if (!empty($p['hot'])): ?><span class="ribbon">Phổ biến</span><?php endif; ?>
        <span class="badge t-<?= e($id) ?>"><?= e(substr($p['name'], 0, 1)) ?></span>
        <h3><?= e($p['name']) ?></h3>
        <p class="muted"><?= e($p['tag']) ?></p>
        <div class="price"><?= vnd($p['price']) ?><small>/tháng</small></div>
        <ul class="check">
          <?php foreach (array_slice($p['specs'], 0, 3) as $k => $v): ?><li><?= e($k) ?>: <b><?= e($v) ?></b></li><?php endforeach; ?>
        </ul>
        <span class="more">Xem chi tiết →</span>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section alt">
  <div class="container">
    <h2 class="title">Vì sao chọn chúng tôi?</h2>
    <p class="sub">Những điều khách hàng quan tâm nhất, chúng tôi làm tốt nhất.</p>
    <div class="grid-3">
      <div class="card feat">
        <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L4 14h7l-1 8 9-12h-7z"/></svg></span>
        <h3>Tốc độ vượt trội</h3><p class="muted">Ổ cứng NVMe và mạng nội bộ 10Gbps giúp website tải rất nhanh.</p>
      </div>
      <div class="card feat">
        <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/></svg></span>
        <h3>Bảo mật nhiều lớp</h3><p class="muted">Tường lửa, chống DDoS, SSL miễn phí và backup tự động.</p>
      </div>
      <div class="card feat">
        <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 13v-1a8 8 0 0116 0v1M4 13h3v5H5a1 1 0 01-1-1zM20 13h-3v5h2a1 1 0 001-1z"/></svg></span>
        <h3>Hỗ trợ tận tâm</h3><p class="muted">Đội ngũ kỹ sư trực 24/7, phản hồi trong vài phút.</p>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <h2 class="title">Bắt đầu chỉ với 3 bước</h2>
    <p class="sub">Đăng ký nhanh bằng tài khoản của bạn trên trang chính.</p>
    <div class="steps">
      <div class="step"><span>1</span><h3>Chọn gói</h3><p class="muted">Chọn cấu hình và chu kỳ thanh toán phù hợp.</p></div>
      <div class="step"><span>2</span><h3>Điền thông tin &amp; thanh toán</h3><p class="muted">Chuyển khoản, ví điện tử hoặc thẻ.</p></div>
      <div class="step"><span>3</span><h3>Kích hoạt</h3><p class="muted">Dịch vụ được cấp trong vòng 5 phút.</p></div>
    </div>
  </div>
</section>

<section class="cta">
  <div class="container">
    <h2>Sẵn sàng triển khai dự án?</h2>
    <p>Hoàn tiền 100% trong 7 ngày đầu nếu bạn chưa hài lòng.</p>
    <a class="btn btn-white" href="index.php?page=dang-ky-goi">Đăng ký gói ngay</a>
  </div>
</section>