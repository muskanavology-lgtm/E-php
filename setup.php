<?php
/**
 * Run this file ONCE in your browser (e.g. http://localhost/ecommerce-site/setup.php)
 * AFTER importing database/schema.sql.
 *
 * It creates the default admin login:
 *   username: admin
 *   password: admin123
 *
 * You can delete this file after running it once.
 */
require_once __DIR__ . '/config/Database.php';

$conn = Database::getInstance()->getConnection();

$stmt = $conn->query("SELECT COUNT(*) AS total FROM admins");
$row = $stmt->fetch();

if ($row && $row['total'] > 0) {
    echo "<h2>Setup already done.</h2>";
    echo "<p>An admin account already exists. Go to <a href='admin/login.php'>admin/login.php</a> to log in.</p>";
} else {
    $username = 'admin';
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $insert = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $insert->execute([$username, $hash]);

    echo "<h2>Setup complete! ✅</h2>";
    echo "<p>Default admin account created:</p>";
    echo "<ul><li><b>Username:</b> admin</li><li><b>Password:</b> admin123</li></ul>";
    echo "<p><b>Please change this password after logging in for the first time.</b></p>";
    echo "<p><a href='admin/login.php'>Click here to go to the Admin Login page</a></p>";
    echo "<p><a href='index.php'>Click here to go to the Home Page</a></p>";
}
