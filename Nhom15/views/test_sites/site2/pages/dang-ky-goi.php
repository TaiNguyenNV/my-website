<?php
$me = current_user();
$methods = [
    'bank'   => ['Chuyển khoản ngân hàng', 'Kích hoạt sau khi nhận được tiền'],
    'wallet' => ['Ví MoMo / ZaloPay', 'Quét mã QR để thanh toán'],
    'card'   => ['Thẻ Visa / Mastercard', 'Thanh toán qua cổng bảo mật'],
    'later'  => ['Thanh toán sau', 'Nhân viên liên hệ và gửi hóa đơn'],
];

$in = [
    'package' => $_GET['package'] ?? 'business',
    'cycle'   => (int)($_GET['cycle'] ?? 1),
    'name' => $me['name'] ?? '', 'email' => $me['email'] ?? '',
    'phone' => '', 'company' => '', 'note' => '', 'method' => 'bank',
];
$errors = [];
$order = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'order') {
    foreach (['package', 'name', 'email', 'phone', 'company', 'note', 'method'] as $k) { $in[$k] = trim($_POST[$k] ?? ''); }
    $in['cycle'] = (int)($_POST['cycle'] ?? 1);

    if (!isset($PACKAGES[$in['package']])) $errors[] = 'Vui lòng chọn gói dịch vụ.';
    if (!in_array($in['cycle'], [1, 12], true)) $errors[] = 'Chu kỳ thanh toán không hợp lệ.';
    if ($in['name'] === '') $errors[] = 'Vui lòng nhập họ tên.';
    if (!filter_var($in['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Email chưa đúng định dạng.';
    if (!preg_match('/^[0-9 +().-]{9,15}$/', $in['phone'])) $errors[] = 'Số điện thoại chưa hợp lệ.';
    if (!isset($methods[$in['method']])) $errors[] = 'Vui lòng chọn phương thức thanh toán.';
    if (empty($_POST['agree'])) $errors[] = 'Bạn cần đồng ý với điều khoản dịch vụ.';

    if (!$errors) {
        $p = $PACKAGES[$in['package']];
        $c = calc_total($p['price'], $in['cycle']);
        $order = [
            'code' => 'NC' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)),
            'package' => $p['name'], 'cycle' => $in['cycle'], 'calc' => $c,
            'name' => $in['name'], 'email' => $in['email'], 'phone' => $in['phone'],
            'method' => $in['method'],
        ];
        order_add([
            'time' => date('c'), 'code' => $order['code'],
            'user_id' => (string)$me['id'], 'user' => $me['name'],
            'package' => $p['name'], 'cycle' => $in['cycle'], 'total' => $c['total'],
            'method' => $in['method'], 'status' => 'pending',
            'name' => $in['name'], 'email' => $in['email'], 'phone' => $in['phone'],
            'company' => cut($in['company'], 100), 'note' => cut($in['note'], 300),
        ]);
        track_event('order', $order['code'] . ' / ' . $p['name'] . ' / ' . $in['method']);
    }
}

if (!isset($PACKAGES[$in['package']])) $in['package'] = 'business';
if (!in_array($in['cycle'], [1, 12], true)) $in['cycle'] = 1;
$cur = $PACKAGES[$in['package']];
$sum = calc_total($cur['price'], $in['cycle']);
?>
<section class="page-hero"><div class="container">
  <h1><?= $order ? 'Đặt hàng thành công' : 'Đăng ký & thanh toán gói' ?></h1>
  <p class="lead"><?= $order ? 'Cảm ơn bạn đã tin tưởng ' . e(SITE_NAME) . '.' : 'Hoàn tất chỉ trong vài phút.' ?></p>
</div></section>

<section class="section"><div class="container">

<?php if ($order): ?>
  <div class="done">
    <div class="done-main card">
      <div class="done-icon">✓</div>
      <h2>Mã đơn hàng: <span class="code"><?= e($order['code']) ?></span></h2>
      <p class="muted">Chúng tôi đã ghi nhận đơn đăng ký gói <b><?= e($order['package']) ?></b> của <b><?= e($order['name']) ?></b>. Thông tin xác nhận sẽ được gửi tới <b><?= e($order['email']) ?></b>.</p>

      <div class="pay-box">
        <h3>Hướng dẫn thanh toán</h3>
        <?php if ($order['method'] === 'bank'): ?>
          <table class="spec">
            <tr><td>Ngân hàng</td><td><?= e(BANK_NAME) ?></td></tr>
            <tr><td>Số tài khoản</td><td><?= e(BANK_ACC) ?></td></tr>
            <tr><td>Chủ tài khoản</td><td><?= e(BANK_OWNER) ?></td></tr>
            <tr><td>Số tiền</td><td><?= vnd($order['calc']['total']) ?></td></tr>
            <tr><td>Nội dung chuyển khoản</td><td><?= e($order['code']) ?></td></tr>
          </table>
          <p class="muted note">Dịch vụ được kích hoạt trong vòng 5 phút sau khi nhận được tiền.</p>
        <?php elseif ($order['method'] === 'wallet'): ?>
          <div class="qr"><div>QR</div></div>
          <p class="center muted">Quét mã bằng MoMo hoặc ZaloPay, số tiền <b><?= vnd($order['calc']['total']) ?></b>, nội dung <b><?= e($order['code']) ?></b>.<br><small>(Mã QR minh họa. Thay bằng mã thật khi tích hợp cổng thanh toán.)</small></p>
        <?php elseif ($order['method'] === 'card'): ?>
          <p>Liên kết thanh toán bảo mật bằng thẻ Visa / Mastercard cho số tiền <b><?= vnd($order['calc']['total']) ?></b> sẽ được gửi tới email của bạn.</p>
          <p class="muted note">Chúng tôi không lưu số thẻ trên website này.</p>
        <?php else: ?>
          <p>Nhân viên sẽ liên hệ qua số <b><?= e($order['phone']) ?></b> để xác nhận và gửi hóa đơn cho số tiền <b><?= vnd($order['calc']['total']) ?></b>.</p>
        <?php endif; ?>
      </div>

      <div class="modal-actions">
        <a class="btn" href="index.php?page=don-hang">Xem đơn hàng của tôi</a>
        <a class="btn btn-line" href="<?= e(main_home_url()) ?>">← Quay lại trang chính</a>
      </div>
    </div>

    <aside class="card summary">
      <h3>Tóm tắt đơn hàng</h3>
      <div class="row"><span>Gói</span><b><?= e($order['package']) ?></b></div>
      <div class="row"><span>Chu kỳ</span><b><?= $order['cycle'] === 12 ? '12 tháng' : '1 tháng' ?></b></div>
      <div class="row"><span>Tạm tính</span><b><?= vnd($order['calc']['base']) ?></b></div>
      <?php if ($order['calc']['disc']): ?><div class="row save-row"><span>Giảm giá năm</span><b>-<?= vnd($order['calc']['disc']) ?></b></div><?php endif; ?>
      <div class="row"><span>VAT 10%</span><b><?= vnd($order['calc']['vat']) ?></b></div>
      <div class="row total"><span>Tổng cộng</span><b><?= vnd($order['calc']['total']) ?></b></div>
    </aside>
  </div>

<?php else: ?>
  <?php if ($errors): ?>
    <div class="alert err"><b>Vui lòng kiểm tra lại:</b><ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>

  <form method="post" action="index.php?page=dang-ky-goi" id="orderForm" class="checkout">
    <input type="hidden" name="form" value="order">
    <div class="checkout-main">

      <div class="card block">
        <h3><span class="num">1</span> Chọn gói dịch vụ</h3>
        <div class="opts opts-4">
          <?php foreach ($PACKAGES as $id => $p): ?>
          <label class="opt">
            <input type="radio" name="package" value="<?= e($id) ?>" <?= $in['package'] === $id ? 'checked' : '' ?>>
            <span class="box">
              <b><?= e($p['name']) ?></b>
              <small><?= e($p['specs']['CPU']) ?> · <?= e($p['specs']['RAM']) ?> · <?= e($p['specs']['SSD']) ?></small>
              <em><?= vnd($p['price']) ?>/tháng</em>
            </span>
          </label>
          <?php endforeach; ?>
        </div>
        <div class="opts opts-2 mt">
          <label class="opt">
            <input type="radio" name="cycle" value="1" <?= $in['cycle'] === 1 ? 'checked' : '' ?>>
            <span class="box"><b>Theo tháng</b><small>Linh hoạt, hủy bất cứ lúc nào</small></span>
          </label>
          <label class="opt">
            <input type="radio" name="cycle" value="12" <?= $in['cycle'] === 12 ? 'checked' : '' ?>>
            <span class="box"><b>Theo năm <span class="tag">-20%</span></b><small>Tiết kiệm hơn khi dùng lâu dài</small></span>
          </label>
        </div>
      </div>

      <div class="card block">
        <h3><span class="num">2</span> Thông tin liên hệ</h3>
        <div class="form form-2">
          <label>Họ tên *<input name="name" value="<?= e($in['name']) ?>" required></label>
          <label>Số điện thoại *<input name="phone" value="<?= e($in['phone']) ?>" required></label>
          <label>Email *<input type="email" name="email" value="<?= e($in['email']) ?>" required></label>
          <label>Công ty (nếu có)<input name="company" value="<?= e($in['company']) ?>"></label>
          <label class="full">Ghi chú<textarea name="note" rows="2" placeholder="Ví dụ: cần cài sẵn WordPress, tên miền..."><?= e($in['note']) ?></textarea></label>
        </div>
      </div>

      <div class="card block">
        <h3><span class="num">3</span> Phương thức thanh toán</h3>
        <div class="opts opts-2">
          <?php foreach ($methods as $id => $m): ?>
          <label class="opt">
            <input type="radio" name="method" value="<?= e($id) ?>" <?= $in['method'] === $id ? 'checked' : '' ?>>
            <span class="box"><b><?= e($m[0]) ?></b><small><?= e($m[1]) ?></small></span>
          </label>
          <?php endforeach; ?>
        </div>
        <label class="agree"><input type="checkbox" name="agree" value="1"> Tôi đồng ý với điều khoản dịch vụ và chính sách hoàn tiền 7 ngày.</label>
      </div>
    </div>

    <aside class="card summary sticky">
      <h3>Tóm tắt đơn hàng</h3>
      <div class="row"><span>Gói</span><b id="sPkg"><?= e($cur['name']) ?></b></div>
      <div class="row"><span>Chu kỳ</span><b id="sCycle"><?= $in['cycle'] === 12 ? '12 tháng' : '1 tháng' ?></b></div>
      <div class="row"><span>Tạm tính</span><b id="sBase"><?= vnd($sum['base']) ?></b></div>
      <div class="row save-row" id="sDiscRow" <?= $sum['disc'] ? '' : 'hidden' ?>><span>Giảm giá năm</span><b id="sDisc">-<?= vnd($sum['disc']) ?></b></div>
      <div class="row"><span>VAT 10%</span><b id="sVat"><?= vnd($sum['vat']) ?></b></div>
      <div class="row total"><span>Tổng cộng</span><b id="sTotal"><?= vnd($sum['total']) ?></b></div>
      <button class="btn btn-block" type="submit">Xác nhận đăng ký</button>
      <ul class="trust">
        <li>Hoàn tiền 100% trong 7 ngày</li>
        <li>Kích hoạt trong 5 phút</li>
        <li>Hỗ trợ 24/7</li>
      </ul>
    </aside>
  </form>
<?php endif; ?>

</div></section>