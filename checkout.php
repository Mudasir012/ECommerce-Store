<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
requireLogin();

$items = getCartItems($pdo);
$total = getCartTotal($items);

if (empty($items)) {
    header('Location: cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping = [
        'name' => $_POST['name'] ?? '',
        'address' => $_POST['address'] ?? '',
        'city' => $_POST['city'] ?? '',
        'postal' => $_POST['postal'] ?? '',
        'country' => $_POST['country'] ?? 'US',
    ];

    $errors = [];
    foreach ($shipping as $key => $val) {
        if (empty(trim($val))) $errors[$key] = 'This field is required.';
    }
    if (!filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Valid email required.';
    }

    if (empty($errors)) {
        $shippingTotal = $total >= 500 ? $total : $total + 15;
        $orderId = placeOrder($pdo, $_SESSION['user_id'], $shipping, $items, $shippingTotal);
        if ($orderId) {
            header('Location: account.php?action=order&id=' . $orderId . '&toast=' . urlencode('Order placed successfully'));
            exit;
        }
        $errors['general'] = 'Something went wrong. Please try again.';
    }
}

$pageTitle = 'Checkout';
include 'includes/header.php';
?>

<section class="py-4" style="padding-top: 2rem !important;">
    <div class="container" style="max-width: 1000px;">
        <h1 class="fw-light h3 mb-4 text-center">Checkout</h1>

        <!-- Steps -->
        <div class="checkout-steps mb-4">
            <div class="checkout-step checkout-step--done">
                <span class="checkout-step__number">✓</span>
                <span class="small fw-medium">Cart</span>
            </div>
            <div class="checkout-step__line checkout-step--done"></div>
            <div class="checkout-step checkout-step--active">
                <span class="checkout-step__number">2</span>
                <span class="small fw-medium">Delivery</span>
            </div>
            <div class="checkout-step__line"></div>
            <div class="checkout-step">
                <span class="checkout-step__number">3</span>
                <span class="small fw-medium">Pay</span>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <form method="POST" action="checkout.php" class="checkout-form" novalidate>
                    <?php if (!empty($errors['general'])): ?>
                        <div class="alert alert-danger py-2 small border-0 rounded-0 mb-3"><?= htmlspecialchars($errors['general']) ?></div>
                    <?php endif; ?>

                    <!-- Contact -->
                    <div class="checkout-section">
                        <h2 class="checkout-section__title">Contact</h2>
                        <div class="mb-0">
                            <label for="email" class="visually-hidden">Email</label>
                            <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" placeholder="Email address" value="<?= htmlspecialchars($_POST['email'] ?? $_SESSION['user_name'] ?? '') ?>" required>
                            <div class="invalid-feedback small"><?= $errors['email'] ?? '' ?></div>
                        </div>
                    </div>

                    <!-- Delivery -->
                    <div class="checkout-section">
                        <h2 class="checkout-section__title">Delivery</h2>
                        <div class="mb-3">
                            <label for="name" class="visually-hidden">Full Name</label>
                            <input type="text" id="name" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" placeholder="Full name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                            <div class="invalid-feedback small"><?= $errors['name'] ?? '' ?></div>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="visually-hidden">Address</label>
                            <input type="text" id="address" name="address" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>" placeholder="Street address" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>" required>
                            <div class="invalid-feedback small"><?= $errors['address'] ?? '' ?></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label for="city" class="visually-hidden">City</label>
                                <input type="text" id="city" name="city" class="form-control <?= isset($errors['city']) ? 'is-invalid' : '' ?>" placeholder="City" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>" required>
                                <div class="invalid-feedback small"><?= $errors['city'] ?? '' ?></div>
                            </div>
                            <div class="col-md-3">
                                <label for="postal" class="visually-hidden">Postal Code</label>
                                <input type="text" id="postal" name="postal" class="form-control <?= isset($errors['postal']) ? 'is-invalid' : '' ?>" placeholder="ZIP / Postal" value="<?= htmlspecialchars($_POST['postal'] ?? '') ?>" required>
                                <div class="invalid-feedback small"><?= $errors['postal'] ?? '' ?></div>
                            </div>
                            <div class="col-md-4">
                                <label for="country" class="visually-hidden">Country</label>
                                <select id="country" name="country" class="form-select" required>
                                    <option value="US" <?= ($_POST['country'] ?? 'US') === 'US' ? 'selected' : '' ?>>United States</option>
                                    <option value="CA" <?= ($_POST['country'] ?? '') === 'CA' ? 'selected' : '' ?>>Canada</option>
                                    <option value="GB" <?= ($_POST['country'] ?? '') === 'GB' ? 'selected' : '' ?>>United Kingdom</option>
                                    <option value="AU" <?= ($_POST['country'] ?? '') === 'AU' ? 'selected' : '' ?>>Australia</option>
                                    <option value="DE" <?= ($_POST['country'] ?? '') === 'DE' ? 'selected' : '' ?>>Germany</option>
                                    <option value="FR" <?= ($_POST['country'] ?? '') === 'FR' ? 'selected' : '' ?>>France</option>
                                    <option value="IT" <?= ($_POST['country'] ?? '') === 'IT' ? 'selected' : '' ?>>Italy</option>
                                    <option value="JP" <?= ($_POST['country'] ?? '') === 'JP' ? 'selected' : '' ?>>Japan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Payment -->
                    <div class="checkout-section">
                        <h2 class="checkout-section__title">Payment</h2>
                        <label class="payment-option selected d-flex">
                            <input type="radio" name="payment" value="card" checked>
                            <span class="fw-medium small flex-grow-1">Credit Card</span>
                            <span class="small text-muted">Visa &nbsp; MC &nbsp; Amex</span>
                        </label>
                        <p class="small text-muted mt-2 mb-0">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="vertical-align: middle; margin-right: 4px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            Your payment info is encrypted and secure.
                        </p>
                    </div>

                    <!-- Coupon -->
                    <div class="checkout-section text-center" style="padding: 0.75rem 1.5rem;">
                        <button type="button" class="coupon-toggle" onclick="document.getElementById('couponInput').classList.toggle('d-none')">+ Have a coupon code?</button>
                        <div id="couponInput" class="d-none mt-2">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Enter code">
                                <button class="btn btn-outline-primary rounded-0" type="button">Apply</button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg rounded-0 d-flex align-items-center justify-content-center gap-2">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Pay — <?= formatPrice($total >= 500 ? $total : $total + 15) ?>
                    </button>

                    <div class="checkout-trust">
                        <span>🔒 Secure checkout</span>
                        <span>↩ Easy returns</span>
                        <span>📦 Free shipping $500+</span>
                    </div>
                </form>
            </div>

            <!-- Summary -->
            <div class="col-lg-5">
                <div class="border p-4 position-sticky" style="top: 96px;">
                    <h3 class="h6 fw-semibold mb-3 text-uppercase ls-wider small">Order Summary</h3>
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 56px; height: 75px; object-fit: cover; flex-shrink: 0;">
                            <div class="flex-grow-1">
                                <p class="small fw-medium mb-0"><?= htmlspecialchars($item['name']) ?></p>
                                <p class="small text-muted mb-0">Qty: <?= $item['cart_quantity'] ?></p>
                            </div>
                            <span class="small fw-medium"><?= formatPrice($item['subtotal']) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Subtotal</span><span><?= formatPrice($total) ?></span></div>
                    <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Shipping</span><span><?= $total >= 500 ? 'Free' : formatPrice(15) ?></span></div>
                    <div class="d-flex justify-content-between fw-semibold fs-6 pt-3 mt-3 border-top"><span>Total</span><span><?= formatPrice($total >= 500 ? $total : $total + 15) ?></span></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="assets/js/checkout.js" defer></script>
<?php include 'includes/footer.php'; ?>
