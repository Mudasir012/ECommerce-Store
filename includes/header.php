<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? $pageTitle . ' — ' . SITE_NAME : SITE_NAME ?></title>
<meta name="description" content="<?= $pageDesc ?? 'Curated luxury fashion for the discerning wardrobe.' ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg bg-white" role="navigation" aria-label="Main navigation" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="index.php"><?= SITE_NAME ?></a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Toggle navigation">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'shop.php' && !isset($_GET['gender']) ? 'active' : '' ?>" href="shop.php">All</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= (isset($_GET['gender']) && $_GET['gender'] === 'men') ? 'active' : '' ?>" href="shop.php?gender=men" data-bs-toggle="dropdown" role="button">Men</a>
                    <ul class="dropdown-menu rounded-0 border-top-0 mt-0">
                        <li><a class="dropdown-item small" href="shop.php?gender=men">All Men's</a></li>
                        <li><a class="dropdown-item small" href="shop.php?gender=men&category=1">Ready-to-Wear</a></li>
                        <li><a class="dropdown-item small" href="shop.php?gender=men&category=2">Outerwear</a></li>
                        <li><a class="dropdown-item small" href="shop.php?gender=men&category=3">Footwear</a></li>
                        <li><a class="dropdown-item small" href="shop.php?gender=men&category=4">Accessories</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= (isset($_GET['gender']) && $_GET['gender'] === 'women') ? 'active' : '' ?>" href="shop.php?gender=women" data-bs-toggle="dropdown" role="button">Women</a>
                    <ul class="dropdown-menu rounded-0 border-top-0 mt-0">
                        <li><a class="dropdown-item small" href="shop.php?gender=women">All Women's</a></li>
                        <li><a class="dropdown-item small" href="shop.php?gender=women&category=1">Ready-to-Wear</a></li>
                        <li><a class="dropdown-item small" href="shop.php?gender=women&category=2">Outerwear</a></li>
                        <li><a class="dropdown-item small" href="shop.php?gender=women&category=3">Footwear</a></li>
                        <li><a class="dropdown-item small" href="shop.php?gender=women&category=4">Accessories</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?= (isset($_GET['gender']) && $_GET['gender'] === 'kids') ? 'active' : '' ?>" href="shop.php?gender=kids" data-bs-toggle="dropdown" role="button">Kids</a>
                    <ul class="dropdown-menu rounded-0 border-top-0 mt-0">
                        <li><a class="dropdown-item small" href="shop.php?gender=kids">All Kids'</a></li>
                        <li><a class="dropdown-item small" href="shop.php?gender=kids&category=1">Ready-to-Wear</a></li>
                        <li><a class="dropdown-item small" href="shop.php?gender=kids&category=2">Outerwear</a></li>
                        <li><a class="dropdown-item small" href="shop.php?gender=kids&category=3">Footwear</a></li>
                        <li><a class="dropdown-item small" href="shop.php?gender=kids&category=4">Accessories</a></li>
                    </ul>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-1">
                <button class="nav-icon-btn" aria-label="Search" data-bs-toggle="collapse" data-bs-target="#searchBar" aria-expanded="false">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                </button>
                <a href="account.php" class="nav-icon-btn" aria-label="Account">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </a>
                <a href="cart.php" class="nav-icon-btn" aria-label="Shopping cart">
                    <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="21" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    <?php if (getCartCount() > 0): ?>
                        <span class="cart-badge bg-primary text-white"><?= getCartCount() ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="collapse bg-light" id="searchBar">
    <div class="container py-3">
        <form action="shop.php" method="GET" class="d-flex gap-3 align-items-center">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="flex-shrink-0 text-muted"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="q" class="form-control form-control-lg border-0 bg-transparent px-0 shadow-none" placeholder="Search products..." autofocus>
        </form>
    </div>
</div>

<main id="main-content">
