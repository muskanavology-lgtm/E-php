<?php
require_once __DIR__ . '/../classes/Admin.php';
require_once __DIR__ . '/../classes/Category.php';
Admin::requireLogin();

$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($categoryId > 0) {
    $categoryModel = new Category();
    $categoryModel->delete($categoryId);
}

header('Location: categories.php?deleted=1');
exit;
