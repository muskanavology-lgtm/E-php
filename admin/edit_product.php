<?php
require_once __DIR__ . '/../classes/Product.php';
require_once __DIR__ . '/../classes/Category.php';

$pageTitle = 'Edit Product';
require_once __DIR__ . '/includes/admin_header.php';

$productModel = new Product();
$categoryModel = new Category();
$categories = $categoryModel->getAll();

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $productModel->getById($productId);

if (!$product) {
    header('Location: products.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);

    if ($name === '') $errors[] = 'Product name is required.';
    if ($price <= 0) $errors[] = 'Price must be greater than 0.';
    if ($categoryId <= 0) $errors[] = 'Please choose a category.';

    if (empty($errors)) {
        $imageName = handleImageUpload('image', 'products'); // null if no new file uploaded

        $productModel->update($productId, $name, $description, $price, $categoryId, $stock, $imageName);

        header('Location: products.php?saved=1');
        exit;
    }
}
?>

<div class="admin-topbar">
  <h1>Edit Product</h1>
  <a href="products.php" class="btn-small btn-edit">← Back to Products</a>
</div>

<div class="form-box" style="margin:0;">
  <?php foreach ($errors as $error): ?>
    <div class="alert alert-error"><?php echo h($error); ?></div>
  <?php endforeach; ?>

  <form method="POST" action="edit_product.php?id=<?php echo (int)$product['id']; ?>" enctype="multipart/form-data">
    <div class="form-group">
      <label for="name">Product Name</label>
      <input type="text" id="name" name="name" value="<?php echo h($_POST['name'] ?? $product['name']); ?>" required>
    </div>
    <div class="form-group">
      <label for="description">Description</label>
      <textarea id="description" name="description"><?php echo h($_POST['description'] ?? $product['description']); ?></textarea>
    </div>
    <div class="form-group">
      <label for="price">Price (₹)</label>
      <input type="number" step="0.01" id="price" name="price" value="<?php echo h($_POST['price'] ?? $product['price']); ?>" required>
    </div>
    <div class="form-group">
      <label for="stock">Stock Quantity</label>
      <input type="number" id="stock" name="stock" value="<?php echo h($_POST['stock'] ?? $product['stock']); ?>" required>
    </div>
    <div class="form-group">
      <label for="category_id">Category</label>
      <select id="category_id" name="category_id" required>
        <option value="">-- Select Category --</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?php echo (int)$cat['id']; ?>" <?php echo (int)$product['category_id'] === (int)$cat['id'] ? 'selected' : ''; ?>>
            <?php echo h($cat['name']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Current Image</label><br>
      <img src="<?php echo '../' . productImage($product['image']); ?>" style="width:80px; height:80px; object-fit:contain; border:1px solid #e5e7eb; border-radius:8px; padding:4px;">
    </div>
    <div class="form-group">
      <label for="image">Replace Image (optional)</label>
      <input type="file" id="image" name="image" accept="image/*">
    </div>
    <button type="submit" class="btn-primary">Update Product</button>
  </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
