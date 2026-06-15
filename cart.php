<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = (int)($_POST['product_id'] ?? 0);

    if ($action === 'update' && $productId) {
        $qty = max(0, (int)($_POST['quantity'] ?? 0));
        updateCartQuantity($productId, $qty);
        header('Location: cart.php');
        exit;
    }
    if ($action === 'remove' && $productId) {
        removeFromCart($productId);
        header('Location: cart.php');
        exit;
    }
    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        header('Location: cart.php');
        exit;
    }
}

$items = getCartItems($pdo);
$total = getCartTotal($items);
$pageTitle = 'Shopping Cart';
include 'includes/header.php';
?>

<section class="py-4" style="padding-top: 3rem !important;">
    <div class="container" style="max-width: 1100px;">
        <h1 class="fw-light h2 mb-4">Shopping Cart</h1>

        <?php if (empty($items)): ?>
            <div class="text-center py-5" style="padding: 5rem 0 !important;">
                <div class="mb-4">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.75" class="text-muted"><circle cx="8" cy="21" r="1.5"/><circle cx="21" cy="21" r="1.5"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                </div>
                <h2 class="h4 fw-light mb-2">Your cart is empty</h2>
                <p class="text-muted mb-4">Browse our collection and add items you love.</p>
                <a href="shop.php" class="btn btn-primary rounded-0 px-5">Explore Products</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="small text-muted"><?= getCartCount() ?> items in your cart</span>
                        <form method="POST" action="cart.php" onsubmit="return confirm('Clear all items?')">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn btn-sm text-muted p-0 border-0 bg-transparent text-decoration-underline">Clear Cart</button>
                        </form>
                    </div>
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex gap-4 py-3 border-bottom align-items-start">
                            <a href="product.php?slug=<?= htmlspecialchars($item['slug']) ?>" class="flex-shrink-0">
                                <img class="cart-item__image" src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
                            </a>
                            <div class="flex-grow-1">
                                <a href="product.php?slug=<?= htmlspecialchars($item['slug']) ?>" class="fw-medium text-dark text-decoration-none"><?= htmlspecialchars($item['name']) ?></a>
                                <p class="small text-muted mb-2"><?= formatPrice($item['price']) ?></p>
                                <div class="d-flex align-items-center gap-3">
                                    <form method="POST" action="cart.php" class="d-flex align-items-center">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                        <div class="input-group input-group-sm" style="width: 110px;">
                                            <button type="submit" name="quantity" value="<?= max(1, $item['cart_quantity'] - 1) ?>" class="btn btn-outline-secondary rounded-0" aria-label="Decrease">−</button>
                                            <span class="input-group-text bg-white rounded-0 small fw-medium"><?= $item['cart_quantity'] ?></span>
                                            <button type="submit" name="quantity" value="<?= $item['cart_quantity'] + 1 ?>" class="btn btn-outline-secondary rounded-0" aria-label="Increase">+</button>
                                        </div>
                                    </form>
                                    <form method="POST" action="cart.php">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                        <button type="submit" class="btn btn-sm text-muted text-decoration-underline p-0 border-0 bg-transparent">Remove</button>
                                    </form>
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <span class="fw-medium"><?= formatPrice($item['subtotal']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="col-lg-4">
                    <div class="border p-4 position-sticky" style="top: 96px;">
                        <h3 class="h6 fw-semibold mb-3 text-uppercase ls-wider small">Order Summary</h3>
                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span><?= formatPrice($total) ?></span>
                        </div>
                        <div class="d-flex justify-content-between small mb-3">
                            <span class="text-muted">Shipping</span>
                            <span><?= $total >= 500 ? 'Free' : formatPrice(15) ?></span>
                        </div>
                        <div class="d-flex justify-content-between fw-semibold fs-6 pt-3 border-top mb-3">
                            <span>Total</span>
                            <span><?= formatPrice($total >= 500 ? $total : $total + 15) ?></span>
                        </div>
                        <a href="checkout.php" class="btn btn-primary w-100 btn-lg rounded-0 mb-2">Checkout →</a>
                        <a href="shop.php" class="btn btn-outline-secondary w-100 rounded-0 small text-muted">Continue Shopping</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
