<?php
$u = current_user();
$roleLabel = ['admin' => 'Quản trị viên', 'staff' => 'Nhân viên', 'user' => 'Khách hàng'];
$back = $_SERVER['REQUEST_URI'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($PAGES[$page]) ?> | <?= e(SITE_NAME) ?></title>
<link rel="stylesheet" href="<?= ASSET_CSS ?>">
</head>
<body>
<div class="topbar">
  <div class="container">
    <a class="back" href="<?= e(main_home_url()) ?>">← Quay lại trang chính</a>
    <div class="who">
      <?php if ($u): ?>
        <span>Xin chào, <b><?= e($u['name']) ?></b></span>
        <span class="role r-<?= e($u['role']) ?>"><?= e($roleLabel[$u['role']]) ?></span>
        <a href="index.php?action=logout">Đăng xuất</a>
      <?php else: ?>
        <a href="<?= e(main_login_url($back)) ?>">Đăng nhập</a>
        <a href="<?= e(MAIN_URL . MAIN_REGISTER) ?>">Đăng ký tài khoản</a>
      <?php endif; ?>
    </div>
  </div>
</div>
<header class="header">
  <div class="container nav">
    <a class="logo" href="index.php"><span class="logo-mark">N</span><?= e(SITE_NAME) ?></a>
    <button class="burger" id="burger" aria-label="Menu">Menu</button>
    <nav id="menu">
      <?php foreach ($PAGES as $slug => $label):
        $need = page_perm($slug);
        if ($slug !== 'dang-ky-goi' && $need && !can($need)) continue;   // ẩn trang không có quyền
      ?>
        <?php if ($slug === 'dang-ky-goi'): ?>
          <a class="btn btn-sm nav-cta" href="index.php?page=<?= $slug ?>"><?= e($label) ?></a>
        <?php else: ?>
          <a href="index.php?page=<?= $slug ?>" class="<?= $slug === $page ? 'active' : '' ?>"><?= e($label) ?></a>
        <?php endif; ?>
      <?php endforeach; ?>
    </nav>
  </div>
</header>
<main>