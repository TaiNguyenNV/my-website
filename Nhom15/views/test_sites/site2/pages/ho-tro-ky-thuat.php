<?php
$faq = [
  ['Tôi có thể nâng cấp gói sau này không?','Có. Bạn có thể nâng cấp bất cứ lúc nào, hệ thống tính phí chênh lệch theo thời gian còn lại.'],
  ['Dữ liệu có được sao lưu không?','Mọi gói đều có backup tự động, tần suất tùy gói (hàng tuần đến liên tục).'],
  ['Có hoàn tiền không?','Hoàn tiền 100% trong 7 ngày đầu nếu bạn không hài lòng.'],
  ['Thời gian hỗ trợ?','Đội ngũ kỹ thuật trực 24/7 qua chat, email và hotline.'],
];

// Gói được chọn từ cửa sổ chi tiết (điền sẵn vào form)
$pk = $_GET['package'] ?? '';
$pkName = isset($PACKAGES[$pk]) ? $PACKAGES[$pk]['name'] : '';
$me = current_user();

$sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'contact') {
    track_event('contact', trim($_POST['topic'] ?? '') . ' / ' . trim($_POST['email'] ?? ''));
    $sent = true;
}
?>
<section class="page-hero"><div class="container"><h1>Hỗ trợ kỹ thuật</h1><p class="lead">Chúng tôi luôn sẵn sàng giúp bạn.</p></div></section>
<section class="section"><div class="container two-col">
  <div>
    <h2>Câu hỏi thường gặp</h2>
    <?php foreach ($faq as $q): ?>
      <div class="faq"><button type="button" class="faq-q"><?= e($q[0]) ?><span>+</span></button><div class="faq-a"><p><?= e($q[1]) ?></p></div></div>
    <?php endforeach; ?>
  </div>
  <div class="card" id="lien-he">
    <h2>Gửi yêu cầu tư vấn / hỗ trợ</h2>
    <?php if ($sent): ?><div class="alert">Đã gửi yêu cầu! Chúng tôi sẽ phản hồi sớm.</div><?php endif; ?>
    <?php if ($pkName && !$sent): ?><div class="alert">Bạn đang quan tâm đến gói <b><?= e($pkName) ?></b>.</div><?php endif; ?>
    <form method="post" action="index.php?page=ho-tro-ky-thuat#lien-he" class="form">
      <input type="hidden" name="form" value="contact">
      <label>Chủ đề
        <select name="topic">
          <option value="Tư vấn gói" <?= $pkName ? 'selected' : '' ?>>Tư vấn chọn gói</option>
          <option value="Hỗ trợ kỹ thuật">Hỗ trợ kỹ thuật</option>
          <option value="Báo giá doanh nghiệp">Báo giá doanh nghiệp</option>
        </select>
      </label>
      <label>Họ tên<input name="name" value="<?= e($me['name'] ?? '') ?>" required></label>
      <label>Email<input type="email" name="email" value="<?= e($me['email'] ?? '') ?>" required></label>
      <label>Nội dung<textarea name="msg" rows="4" required><?= $pkName ? 'Tôi muốn được tư vấn về gói ' . e($pkName) . '.' : '' ?></textarea></label>
      <button class="btn" type="submit">Gửi yêu cầu</button>
    </form>
  </div>
</div></section>