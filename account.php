<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$action = $_GET['action'] ?? '';

if ($action === 'logout') {
    require_once 'includes/auth.php';
    logoutUser();
    header('Location: account.php?action=login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/auth.php';
    $formAction = $_POST['form_action'] ?? '';

    if ($formAction === 'login') {
        $result = loginUser($pdo, $_POST['email'] ?? '', $_POST['password'] ?? '');
        if ($result['success']) { header('Location: account.php'); exit; }
        $error = $result['error'];
    } elseif ($formAction === 'register') {
        $result = registerUser($pdo, $_POST['name'] ?? '', $_POST['email'] ?? '', $_POST['password'] ?? '');
        if ($result['success']) { header('Location: account.php'); exit; }
        $error = $result['error'];
    } elseif ($formAction === 'update_profile') {
        require_once 'includes/auth.php';
        $result = updateUserProfile($pdo, $_SESSION['user_id'], $_POST);
        if ($result['success']) { header('Location: account.php?toast=Profile updated'); exit; }
        $error = $result['error'];
    }
}

$pageTitle = $action === 'register' ? 'Create Account' : ($action === 'login' || !isLoggedIn() ? 'Sign In' : 'My Account');
$isAuth = !isLoggedIn();
include 'includes/header.php';
?>

<?php if ($isAuth): ?>
<div class="auth-page">
    <div class="auth-hero">
        <img class="auth-hero__bg" src="https://images.unsplash.com/photo-1441984904996-e0b6ba687e04?w=1200&q=85" alt="">
        <div class="auth-hero__overlay"></div>
        <div class="auth-hero__content">
            <p class="text-uppercase ls-wider small fw-medium mb-2 opacity-75"><?= SITE_NAME ?></p>
            <h2 class="h3 fw-light">Quality you can feel.<br>Service you can trust.</h2>
        </div>
    </div>

    <div class="auth-panel">
        <?php if ($action === 'register'): ?>
            <p class="auth-panel__brand"><?= SITE_NAME ?></p>
            <h1 class="auth-panel__title">Create your account</h1>
            <p class="auth-panel__sub">Join LUXE for a seamless shopping experience.</p>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger py-2 small border-0 rounded-0 mb-3"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="account.php?action=register">
                <input type="hidden" name="form_action" value="register">
                <div class="mb-3">
                    <label for="reg-name" class="form-label">Full Name</label>
                    <input type="text" id="reg-name" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="reg-email" class="form-label">Email</label>
                    <input type="email" id="reg-email" name="email" class="form-control" required>
                </div>
                <div class="mb-3 position-relative">
                    <label for="reg-password" class="form-label">Password</label>
                    <input type="password" id="reg-password" name="password" class="form-control" minlength="8" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('reg-password', this)">Show</button>
                </div>
                <button type="submit" class="btn btn-primary w-100 btn-lg rounded-0 mt-2">Create Account</button>
            </form>

            <div class="auth-divider mt-4 mb-3">or</div>

            <button class="btn btn-outline-secondary w-100 rounded-0 mb-3" disabled style="opacity: 0.5;">
                <svg width="16" height="16" viewBox="0 0 24 24" class="me-2" style="vertical-align: middle;"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                Continue with Google
            </button>

            <p class="small text-center text-muted mt-3 mb-0">Already have an account? <a href="account.php?action=login" class="fw-medium">Sign In</a></p>
        <?php else: ?>
            <p class="auth-panel__brand"><?= SITE_NAME ?></p>
            <h1 class="auth-panel__title">Welcome back</h1>
            <p class="auth-panel__sub">Sign in to access your account and orders.</p>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger py-2 small border-0 rounded-0 mb-3"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="account.php?action=login">
                <input type="hidden" name="form_action" value="login">
                <div class="mb-3">
                    <label for="login-email" class="form-label">Email</label>
                    <input type="email" id="login-email" name="email" class="form-control" required>
                </div>
                <div class="mb-3 position-relative">
                    <label for="login-password" class="form-label">Password</label>
                    <input type="password" id="login-password" name="password" class="form-control" required>
                    <button type="button" class="password-toggle" onclick="togglePassword('login-password', this)">Show</button>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="d-flex align-items-center gap-2 small">
                        <input type="checkbox" name="remember" value="1" style="accent-color: var(--brand-green);">
                        Remember me
                    </label>
                </div>
                <button type="submit" class="btn btn-primary w-100 btn-lg rounded-0">Sign In</button>
            </form>

            <div class="auth-divider mt-4 mb-3">or</div>

            <button class="btn btn-outline-secondary w-100 rounded-0 mb-3" disabled style="opacity: 0.5;">
                <svg width="16" height="16" viewBox="0 0 24 24" class="me-2" style="vertical-align: middle;"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                Continue with Google
            </button>

            <p class="small text-center text-muted mt-3 mb-0">Don't have an account? <a href="account.php?action=register" class="fw-medium">Create One</a></p>
        <?php endif; ?>
    </div>
</div>

<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = 'Hide';
    } else {
        input.type = 'password';
        btn.textContent = 'Show';
    }
}
</script>

<?php else: /* Logged in dashboard */ ?>
<?php
$orders = getUserOrders($pdo, $_SESSION['user_id']);
$activeTab = match($action) {
    'orders' => 'orders',
    'order' => 'order',
    default => 'profile',
};
?>

<section class="py-4" style="padding-top: 2rem !important;">
    <div class="container" style="max-width: 1100px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <p class="text-uppercase ls-wider small fw-medium text-muted mb-1">Account</p>
                <h1 class="fw-light h2 mb-0">Hello, <?= htmlspecialchars($_SESSION['user_name']) ?></h1>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-3">
                <nav class="list-group list-group-flush position-sticky" style="top: 88px;">
                    <a href="account.php" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 <?= $activeTab === 'profile' ? 'active' : '' ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Profile
                    </a>
                    <a href="account.php?action=orders" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 <?= $activeTab === 'orders' ? 'active' : '' ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        Orders
                    </a>
                    <a href="account.php?action=logout" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 text-danger">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Sign Out
                    </a>
                </nav>
            </div>

            <div class="col-lg-9">
                <?php if ($activeTab === 'order' && isset($_GET['id'])): ?>
                    <?php
                    $orderId = (int)$_GET['id'];
                    $orderItems = getOrderItems($pdo, $orderId);
                    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
                    $stmt->execute([$orderId, $_SESSION['user_id']]);
                    $order = $stmt->fetch();
                    if (!$order): ?>
                        <p class="text-muted">Order not found.</p>
                    <?php else: ?>
                        <div class="border p-4">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <h2 class="h5 fw-medium mb-1">Order #<?= $order['id'] ?></h2>
                                    <p class="small text-muted mb-0"><?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?></p>
                                </div>
                                <span class="badge rounded-0 px-3 py-2 fw-medium text-uppercase small ls-wider bg-<?= match($order['status']) { 'pending'=>'secondary', 'confirmed'=>'primary', 'shipped'=>'info', 'delivered'=>'success', 'cancelled'=>'danger', default=>'secondary' } ?>"><?= ucfirst($order['status']) ?></span>
                            </div>
                            <div class="p-3 small" style="background: var(--brand-surface);">
                                <strong class="d-block mb-1">Shipping Address</strong>
                                <?= htmlspecialchars($order['shipping_name']) ?><br>
                                <?= htmlspecialchars($order['shipping_address']) ?><br>
                                <?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_postal']) ?>
                            </div>
                            <div class="table-responsive mt-3">
                                <table class="table table-sm align-middle">
                                    <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($orderItems as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['name']) ?></td>
                                            <td><?= $item['quantity'] ?></td>
                                            <td><?= formatPrice($item['price']) ?></td>
                                            <td><?= formatPrice($item['price'] * $item['quantity']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end pt-3 border-top"><strong class="fs-5">Total: <?= formatPrice($order['total']) ?></strong></div>
                        </div>
                    <?php endif; ?>
                <?php elseif ($activeTab === 'orders'): ?>
                    <h2 class="h5 fw-medium mb-3">Order History</h2>
                    <?php if (empty($orders)): ?>
                        <div class="text-center py-5 border" style="padding: 4rem 0 !important;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.75" class="text-muted mb-3"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                            <h3 class="h5 fw-light mb-2">No orders yet</h3>
                            <p class="text-muted small mb-3">Your order history will appear here once you make a purchase.</p>
                            <a href="shop.php" class="btn btn-primary rounded-0 px-4">Start Shopping</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead><tr><th>Order</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr></thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td class="fw-medium">#<?= $order['id'] ?></td>
                                        <td class="small text-muted"><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                        <td><?= formatPrice($order['total']) ?></td>
                                        <td><span class="badge rounded-0 fw-medium bg-<?= match($order['status']) { 'pending'=>'secondary', 'confirmed'=>'primary', 'shipped'=>'info', 'delivered'=>'success', 'cancelled'=>'danger', default=>'secondary' } ?>"><?= ucfirst($order['status']) ?></span></td>
                                        <td><a href="account.php?action=order&id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary rounded-0">View</a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <h2 class="h5 fw-medium mb-3">Profile</h2>
                    <?php if (isset($error)): ?><div class="alert alert-danger py-2 small border-0 rounded-0 mb-3"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                    <div class="border p-4" style="max-width: 600px;">
                        <form method="POST" action="account.php">
                            <input type="hidden" name="form_action" value="update_profile">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" disabled style="opacity: 0.6;">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" id="address" name="address" class="form-control" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-5">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" id="city" name="city" class="form-control" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="postal_code" class="form-label">Postal</label>
                                    <input type="text" id="postal_code" name="postal_code" class="form-control" value="<?= htmlspecialchars($_POST['postal_code'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="country" class="form-label">Country</label>
                                    <select id="country" name="country" class="form-select">
                                        <option value="US">United States</option>
                                        <option value="CA">Canada</option>
                                        <option value="GB">United Kingdom</option>
                                        <option value="AU">Australia</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-0 px-4">Update Profile</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
