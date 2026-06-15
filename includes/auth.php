<?php
require_once __DIR__ . '/config.php';

function registerUser(PDO $pdo, string $name, string $email, string $password): array {
    if (empty($name) || empty($email) || empty($password)) {
        return ['success' => false, 'error' => 'All fields are required.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Invalid email address.'];
    }
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'An account with this email already exists.'];
    }
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $hash]);
    $_SESSION['user_id'] = (int) $pdo->lastInsertId();
    $_SESSION['user_name'] = $name;
    $_SESSION['user_role'] = 'user';
    return ['success' => true];
}

function loginUser(PDO $pdo, string $email, string $password): array {
    if (empty($email) || empty($password)) {
        return ['success' => false, 'error' => 'Email and password are required.'];
    }
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'error' => 'Invalid email or password.'];
    }
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'] ?? 'user';
    return ['success' => true];
}

function logoutUser(): void {
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_role']);
}

function updateUserProfile(PDO $pdo, int $userId, array $data): array {
    $fields = [];
    $params = [];
    foreach (['name', 'address', 'city', 'postal_code', 'country'] as $field) {
        if (isset($data[$field]) && $data[$field] !== '') {
            $fields[] = "{$field} = ?";
            $params[] = $data[$field];
        }
    }
    if (empty($fields)) {
        return ['success' => false, 'error' => 'No fields to update.'];
    }
    $params[] = $userId;
    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    if (isset($data['name'])) {
        $_SESSION['user_name'] = $data['name'];
    }
    return ['success' => true];
}

function placeOrder(PDO $pdo, int $userId, array $shipping, array $items, float $total): ?int {
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total, status, shipping_name, shipping_address, shipping_city, shipping_postal, shipping_country) VALUES (?, ?, 'pending', ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            $total,
            $shipping['name'],
            $shipping['address'],
            $shipping['city'],
            $shipping['postal'],
            $shipping['country'] ?? 'US'
        ]);
        $orderId = (int) $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $stmt->execute([$orderId, $item['id'], $item['cart_quantity'], $item['price']]);
        }
        $pdo->commit();
        unset($_SESSION['cart']);
        return $orderId;
    } catch (Exception $e) {
        $pdo->rollBack();
        return null;
    }
}
