<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdmin()) {
    die('Access denied.');
}

$orderId = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    die('Order not found.');
}

$items = getOrderItems($pdo, $orderId);

$pageTitle = 'Order #' . $orderId;
include __DIR__ . '/admin-header.php';
?>

<a href="orders.php" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>

<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="fw-light h4 mb-1">Order #<?= $order['id'] ?></h2>
        <p class="small text-muted mb-0">Placed on <?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?></p>
    </div>
    <span class="badge bg-<?= match($order['status']) { 'pending'=>'secondary','confirmed'=>'primary','shipped'=>'info','delivered'=>'success','cancelled'=>'danger',default=>'secondary' } ?> fs-6"><?= ucfirst($order['status']) ?></span>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="border p-3">
            <h5 class="small text-uppercase text-muted fw-semibold mb-2">Shipping</h5>
            <p class="small mb-0"><?= htmlspecialchars($order['shipping_name']) ?><br>
            <?= htmlspecialchars($order['shipping_address']) ?><br>
            <?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_postal']) ?><br>
            <?= htmlspecialchars($order['shipping_country']) ?></p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="border p-3">
            <h5 class="small text-uppercase text-muted fw-semibold mb-2">Summary</h5>
            <div class="d-flex justify-content-between small mb-1"><span>Subtotal</span><span><?= formatPrice($order['total']) ?></span></div>
            <div class="d-flex justify-content-between small mb-1"><span>Shipping</span><span><?= $order['total'] >= 500 ? 'Free' : formatPrice(15) ?></span></div>
            <div class="d-flex justify-content-between fw-semibold pt-2 border-top mt-2"><span>Total</span><span><?= formatPrice($order['total']) ?></span></div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-sm align-middle">
        <thead class="table-light">
            <tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <?php if ($item['image']): ?>
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="" style="width: 40px; height: 53px; object-fit: cover;">
                            <?php endif; ?>
                            <span class="small"><?= htmlspecialchars($item['name']) ?></span>
                        </div>
                    </td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= formatPrice($item['price']) ?></td>
                    <td><?= formatPrice($item['price'] * $item['quantity']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
