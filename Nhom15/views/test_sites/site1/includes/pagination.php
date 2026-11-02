<?php
// views/test_sites/site1/includes/pagination.php
// Cần có sẵn: $page (trang hiện tại), $totalPages, $baseQuery (cat, sub, q)

if ($totalPages <= 1) {
    return;
}

$pageUrl = function (int $n) use ($baseQuery): string {
    return site1_url('index.php', $baseQuery + ['page' => $n > 1 ? $n : null]);
};

// Hiện trang đầu, trang cuối và các trang quanh trang hiện tại
$shown = [];
for ($i = 1; $i <= $totalPages; $i++) {
    if ($i === 1 || $i === $totalPages || abs($i - $page) <= 2) {
        $shown[] = $i;
    }
}
?>
<nav class="pagination" aria-label="Phân trang sản phẩm">
    <?php if ($page > 1): ?>
        <a class="page-link" href="<?= h($pageUrl($page - 1)) ?>">« Trước</a>
    <?php else: ?>
        <span class="page-link disabled">« Trước</span>
    <?php endif; ?>

    <?php $prev = 0; foreach ($shown as $i): ?>
        <?php if ($i - $prev > 1): ?><span class="page-dots">…</span><?php endif; ?>
        <?php if ($i === $page): ?>
            <span class="page-link active" aria-current="page"><?= $i ?></span>
        <?php else: ?>
            <a class="page-link" href="<?= h($pageUrl($i)) ?>"><?= $i ?></a>
        <?php endif; ?>
        <?php $prev = $i; ?>
    <?php endforeach; ?>

    <?php if ($page < $totalPages): ?>
        <a class="page-link" href="<?= h($pageUrl($page + 1)) ?>">Sau »</a>
    <?php else: ?>
        <span class="page-link disabled">Sau »</span>
    <?php endif; ?>
</nav>