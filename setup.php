<?php
/**
 * LUXE Ecommerce — Setup Script
 * Run once after importing sql/schema.sql:
 *   php setup.php
 */

$host = 'localhost';
$db = 'luxe_store';
$user = 'luxe';
$pass = 'luxepass';

echo "LUXE Store Setup\n";
echo "================\n\n";

// Connect
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "✓ Connected to database\n";
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    echo "  Make sure MariaDB is running and the database 'luxe_store' exists.\n";
    exit(1);
}

// Create admin user
$email = 'admin@luxe.com';
$password = 'admin';
$name = 'Admin';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    // Update existing
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, role = 'admin', name = ? WHERE email = ?");
    $stmt->execute([$hash, $name, $email]);
    echo "✓ Admin user updated (admin@luxe.com / admin)\n";
} else {
    // Create new
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'admin')");
    $stmt->execute([$name, $email, $hash]);
    echo "✓ Admin user created (admin@luxe.com / admin)\n";
}

echo "\nDone. You can now log in at http://localhost:8000/account.php\n";
echo "Admin panel: http://localhost:8000/admin/index.php\n";
