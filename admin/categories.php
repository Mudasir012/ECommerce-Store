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

        if ($formAction === 'add') {
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
            $stmt->execute([$name, $slug, $description]);
        } else {
            $stmt = $pdo->prepare("UPDATE categories SET name=?, slug=?, description=? WHERE id=?");
            $stmt->execute([$name, $slug, $description, $id]);
        }
        header('Location: categories.php');
        exit;
    }

    if ($formAction === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: categories.php');
        exit;
    }
}

$editCat = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editCat = $stmt->fetch();
}

$stmt = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) AS product_count FROM categories c ORDER BY c.sort_order ASC");
$categories = $stmt->fetchAll();

$pageTitle = 'Manage Categories';
include __DIR__ . '/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-light h4 mb-0">Categories</h2>
    <button class="btn btn-primary btn-sm" onclick="document.getElementById('catForm').classList.toggle('d-none')">
        <?= $editCat ? 'Edit Category' : '+ Add Category' ?>
    </button>
</div>

<div id="catForm" class="border p-4 mb-4 bg-light <?= $editCat ? '' : 'd-none' ?>">
    <form method="POST" action="categories.php">
        <input type="hidden" name="form_action" value="<?= $editCat ? 'edit' : 'add' ?>">
        <?php if ($editCat): ?>
            <input type="hidden" name="id" value="<?= $editCat['id'] ?>">
        <?php endif; ?>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-medium">Name</label>
                <input type="text" name="name" class="form-control form-control-sm" value="<?= htmlspecialchars($editCat['name'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-medium">Slug</label>
                <input type="text" name="slug" class="form-control form-control-sm" value="<?= htmlspecialchars($editCat['slug'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-medium">Description</label>
                <input type="text" name="description" class="form-control form-control-sm" value="<?= htmlspecialchars($editCat['description'] ?? '') ?>">
            </div>
        </div>
        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm"><?= $editCat ? 'Update' : 'Add' ?></button>
            <?php if ($editCat): ?>
                <a href="categories.php" class="btn btn-outline-secondary btn-sm">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="table-responsive">
    <table class="table table-sm align-middle">
        <thead class="table-light">
            <tr><th>Name</th><th>Slug</th><th>Products</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td class="small"><?= htmlspecialchars($cat['name']) ?></td>
                    <td class="small text-muted"><?= htmlspecialchars($cat['slug']) ?></td>
                    <td><?= $cat['product_count'] ?></td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="categories.php?edit=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form method="POST" action="categories.php" onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($cat['name'])) ?>?')">
                                <input type="hidden" name="form_action" value="delete">
                                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
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
