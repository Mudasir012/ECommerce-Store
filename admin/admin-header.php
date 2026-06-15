<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? $pageTitle . ' — Admin · ' . SITE_NAME : 'Admin · ' . SITE_NAME ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-md navbar-dark bg-dark" aria-label="Admin navigation">
    <div class="container-fluid">
        <a class="navbar-brand small text-uppercase ls-wider" href="index.php"><?= SITE_NAME ?> Admin</a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link small" href="index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link small" href="products.php">Products</a></li>
                <li class="nav-item"><a class="nav-link small" href="orders.php">Orders</a></li>
                <li class="nav-item"><a class="nav-link small" href="categories.php">Categories</a></li>
                <li class="nav-item"><a class="nav-link small" href="../index.php">View Site</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar vh-100 border-end p-0" style="position: sticky; top: 0;">
            <div class="list-group list-group-flush rounded-0">
                <a href="index.php" class="list-group-item list-group-item-action border-0 small <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">Dashboard</a>
                <a href="products.php" class="list-group-item list-group-item-action border-0 small <?= basename($_SERVER['PHP_SELF']) === 'products.php' ? 'active' : '' ?>">Products</a>
                <a href="orders.php" class="list-group-item list-group-item-action border-0 small <?= basename($_SERVER['PHP_SELF']) === 'orders.php' ? 'active' : '' ?>">Orders</a>
                <a href="categories.php" class="list-group-item list-group-item-action border-0 small <?= basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active' : '' ?>">Categories</a>
            </div>
        </nav>
        <main class="col-md-10 ms-auto px-4 py-4">
