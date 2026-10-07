<?php
require_once __DIR__ . '/../classes/Category.php';

$pageTitle = 'Categories';
require_once __DIR__ . '/includes/admin_header.php';

$categoryModel = new Category();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    if ($name === '') {
        $errors[] = 'Category name is required.';
    }

    if (empty($errors)) {
        $imageName = handleImageUpload('image', 'categories');
        $categoryModel->add($name, $imageName);
        header('Location: categories.php?saved=1');
        exit;
    }
}

$categories = $categoryModel->getAll();
?>

<div class="admin-topbar">
  <h1>Categories</h1>
</div>

<?php if (isset($_GET['saved'])): ?>
  <div class="alert alert-success">Category saved successfully.</div>
<?php endif; ?>
<?php if (isset($_GET['deleted'])): ?>
  <div class="alert alert-success">Category deleted.</div>
<?php endif; ?>

<div style="display:grid; grid-template-columns: 1fr 320px; gap:20px; align-items:start;">
  <table class="admin-table">
    <tr>
      <th>Image</th>
      <th>Name</th>
      <th>Products</th>
      <th>Actions</th>
    </tr>
    <?php if (empty($categories)): ?>
      <tr><td colspan="4">No categories yet.</td></tr>
    <?php else: ?>
      <?php foreach ($categories as $cat): ?>
        <tr>
          <td><img src="<?php echo '../' . categoryImage($cat['image']); ?>" alt=""></td>
          <td><?php echo h($cat['name']); ?></td>
          <td><?php echo $categoryModel->countProducts($cat['id']); ?></td>
          <td>
            <a href="delete_category.php?id=<?php echo (int)$cat['id']; ?>" class="btn-small btn-delete"
               onclick="return confirm('Delete this category? Products inside it will become uncategorized.');">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </table>

  <div class="form-box" style="margin:0;">
    <h2 style="font-size:18px;">Add Category</h2>
    <?php foreach ($errors as $error): ?>
      <div class="alert alert-error"><?php echo h($error); ?></div>
    <?php endforeach; ?>
    <form method="POST" action="categories.php" enctype="multipart/form-data">
      <div class="form-group">
        <label for="name">Category Name</label>
        <input type="text" id="name" name="name" required>
      </div>
      <div class="form-group">
        <label for="image">Category Icon/Image</label>
        <input type="file" id="image" name="image" accept="image/*">
      </div>
      <button type="submit" class="btn-primary">Add Category</button>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
