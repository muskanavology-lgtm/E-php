<?php
require_once __DIR__ . '/../classes/Product.php';

$pageTitle = 'Products';
require_once __DIR__ . '/includes/admin_header.php';

$productModel = new Product();
$products = $productModel->getAll();
?>

<div class="admin-topbar">
  <h1>Products</h1>
  <a href="add_product.php" class="btn-new">+ Add New Product</a>
</div>

<?php if (isset($_GET['deleted'])): ?>
  <div class="alert alert-success">Product deleted successfully.</div>
<?php endif; ?>
<?php if (isset($_GET['saved'])): ?>
  <div class="alert alert-success">Product saved successfully.</div>
<?php endif; ?>

<table class="admin-table">
  <tr>
    <th>Image</th>
    <th>Name</th>
    <th>Category</th>
    <th>Price</th>
    <th>Stock</th>
    <th>Actions</th>
  </tr>
  <?php if (empty($products)): ?>
    <tr><td colspan="6">No products yet. Click "Add New Product" to create one.</td></tr>
  <?php else: ?>
    <?php foreach ($products as $p): ?>
      <tr>
        <td><img src="<?php echo '../' . productImage($p['image']); ?>" alt=""></td>
        <td><?php echo h($p['name']); ?></td>
        <td><?php echo h($p['category_name'] ?? '—'); ?></td>
        <td><?php echo formatPrice($p['price']); ?></td>
        <td><?php echo (int)$p['stock']; ?></td>
        <td>
          <a href="edit_product.php?id=<?php echo (int)$p['id']; ?>" class="btn-small btn-edit">Edit</a>
          <a href="delete_product.php?id=<?php echo (int)$p['id']; ?>" class="btn-small btn-delete"
             onclick="return confirm('Delete this product?');">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php endif; ?>
</table>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
