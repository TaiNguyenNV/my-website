<?php
$me = current_user();
$seeAll = can('order.view_all');
$canEdit = can('order.update');
$st = order_statuses();
$methodLbl = ['bank' => 'Chuyển khoản', 'wallet' => 'Ví điện tử', 'card' => 'Thẻ', 'later' => 'Thanh toán sau'];

$orders = array_reverse(orders_all());
if (!$seeAll) {   // khách hàng chỉ thấy đơn của mình
    $orders = array_values(array_filter($orders, function ($o) use ($me) {
        return (string)($o['user_id'] ?? '') === (string)$me['id'];
    }));
}
$cnt = array_fill_keys(array_keys($st), 0);
$revenue = 0;
foreach ($orders as $o) {
    $s = $o['status'] ?? 'pending';
    if ($s === 'active') $s = 'paid';   // đơn cũ "đã kích hoạt" coi như đã thanh toán
    if (isset($cnt[$s])) $cnt[$s]++;
    if ($s === 'paid') $revenue += (int)($o['total'] ?? 0);
}
?>
<section class="page-hero"><div class="container">
  <h1><?= $seeAll ? 'Quản lý đơn hàng' : 'Đơn hàng của tôi' ?></h1>
  <p class="lead"><?= $seeAll ? 'Toàn bộ đơn đăng ký gói trên site2.' : 'Theo dõi các gói bạn đã đăng ký.' ?></p>
</div></section>

<section class="section"><div class="container">
  <?php if ($seeAll): ?>
  <div class="stat-cards">
    <div class="card"><span class="muted">Tổng đơn</span><b><?= count($orders) ?></b></div>
    <div class="card"><span class="muted">Chờ thanh toán</span><b><?= $cnt['pending'] ?></b></div>
    <div class="card"><span class="muted">Đã thanh toán</span><b><?= $cnt['paid'] ?></b></div>
    <div class="card"><span class="muted">Doanh thu đã thu</span><b><?= vnd($revenue) ?></b></div>
  </div>
  <?php endif; ?>

  <?php if (!$orders): ?>
    <div class="card center"><p class="muted">Chưa có đơn hàng nào.</p><a class="btn mt" href="index.php?page=dang-ky-goi">Đăng ký gói ngay</a></div>
  <?php else: ?>
  <div class="table-wrap">
    <table class="price-table">
      <thead><tr>
        <th>Mã đơn</th><th>Thời gian</th><?php if ($seeAll): ?><th>Khách hàng</th><?php endif; ?>
        <th>Gói</th><th>Tổng tiền</th><th>Thanh toán</th><th>Trạng thái</th>
      </tr></thead>
      <tbody>
      <?php foreach ($orders as $o): $s = $o['status'] ?? 'pending'; if ($s === 'active') $s = 'paid'; ?>
        <tr>
          <td><b><?= e($o['code'] ?? '') ?></b></td>
          <td><?= e(date('d/m/Y H:i', strtotime($o['time'] ?? 'now'))) ?></td>
          <?php if ($seeAll): ?><td><?= e($o['name'] ?? '') ?><br><small class="muted"><?= e($o['email'] ?? '') ?> · <?= e($o['phone'] ?? '') ?></small></td><?php endif; ?>
          <td><?= e($o['package'] ?? '') ?> <small class="muted">/ <?= (int)($o['cycle'] ?? 1) === 12 ? '12 tháng' : '1 tháng' ?></small></td>
          <td class="price-cell"><?= vnd((int)($o['total'] ?? 0)) ?></td>
          <td><?= e($methodLbl[$o['method'] ?? ''] ?? '') ?></td>
          <td>
            <?php if ($canEdit): ?>
              <form method="post" action="index.php?page=don-hang" class="st-form">
                <input type="hidden" name="form" value="status">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="code" value="<?= e($o['code'] ?? '') ?>">
                <select name="status" onchange="this.form.submit()">
                  <?php foreach ($st as $k => $lbl): ?><option value="<?= e($k) ?>" <?= $k === $s ? 'selected' : '' ?>><?= e($lbl) ?></option><?php endforeach; ?>
                </select>
              </form>
            <?php else: ?>
              <span class="status st-<?= e($s) ?>"><?= e($st[$s] ?? $s) ?></span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div></section>