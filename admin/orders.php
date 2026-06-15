<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdmin()) {
    die('Access denied.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], (int)$_POST['order_id']]);
    header('Location: orders.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll();

$pageTitle = 'Manage Orders';
include __DIR__ . '/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-light h4 mb-0">Orders</h2>
</div>

<?php if (empty($orders)): ?>
    <p class="text-muted small">No orders found.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead class="table-light">
                <tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['shipping_name']) ?><br><span class="small text-muted"><?= htmlspecialchars($order['shipping_city']) ?></span></td>
                        <td><?= formatPrice($order['total']) ?></td>
                        <td>
                            <form method="POST" action="orders.php" class="d-inline">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                                    <?php foreach (['pending','confirmed','shipped','delivered','cancelled'] as $s): ?>
                                        <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        <td class="small"><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                        <td><a href="order-detail.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">Detail</a></td>
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
