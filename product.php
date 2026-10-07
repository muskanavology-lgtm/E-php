<?php
require_once __DIR__ . '/classes/Product.php';

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$productModel = new Product();
$product = $productModel->getById($productId);

if (!$product) {
    header('Location: index.php');
    exit;
}

$pageTitle = $product['name'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="detail-wrap">
  <img src="<?php echo productImage($product['image']); ?>" alt="<?php echo h($product['name']); ?>">
  <div>
    <div class="cat-tag"><?php echo h($product['category_name'] ?? 'General'); ?></div>
    <h1><?php echo h($product['name']); ?></h1>
    <div class="detail-price"><?php echo formatPrice($product['price']); ?></div>
    <p class="detail-desc"><?php echo nl2br(h($product['description'])); ?></p>
    <p class="p-stock">
      <?php echo $product['stock'] > 0 ? $product['stock'] . ' in stock' : '<span style="color:#d64550">Out of stock</span>'; ?>
    </p>

    <?php if ($product['stock'] > 0): ?>
      <div style="display:flex; align-items:center; gap:14px; margin-top:10px;">
        <input type="number" id="qty-<?php echo (int)$product['id']; ?>" value="1" min="1" max="<?php echo (int)$product['stock']; ?>"
               style="width:70px; padding:8px; border:1px solid #e5e7eb; border-radius:8px;">
        <button class="btn-add" data-id="<?php echo (int)$product['id']; ?>" style="padding:10px 20px;">+ Add to cart</button>
      </div>
    <?php else: ?>
      <button class="btn-add" disabled style="padding:10px 20px;">Out of stock</button>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
