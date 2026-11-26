<?php
$pageTitle = 'UniFood | My Orders';
$activePage = 'orders';
require_once 'header.php';
?>

<div class="page-header">
    <div class="header-title">
        <h1>My campus orders</h1>
        <p>Showing personal orders placed by <strong><?= htmlspecialchars($fullName ?? 'your account', ENT_QUOTES, 'UTF-8') ?></strong>. Track your live meal preparation, self-pickup code, and campus runner delivery.</p>
    </div>
    <a href="index.php" class="header-tag">← Back to menu</a>
</div>

<div class="orders-list" id="orders-container"></div>

<?php require_once 'footer.php'; ?>

