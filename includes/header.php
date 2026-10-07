<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Cart.php';

$categoryModel = new Category();
$allCategories = $categoryModel->getAll();

$cart = new Cart();
$cartCount = $cart->getCount();

// Highlight the active category chip when browsing category.php?id=
$activeCategoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? h($pageTitle) . ' - FreshCart' : 'FreshCart - Grocery Delivery'; ?></title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="topbar">
  <div class="topbar-inner">
    <a href="index.php" class="logo">Fresh<span>Cart</span></a>

    <form class="search-form" action="search.php" method="GET">
      <input type="text" name="q" placeholder="Search for products, e.g. milk, bread, chips..."
             value="<?php echo isset($_GET['q']) ? h($_GET['q']) : ''; ?>" required>
      <button type="submit">Search</button>
    </form>

    <a href="cart.php" class="cart-link">
      🛒 Cart
      <span class="cart-badge" id="cartBadge"><?php echo (int)$cartCount; ?></span>
    </a>
  </div>

  <div class="category-strip">
    <div class="container">
      <a href="index.php" class="category-chip <?php echo $activeCategoryId === 0 ? 'active' : ''; ?>">
        <img src="assets/img/placeholder.svg" alt="All">
        All
      </a>
      <?php foreach ($allCategories as $cat): ?>
        <a href="category.php?id=<?php echo (int)$cat['id']; ?>"
           class="category-chip <?php echo $activeCategoryId === (int)$cat['id'] ? 'active' : ''; ?>">
          <img src="<?php echo categoryImage($cat['image']); ?>" alt="<?php echo h($cat['name']); ?>">
          <?php echo h($cat['name']); ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<div class="container">
<div id="toast" class="toast"></div>
