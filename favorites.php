<?php
$pageTitle = 'UniFood | Favorites';
$activePage = 'favorites';
require_once 'header.php';
?>

<div class="page-header">
    <div class="header-title">
        <h1>Your saved favorites</h1>
        <p>Quickly reorder the campus meals you love most.</p>
    </div>
    <a href="index.php" class="header-tag">← Explore all food</a>
</div>

<div class="products-grid" id="favorites-grid"></div>

<?php require_once 'footer.php'; ?>

