<?php
// Create test user script
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();

// Create test user with known password
$username = 'testuser';
$password = 'test123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (name, username, email, password, role_id, status) VALUES (?, ?, ?, ?, ?, ?)";
$params = [
    'Test User',
    $username, 
    'test@example.com',
    $hashed_password,
    2, // Admin role
    1  // Active
];

try {
    $db->execute($sql, $params);
    echo "Test user created successfully\n";
    echo "Username: $username\n";
    echo "Password: $password\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
