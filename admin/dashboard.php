<?php
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Order.php';
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/admin_header.php';
$productModel = new Product();
$categoryModel = new Category();
$orderModel = new Order();
$totalProducts = $productModel->countAll();
$totalCategories = count($categoryModel->getAll());
$orders = $orderModel->getAll();
$totalOrders = count($orders);
$totalRevenue = array_sum(array_column($orders, 'total'));
?>
<div class="admin-topbar">
  <h1>Dashboard</h1>
</div>
<div class="stat
-cards">
  <div class="stat-card">
    <div class="num"><?php echo $totalProducts; ?></div>
    <div class="label">Total Products</div>
  </div>
  <div class="stat-card">
    <div class="num"><?php echo $totalCategories; ?></div>
    <div class="label">Total Categories</div>
  </div>
  <div class="stat-card">
    <div class="num"><?php echo $totalOrders; ?></div>
    <div class="label">Total Orders</div>
  </div>
  <div class="stat-card">
    <div class="num"><?php echo formatPrice($totalRevenue); ?></div>
    <div class="label">Total Revenue</div>
  </div>
</div>
<h2 class="section-title" style="margin-top:0;">Recent Orders</h2>
<table class="admin-table">
  <tr>
    <th>#</th>
    <th>Customer</th>
    <th>Phone</th>
    <th>Total</th>
    <th>Status</th>
    <th>Date</th>
  </tr>
  <?php if (empty($orders)): ?>
    <tr>
      <td colspan="6">No orders placed yet.</td>
    </tr>
  <?php else: ?>
    <?php foreach (array_slice($orders, 0, 5) as $order): ?>
      <tr>
        <td>#<?php echo (int)$order['id']; ?></td>
        <td><?php echo h($order['customer_name']); ?></td>
        <td><?php echo h($order['phone']); ?></td>
        <td><?php echo formatPrice($order['total']); ?></td>
        <td><span class="status-tag"><?php echo h($order['status']); ?></span></td>
        <td><?php echo h($order['created_at']); ?></td>
      </tr>
    <?php endforeach; ?>
  <?php endif; ?>
</table>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>