<?php
$services = [
  ['Cloud VPS','Máy chủ ảo cấu hình linh hoạt, nâng cấp không cần khởi động lại.','M3 5h18v6H3zM3 13h18v6H3zM7 8h.01M7 16h.01'],
  ['Cloud Hosting','Hosting tối ưu cho WordPress, Laravel, PHP với LiteSpeed.','M12 3a9 9 0 100 18 9 9 0 000-18zM3 12h18M12 3c3 3 3 15 0 18M12 3c-3 3-3 15 0 18'],
  ['Cloud Storage','Lưu trữ đối tượng tương thích S3, mã hóa dữ liệu.','M4 7c0-2 4-3 8-3s8 1 8 3-4 3-8 3-8-1-8-3zM4 7v10c0 2 4 3 8 3s8-1 8-3V7'],
  ['Backup & Restore','Sao lưu tự động, khôi phục chỉ với một cú nhấp.','M3 12a9 9 0 109-9M3 4v5h5M12 8v4l3 2'],
  ['Bảo mật & DDoS','WAF, chống DDoS và quét mã độc liên tục.','M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z'],
  ['Giám sát','Theo dõi CPU, RAM, băng thông theo thời gian thực.','M3 12h4l3-8 4 16 3-8h4'],
];
?>
<section class="page-hero"><div class="container"><h1>Dịch vụ Cloud</h1><p class="lead">Trọn bộ giải pháp hạ tầng cho dự án của bạn.</p></div></section>
<section class="section"><div class="container grid-3">
  <?php foreach ($services as $s): ?>
    <div class="card feat">
      <span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="<?= e($s[2]) ?>"/></svg></span>
      <h3><?= e($s[0]) ?></h3><p class="muted"><?= e($s[1]) ?></p>
    </div>
  <?php endforeach; ?>
</div></section>
<section class="cta"><div class="container"><h2>Chưa biết chọn gói nào?</h2><p>Xem bảng giá để so sánh các gói.</p><a class="btn btn-white" href="index.php?page=bang-gia">Xem bảng giá</a></div></section>