<?php
require_once __DIR__ . '/config.php';

function getCategories(PDO $pdo): array {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC");
    return $stmt->fetchAll();
}

function getFeaturedProducts(PDO $pdo, int $limit = 6, ?string $gender = null): array {
    $sql = "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.featured = 1";
    $params = [];
    if ($gender) {
        $sql .= " AND p.gender = ?";
        $params[] = $gender;
    }
    $sql .= " ORDER BY p.created_at DESC LIMIT " . intval($limit);
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getProductBySlug(PDO $pdo, string $slug): ?array {
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.slug = ?");
    $stmt->execute([$slug]);
    $product = $stmt->fetch();
    return $product ?: null;
}

function getProductsByGender(PDO $pdo, string $gender, ?int $categoryId = null, string $sort = 'newest', int $page = 1, int $perPage = 12): array {
    $orderClause = match($sort) {
        'price-asc'  => 'p.price ASC',
        'price-desc' => 'p.price DESC',
        'name'       => 'p.name ASC',
        default      => 'p.created_at DESC',
    };
    $offset = intval(($page - 1) * $perPage);
    $perPage = intval($perPage);
    $sql = "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.gender = ?";
    $params = [$gender];
    if ($categoryId) {
        $sql .= " AND p.category_id = ?";
        $params[] = $categoryId;
    }
    $sql .= " ORDER BY {$orderClause} LIMIT {$perPage} OFFSET {$offset}";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getProductCountByGender(PDO $pdo, string $gender, ?int $categoryId = null): int {
    $sql = "SELECT COUNT(*) FROM products WHERE gender = ?";
    $params = [$gender];
    if ($categoryId) {
        $sql .= " AND category_id = ?";
        $params[] = $categoryId;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

function getProductsByCategory(PDO $pdo, int $categoryId, string $sort = 'newest', int $page = 1, int $perPage = 12): array {
    $orderClause = match($sort) {
        'price-asc'  => 'p.price ASC',
        'price-desc' => 'p.price DESC',
        'name'       => 'p.name ASC',
        default      => 'p.created_at DESC',
    };
    $offset = intval(($page - 1) * $perPage);
    $perPage = intval($perPage);
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? ORDER BY {$orderClause} LIMIT {$perPage} OFFSET {$offset}");
    $stmt->execute([$categoryId]);
    return $stmt->fetchAll();
}

function getProductCountByCategory(PDO $pdo, int $categoryId): int {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $stmt->execute([$categoryId]);
    return (int) $stmt->fetchColumn();
}

function addToCart(int $productId, int $quantity = 1): void {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

function updateCartQuantity(int $productId, int $quantity): void {
    if ($quantity <= 0) {
        unset($_SESSION['cart'][$productId]);
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

function removeFromCart(int $productId): void {
    unset($_SESSION['cart'][$productId]);
}

function getCartItems(PDO $pdo): array {
    $items = [];
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        return $items;
    }
    $ids = array_keys($_SESSION['cart']);
    if (empty($ids)) return $items;

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ({$placeholders})");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    foreach ($products as $product) {
        $product['cart_quantity'] = $_SESSION['cart'][$product['id']];
        $product['subtotal'] = $product['price'] * $product['cart_quantity'];
        $items[] = $product;
    }
    return $items;
}

function getCartTotal(array $items): float {
    return array_sum(array_column($items, 'subtotal'));
}

function searchProducts(PDO $pdo, string $query): array {
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.name LIKE ? OR p.description LIKE ? ORDER BY p.created_at DESC");
    $like = '%' . $query . '%';
    $stmt->execute([$like, $like]);
    return $stmt->fetchAll();
}

function getRelatedProducts(PDO $pdo, int $categoryId, int $excludeId, int $limit = 4): array {
    $limit = intval($limit);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? ORDER BY RAND() LIMIT {$limit}");
    $stmt->execute([$categoryId, $excludeId]);
    return $stmt->fetchAll();
}

function getUserOrders(PDO $pdo, int $userId): array {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getOrderItems(PDO $pdo, int $orderId): array {
    $stmt = $pdo->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $stmt->execute([$orderId]);
    return $stmt->fetchAll();
}
