<?php
require_once __DIR__ . '/classes/Product.php';
require_once __DIR__ . '/classes/Category.php';

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$categoryFilter = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? (int)$_GET['category_id'] : null;

$pageTitle = 'Search results for "' . $keyword . '"';
require_once __DIR__ . '/includes/header.php';

$productModel = new Product();
$categoryModel = new Category();
$categories = $categoryModel->getAll();

$products = $keyword !== '' ? $productModel->search($keyword, $categoryFilter) : [];
?>

<div class="page-heading">
  <h1>Search results for "<?php echo h($keyword); ?>"</h1>
  <span class="result-count"><?php echo count($products); ?> product(s)</span>
</div>

<form method="GET" action="search.php" style="margin-bottom:16px; display:flex; gap:10px; align-items:center;">
  <input type="hidden" name="q" value="<?php echo h($keyword); ?>">
  <label for="category_id" style="font-size:14px; font-weight:600;">Filter by category:</label>
  <select name="category_id" id="category_id" onchange="this.form.submit()" style="padding:8px 10px; border-radius:8px; border:1px solid #e5e7eb;">
    <option value="">All Categories</option>
    <?php foreach ($categories as $cat): ?>
      <option value="<?php echo (int)$cat['id']; ?>" <?php echo $categoryFilter === (int)$cat['id'] ? 'selected' : ''; ?>>
        <?php echo h($cat['name']); ?>
      </option>
    <?php endforeach; ?>
  </select>
</form>

<?php if (empty($products)): ?>
  <p class="empty-msg">No products matched your search. Try a different keyword.</p>
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
