<?php
require_once __DIR__ . '/../classes/Order.php';

$pageTitle = 'Orders';
require_once __DIR__ . '/includes/admin_header.php';

$orderModel = new Order();
$orders = $orderModel->getAll();
?>

<div class="admin-topbar">
  <h1>Orders</h1>
</div>

<table class="admin-table">
  <tr>
    <th>#</th>
    <th>Customer</th>
    <th>Phone</th>
    <th>Total</th>
    <th>Status</th>
    <th>Date</th>
    <th>Actions</th>
  </tr>
  <?php if (empty($orders)): ?>
    <tr><td colspan="7">No orders placed yet.</td></tr>
  <?php else: ?>
    <?php foreach ($orders as $order): ?>
      <tr>
        <td>#<?php echo (int)$order['id']; ?></td>
        <td><?php echo h($order['customer_name']); ?></td>
        <td><?php echo h($order['phone']); ?></td>
        <td><?php echo formatPrice($order['total']); ?></td>
        <td><span class="status-tag"><?php echo h($order['status']); ?></span></td>
        <td><?php echo h($order['created_at']); ?></td>
        <td><a href="view_order.php?id=<?php echo (int)$order['id']; ?>" class="btn-small btn-edit">View</a></td>
      </tr>
    <?php endforeach; ?>
  <?php endif; ?>
</table>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
