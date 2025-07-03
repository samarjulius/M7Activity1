<?php
// get_product_details.php
header('Content-Type: application/json');

include 'products.php';

$productId = (int)($_GET['id'] ?? 0);

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

$product = getProductById($productId);

if ($product) {
    echo json_encode(['success' => true, 'product' => $product]);
} else {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
}
?>