<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: shop.php');
    exit;
}

$product = getProductBySlug($pdo, $slug);
if (!$product) {
    header('HTTP/1.0 404 Not Found');
    $pageTitle = 'Product Not Found';
    include 'includes/header.php';
    echo '<div class="container py-5 text-center" style="padding-top: 5rem !important;"><h2 class="fw-light mb-3">Product Not Found</h2><p class="text-muted mb-4">The product you\'re looking for doesn\'t exist.</p><a href="shop.php" class="btn btn-primary rounded-0 px-5">Continue Shopping</a></div>';
    include 'includes/footer.php';
    exit;
}

$related = getRelatedProducts($pdo, $product['category_id'], $product['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    addToCart($product['id'], $qty);
    header('Location: product.php?slug=' . htmlspecialchars($slug) . '&toast=' . urlencode($product['name'] . ' added to cart'));
    exit;
}

$pageTitle = $product['name'];
$pageDesc = $product['description'];
include 'includes/header.php';
?>

<section class="py-4" style="padding-top: 2rem !important;">
    <div class="container">
        <div class="row g-5">
            <div class="col-md-7">
                <div class="position-sticky" style="top: 96px;">
                    <div class="product-card__image-wrap w-100">
                        <img class="w-100" style="aspect-ratio: 3/4; object-fit: cover;" src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['image_alt'] ?: $product['name']) ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <p class="small text-muted text-uppercase ls-wider mb-2" style="font-size: 0.6875rem;"><?= htmlspecialchars($product['category_name']) ?></p>
                <h1 class="fw-light h2 mb-3" style="letter-spacing: -0.02em;"><?= htmlspecialchars($product['name']) ?></h1>
                
                <div class="d-flex align-items-center gap-3 mb-4 pb-4 border-bottom">
                    <span class="h3 fw-medium mb-0"><?= formatPrice($product['price']) ?></span>
                    <?php if ($product['compare_price']): ?>
                        <span class="text-muted text-decoration-line-through fs-5"><?= formatPrice($product['compare_price']) ?></span>
                        <span class="small text-uppercase ls-wider fw-medium" style="color: var(--brand-gold);">Save <?= formatPrice($product['compare_price'] - $product['price']) ?></span>
                    <?php endif; ?>
                </div>

                <p class="text-secondary mb-4" style="max-width: 65ch; line-height: 1.75;"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                
                <?php if ($product['details']): ?>
                    <div class="mb-4">
                        <h3 class="h6 fw-semibold text-uppercase ls-wider small mb-2">Details</h3>
                        <p class="text-secondary" style="line-height: 1.75;"><?= nl2br(htmlspecialchars($product['details'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Size selector (UI only) -->
                <div class="mb-4">
                    <label class="form-label small fw-medium">Size</label>
                    <div class="d-flex gap-2">
                        <?php foreach (['XS', 'S', 'M', 'L', 'XL'] as $size): ?>
                            <label class="border px-3 py-2 small fw-medium" style="cursor: pointer; min-width: 44px; text-align: center;">
                                <input type="radio" name="size" value="<?= $size ?>" class="position-absolute opacity-0" style="pointer-events: none;">
                                <?= $size ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <form method="POST" action="product.php?slug=<?= htmlspecialchars($slug) ?>" class="d-flex gap-3 pt-3 border-top">
                    <input type="hidden" name="action" value="add_to_cart">
                    <div class="input-group" style="width: 120px;">
                        <button type="button" class="btn btn-outline-secondary rounded-0 qty-dec" aria-label="Decrease">−</button>
                        <input type="number" name="quantity" class="form-control text-center border-start-0 border-end-0 rounded-0" value="1" min="1" max="99" style="-moz-appearance: textfield;">
                        <button type="button" class="btn btn-outline-secondary rounded-0 qty-inc" aria-label="Increase">+</button>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg flex-grow-1 rounded-0" <?= $product['stock'] < 1 ? 'disabled' : '' ?>>
                        <?= $product['stock'] < 1 ? 'Out of Stock' : 'Add to Cart' ?>
                    </button>
                </form>

                <div class="d-flex gap-4 mt-4 pt-3 border-top small text-muted">
                    <span>✓ Free shipping over $500</span>
                    <span>↩ Easy returns</span>
                    <span>🔒 Secure checkout</span>
                </div>

                <p class="small text-muted mt-3">
                    <?= $product['stock'] > 0 ? 'In stock — ships within 1–2 business days' : 'Currently out of stock' ?>
                </p>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($related)): ?>
<section class="py-5" style="background: var(--brand-surface);">
    <div class="container">
        <div class="text-center mb-5">
            <p class="text-uppercase ls-wider small fw-medium text-muted mb-2">Complete the Look</p>
            <h2 class="fw-light h3 mb-0">You May Also Like</h2>
        </div>
        <div class="row g-4">
            <?php foreach ($related as $i => $item): ?>
                <div class="col-6 col-md-3 reveal" style="transition-delay: <?= 0.1 + $i * 0.1 ?>s">
                    <a href="product.php?slug=<?= htmlspecialchars($item['slug']) ?>" class="product-card">
                        <div class="product-card__image-wrap">
                            <img class="product-card__image" src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['image_alt'] ?: $item['name']) ?>" loading="lazy">
                        </div>
                        <div class="pt-3">
                            <h3 class="fs-6 fw-medium mb-1"><?= htmlspecialchars($item['name']) ?></h3>
                            <span class="fw-medium"><?= formatPrice($item['price']) ?></span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
