<?php
require_once __DIR__ . '/../../classes/Admin.php';
require_once __DIR__ . '/../../includes/functions.php';
Admin::requireLogin();

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? h($pageTitle) . ' - Admin' : 'Admin Panel'; ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-wrap">
  <div class="admin-sidebar">
    <div class="brand">FreshCart Admin</div>
    <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">📊 Dashboard</a>
    <a href="products.php" class="<?php echo in_array($currentPage, ['products.php','add_product.php','edit_product.php']) ? 'active' : ''; ?>">📦 Products</a>
    <a href="categories.php" class="<?php echo $currentPage === 'categories.php' ? 'active' : ''; ?>">🏷️ Categories</a>
    <a href="orders.php" class="<?php echo in_array($currentPage, ['orders.php','view_order.php']) ? 'active' : ''; ?>">🧾 Orders</a>
    <a href="../index.php" target="_blank">🌐 View Site</a>
    <a href="logout.php">🚪 Logout (<?php echo h(Admin::getUsername()); ?>)</a>
  </div>
  <div class="admin-main">
