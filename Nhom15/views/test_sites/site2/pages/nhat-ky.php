<?php
$f = LOG_DIR . '/site2-tracker.log';
$lines = is_file($f) ? file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
$lines = array_reverse(array_slice($lines, -100));   // 100 dòng gần nhất
?>
<section class="page-hero"><div class="container"><h1>Nhật ký hệ thống</h1><p class="lead">100 hoạt động gần nhất trên site2 (chỉ quản trị viên xem được).</p></div></section>
<section class="section"><div class="container">
  <div class="table-wrap">
    <table class="price-table">
      <thead><tr><th>Thời gian</th><th>Người dùng</th><th>Vai trò</th><th>Sự kiện</th><th>Chi tiết</th><th>IP</th></tr></thead>
      <tbody>
      <?php foreach ($lines as $ln): $r = json_decode($ln, true); if (!is_array($r)) continue; ?>
        <tr>
          <td><?= e(date('d/m H:i:s', strtotime($r['time'] ?? 'now'))) ?></td>
          <td><?php if (($r['user'] ?? '') !== ''): ?><?= e($r['user']) ?><?php else: ?><span class="muted">khách</span><?php endif; ?></td>
          <td><span class="role r-<?= e($r['role'] ?? 'guest') ?>"><?= e($r['role'] ?? 'guest') ?></span></td>
          <td><b><?= e($r['type'] ?? '') ?></b></td>
          <td><?= e($r['detail'] ?? '') ?></td>
          <td class="muted"><?= e($r['ip'] ?? '') ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$lines): ?><tr><td colspan="6" class="muted center">Chưa có dữ liệu.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div></section>