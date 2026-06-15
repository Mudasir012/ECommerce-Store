<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdmin()) {
    die('Access denied.');
}

$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$productCount = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$orderCount = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$userCount = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'cancelled'");
$revenue = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
$pendingOrders = $stmt->fetchColumn();

$pageTitle = 'Admin Dashboard';
include __DIR__ . '/admin-header.php';
?>

<h2 class="fw-light h4 mb-4">Dashboard</h2>

<div class="row g-3 mb-4">
    <div class="col-6 col-md">
        <div class="border p-3 text-center">
            <div class="h3 fw-light mb-1"><?= $productCount ?></div>
            <div class="small text-muted">Products</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="border p-3 text-center">
            <div class="h3 fw-light mb-1"><?= $orderCount ?></div>
            <div class="small text-muted">Total Orders</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="border p-3 text-center">
            <div class="h3 fw-light mb-1"><?= $pendingOrders ?></div>
            <div class="small text-muted">Pending</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="border p-3 text-center">
            <div class="h3 fw-light mb-1"><?= formatPrice($revenue) ?></div>
            <div class="small text-muted">Revenue</div>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="border p-3 text-center">
            <div class="h3 fw-light mb-1"><?= $userCount ?></div>
            <div class="small text-muted">Users</div>
        </div>
    </div>
</div>

<h3 class="fw-light h5 mb-3">Recent Orders</h3>
<?php
$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
$recentOrders = $stmt->fetchAll();
?>
<?php if (empty($recentOrders)): ?>
    <p class="text-muted small">No orders yet.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead class="table-light">
                <tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['shipping_name']) ?></td>
                        <td><?= formatPrice($order['total']) ?></td>
                        <td><span class="badge bg-<?= match($order['status']) { 'pending'=>'secondary','confirmed'=>'primary','shipped'=>'info','delivered'=>'success','cancelled'=>'danger',default=>'secondary' } ?>"><?= ucfirst($order['status']) ?></span></td>
                        <td class="small"><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                        <td><a href="orders.php" class="btn btn-sm btn-outline-primary">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

</main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
