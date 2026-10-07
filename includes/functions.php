<?php
// Format a number as Indian Rupees, e.g. 1234.5 -> ₹1,234.50
function formatPrice($amount)
{
    return '₹' . number_format((float)$amount, 2);
}

// Return the correct image path, or a placeholder if no image was uploaded
function productImage($image)
{
    if ($image && file_exists(__DIR__ . '/../uploads/products/' . $image)) {
        return 'uploads/products/' . $image;
    }
    return 'assets/img/placeholder.svg';
}

function categoryImage($image)
{
    if ($image && file_exists(__DIR__ . '/../uploads/categories/' . $image)) {
        return 'uploads/categories/' . $image;
    }
    return 'assets/img/placeholder.svg';
}

// Basic protection when printing user data back into HTML
function h($text)
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

// Handles a single image upload, returns the new filename or null
function handleImageUpload($fileInputName, $targetFolder)
{
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $tmpName = $_FILES[$fileInputName]['tmp_name'];
    $originalName = $_FILES[$fileInputName]['name'];
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return null;
    }

    $newName = uniqid('img_', true) . '.' . $ext;
    $destination = __DIR__ . '/../uploads/' . $targetFolder . '/' . $newName;

    if (move_uploaded_file($tmpName, $destination)) {
        return $newName;
    }
    return null;
}
