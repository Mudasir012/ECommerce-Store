<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$gender = $_GET['gender'] ?? '';
$categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
$sort = $_GET['sort'] ?? 'newest';
$search = $_GET['q'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;

$categories = getCategories($pdo);

if ($search) {
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.name LIKE ? OR p.description LIKE ? ORDER BY p.created_at DESC");
    $like = '%' . $search . '%';
    $stmt->execute([$like, $like]);
    $products = $stmt->fetchAll();
    $totalProducts = count($products);
} elseif ($gender && $categoryId) {
    $products = getProductsByGender($pdo, $gender, $categoryId, $sort, $page, $perPage);
    $totalProducts = getProductCountByGender($pdo, $gender, $categoryId);
} elseif ($gender) {
    $products = getProductsByGender($pdo, $gender, null, $sort, $page, $perPage);
    $totalProducts = getProductCountByGender($pdo, $gender);
} elseif ($categoryId) {
    $products = getProductsByCategory($pdo, $categoryId, $sort, $page, $perPage);
    $totalProducts = getProductCountByCategory($pdo, $categoryId);
} else {
    $stmt = $pdo->query("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
    $products = $stmt->fetchAll();
    $totalProducts = count($products);
}

$totalPages = $gender || $categoryId ? max(1, (int)ceil($totalProducts / $perPage)) : 1;

$genderLabel = match($gender) { 'men' => 'Men\'s', 'women' => 'Women\'s', 'kids' => 'Kids\'', default => '' };
$catName = '';
if ($categoryId) {
    foreach ($categories as $c) { if ($c['id'] === $categoryId) { $catName = $c['name']; break; } }
}
$pageTitle = $search ? "Search: {$search}" : ($genderLabel ? ($catName ? "{$genderLabel} {$catName}" : $genderLabel . ' Fashion') : ($catName ?: 'All Products'));
$pageDesc = '';

include 'includes/header.php';
?>

<section class="py-4" style="padding-top: 2rem !important;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
            <div>
                <p class="text-uppercase ls-wider small fw-medium text-muted mb-1">
                    <?php if ($gender): ?><a href="shop.php?gender=<?= $gender ?>" class="text-muted text-decoration-none"><?= $genderLabel ?></a><?php if ($categoryId): ?> / <?php endif; ?><?php endif; ?>
                    <?php if ($categoryId): ?><?= $catName ?><?php endif; ?>
                    <?php if (!$gender && !$categoryId && !$search): ?>Browse All<?php endif; ?>
                </p>
                <h1 class="fw-light h2 mb-0"><?= $search ? "Search results" : ($pageTitle) ?></h1>
            </div>
            <span class="small text-muted"><?= $totalProducts ?> <?= $totalProducts === 1 ? 'item' : 'items' ?></span>
        </div>

        <!-- Gender tabs -->
        <div class="d-flex gap-4 pb-3 mb-3 border-bottom overflow-auto" style="scrollbar-width: none;">
            <a href="shop.php<?= $categoryId ? '?category=' . $categoryId : '' ?>" class="category-link <?= !$gender && !$search ? 'active' : '' ?>">All</a>
            <a href="shop.php?gender=men<?= $categoryId ? '&category=' . $categoryId : '' ?>" class="category-link <?= $gender === 'men' ? 'active' : '' ?>">Men</a>
            <a href="shop.php?gender=women<?= $categoryId ? '&category=' . $categoryId : '' ?>" class="category-link <?= $gender === 'women' ? 'active' : '' ?>">Women</a>
            <a href="shop.php?gender=kids<?= $categoryId ? '&category=' . $categoryId : '' ?>" class="category-link <?= $gender === 'kids' ? 'active' : '' ?>">Kids</a>
        </div>

        <!-- Category sub-nav -->
        <?php if (!$search): ?>
        <div class="d-flex gap-4 pb-3 mb-3 overflow-auto" style="scrollbar-width: none;">
            <a href="shop.php<?= $gender ? '?gender=' . $gender : '' ?>" class="category-link small" style="font-size: 0.75rem; letter-spacing: 0.04em; text-transform: none; <?= !$categoryId ? 'color: var(--brand-green) !important; border-bottom-color: var(--brand-green);' : '' ?>">All Categories</a>
            <?php foreach ($categories as $cat): ?>
                <a href="shop.php<?= $gender ? '?gender=' . $gender . '&' : '?' ?>category=<?= $cat['id'] ?>" class="category-link small" style="font-size: 0.75rem; letter-spacing: 0.04em; text-transform: none; <?= $categoryId === $cat['id'] ? 'color: var(--brand-green) !important; border-bottom-color: var(--brand-green);' : '' ?>"><?= htmlspecialchars($cat['name']) ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <p class="small text-muted mb-0">
                <?php if ($search): ?>Showing results for "<strong><?= htmlspecialchars($search) ?></strong>"<?php endif; ?>
            </p>
            <form method="GET" action="shop.php" class="d-flex gap-2 align-items-center">
                <?php if ($gender): ?><input type="hidden" name="gender" value="<?= $gender ?>"><?php endif; ?>
                <?php if ($categoryId): ?><input type="hidden" name="category" value="<?= $categoryId ?>"><?php endif; ?>
                <?php if ($search): ?><input type="hidden" name="q" value="<?= htmlspecialchars($search) ?>"><?php endif; ?>
                <label for="sortSelect" class="small text-muted visually-hidden">Sort by</label>
                <select id="sortSelect" name="sort" class="form-select form-select-sm border" style="width: auto;" onchange="this.form.submit()">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                    <option value="price-asc" <?= $sort === 'price-asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name</option>
                </select>
            </form>
        </div>

        <?php if (empty($products)): ?>
            <div class="text-center py-5" style="padding: 5rem 0 !important;">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.75" class="text-muted mb-3"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <h2 class="h4 fw-light mb-2">No products found</h2>
                <p class="text-muted mb-4">Try adjusting your search or filter.</p>
                <a href="shop.php" class="btn btn-primary rounded-0 px-5">View All Products</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="product.php?slug=<?= htmlspecialchars($product['slug']) ?>" class="product-card">
                            <div class="product-card__image-wrap position-relative">
                                <img class="product-card__image" src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['image_alt'] ?: $product['name']) ?>" loading="lazy">
                                <?php if ($product['compare_price']): ?>
                                    <span class="product-card__badge badge position-absolute top-0 start-0 mt-2 ms-2" style="background: var(--brand-gold);">Sale</span>
                                <?php endif; ?>
                                <div class="product-card__quick-add text-center">
                                    <span class="btn btn-sm btn-light rounded-0 px-4 fw-medium small text-uppercase ls-wider">Quick View</span>
                                </div>
                            </div>
                            <div class="pt-3">
                                <p class="small text-muted text-uppercase ls-wider mb-1" style="font-size: 0.6875rem;">
                                    <?= htmlspecialchars($product['category_name'] ?? '') ?>
                                    <?php if ($product['gender']): ?> · <span class="text-lowercase"><?= ucfirst($product['gender']) ?></span><?php endif; ?>
                                </p>
                                <h3 class="fs-6 fw-medium mb-1"><?= htmlspecialchars($product['name']) ?></h3>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-medium"><?= formatPrice($product['price']) ?></span>
                                    <?php if ($product['compare_price']): ?>
                                        <span class="text-muted text-decoration-line-through small"><?= formatPrice($product['compare_price']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav class="mt-5 d-flex justify-content-center" aria-label="Pagination">
                    <ul class="pagination pagination-sm">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link border-0" href="?gender=<?= $gender ?>&category=<?= $categoryId ?>&sort=<?= $sort ?>&page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
