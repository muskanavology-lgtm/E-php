<?php
require_once __DIR__ . '/classes/Product.php';

$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';

$productModel = new Product();
$products = $productModel->getLatest(24);
?>

<div class="hero-banner">
  <h1>Groceries delivered in minutes</h1>
  <p>Fruits, snacks, dairy, drinks and more — fresh at your doorstep.</p>
</div>

<h2 class="section-title">Popular Products</h2>

<?php if (empty($products)): ?>
  <p class="empty-msg">No products added yet. Please check back soon!</p>
<?php else: ?>
  <div class="product-grid">
    <?php foreach ($products as $p): ?>
      <div class="product-card">
        <a href="product.php?id=<?php echo (int)$p['id']; ?>">
          <img src="<?php echo productImage($p['image']); ?>" alt="<?php echo h($p['name']); ?>">
        </a>
        <div class="cat-tag"><?php echo h($p['category_name'] ?? 'General'); ?></div>
        <a href="product.php?id=<?php echo (int)$p['id']; ?>" class="p-name"><?php echo h($p['name']); ?></a>
        <div class="p-price"><?php echo formatPrice($p['price']); ?></div>
        <div class="p-stock">
          <?php echo $p['stock'] > 0 ? $p['stock'] . ' in stock' : '<span style="color:#d64550">Out of stock</span>'; ?>
        </div>
        <button class="btn-add" data-id="<?php echo (int)$p['id']; ?>" <?php echo $p['stock'] <= 0 ? 'disabled' : ''; ?>>
          <?php echo $p['stock'] <= 0 ? 'Out of stock' : '+ Add to cart'; ?>
        </button>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
