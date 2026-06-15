<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isAdmin()) {
    die('Access denied.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formAction = $_POST['form_action'] ?? '';

    if ($formAction === 'add' || $formAction === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $name = $_POST['name'] ?? '';
        $slug = $_POST['slug'] ?: slugify($name);
        $description = $_POST['description'] ?? '';
        $price = (float)($_POST['price'] ?? 0);
        $comparePrice = $_POST['compare_price'] ? (float)$_POST['compare_price'] : null;
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $gender = $_POST['gender'] ?? 'men';
        $image = $_POST['image'] ?? '';
        $imageAlt = $_POST['image_alt'] ?? '';
        $stock = (int)($_POST['stock'] ?? 0);
        $featured = isset($_POST['featured']) ? 1 : 0;
        $details = $_POST['details'] ?? '';

        if ($formAction === 'add') {
            $stmt = $pdo->prepare("INSERT INTO products (category_id, gender, name, slug, description, details, price, compare_price, image, image_alt, stock, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$categoryId, $gender, $name, $slug, $description, $details, $price, $comparePrice, $image, $imageAlt, $stock, $featured]);
        } else {
            $stmt = $pdo->prepare("UPDATE products SET category_id=?, gender=?, name=?, slug=?, description=?, details=?, price=?, compare_price=?, image=?, image_alt=?, stock=?, featured=? WHERE id=?");
            $stmt->execute([$categoryId, $gender, $name, $slug, $description, $details, $price, $comparePrice, $image, $imageAlt, $stock, $featured, $id]);
        }
        header('Location: products.php');
        exit;
    }

    if ($formAction === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: products.php');
        exit;
    }
}

$editProduct = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editProduct = $stmt->fetch();
}

$stmt = $pdo->query("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
$products = $stmt->fetchAll();

$categories = getCategories($pdo);

$pageTitle = 'Manage Products';
include __DIR__ . '/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-light h4 mb-0">Products</h2>
    <button class="btn btn-primary btn-sm" onclick="document.getElementById('productForm').classList.toggle('d-none')">
        <?= $editProduct ? 'Edit Product' : '+ Add Product' ?>
    </button>
</div>

<div id="productForm" class="border p-4 mb-4 bg-light <?= $editProduct ? '' : 'd-none' ?>">
    <form method="POST" action="products.php">
        <input type="hidden" name="form_action" value="<?= $editProduct ? 'edit' : 'add' ?>">
        <?php if ($editProduct): ?>
            <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
        <?php endif; ?>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-medium">Name</label>
                <input type="text" name="name" class="form-control form-control-sm" value="<?= htmlspecialchars($editProduct['name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-medium">Slug (blank = auto)</label>
                <input type="text" name="slug" class="form-control form-control-sm" value="<?= htmlspecialchars($editProduct['slug'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-medium">Category</label>
                <select name="category_id" class="form-select form-select-sm" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($editProduct['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-medium">Gender</label>
                <select name="gender" class="form-select form-select-sm">
                    <option value="men" <?= ($editProduct['gender'] ?? 'men') === 'men' ? 'selected' : '' ?>>Men</option>
                    <option value="women" <?= ($editProduct['gender'] ?? '') === 'women' ? 'selected' : '' ?>>Women</option>
                    <option value="kids" <?= ($editProduct['gender'] ?? '') === 'kids' ? 'selected' : '' ?>>Kids</option>
                    <option value="unisex" <?= ($editProduct['gender'] ?? '') === 'unisex' ? 'selected' : '' ?>>Unisex</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-medium">Stock</label>
                <input type="number" name="stock" class="form-control form-control-sm" value="<?= $editProduct['stock'] ?? 0 ?>" min="0">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-medium">Price</label>
                <input type="number" step="0.01" name="price" class="form-control form-control-sm" value="<?= $editProduct['price'] ?? '' ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-medium">Compare Price</label>
                <input type="number" step="0.01" name="compare_price" class="form-control form-control-sm" value="<?= $editProduct['compare_price'] ?? '' ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-medium">Image URL</label>
                <input type="url" name="image" class="form-control form-control-sm" value="<?= htmlspecialchars($editProduct['image'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-medium">Image Alt Text</label>
                <input type="text" name="image_alt" class="form-control form-control-sm" value="<?= htmlspecialchars($editProduct['image_alt'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label small fw-medium">Description</label>
                <textarea name="description" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($editProduct['description'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label small fw-medium">Details</label>
                <textarea name="details" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($editProduct['details'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input type="checkbox" name="featured" id="featured" value="1" class="form-check-input" <?= ($editProduct['featured'] ?? 0) ? 'checked' : '' ?>>
                    <label for="featured" class="form-check-label small">Featured product</label>
                </div>
            </div>
        </div>
        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm"><?= $editProduct ? 'Update' : 'Add' ?> Product</button>
            <?php if ($editProduct): ?>
                <a href="products.php" class="btn btn-outline-secondary btn-sm">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-sm align-middle">
            <thead class="table-light">
                <tr><th></th><th>Name</th><th>Gender</th><th>Category</th><th>Price</th><th>Stock</th><th>Featured</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <?php if ($product['image']): ?>
                                <img src="<?= htmlspecialchars($product['image']) ?>" alt="" style="width: 32px; height: 43px; object-fit: cover;">
                            <?php endif; ?>
                        </td>
                        <td class="small"><?= htmlspecialchars($product['name']) ?></td>
                        <td><span class="badge bg-light text-dark rounded-0 small text-uppercase ls-wider" style="font-size: 0.6rem;"><?= $product['gender'] ?? 'men' ?></span></td>
                        <td class="small"><?= htmlspecialchars($product['category_name'] ?? '') ?></td>
                        <td><?= formatPrice($product['price']) ?></td>
                        <td><?= $product['stock'] ?></td>
                        <td><?= $product['featured'] ? '✓' : '' ?></td>
                        <td>
                        <div class="d-flex gap-1">
                            <a href="products.php?edit=<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="POST" action="products.php" onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($product['name'])) ?>?')">
                                <input type="hidden" name="form_action" value="delete">
                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    </td>
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
