<?php
session_start();
include 'products.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Set content type to JSON
header('Content-Type: application/json');

// Handle different cart actions
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        $product_id = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);
        
        if ($product_id > 0 && $quantity > 0) {
            // Check if product exists
            $product = getProductById($product_id);
            if ($product && $product['in_stock']) {
                // Add to cart or update quantity
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id] += $quantity;
                } else {
                    $_SESSION['cart'][$product_id] = $quantity;
                }
                
                // Limit quantity to 10 per product
                if ($_SESSION['cart'][$product_id] > 10) {
                    $_SESSION['cart'][$product_id] = 10;
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Product added to cart successfully',
                    'cart_count' => array_sum($_SESSION['cart'])
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Product not found or out of stock'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid product or quantity'
            ]);
        }
        break;

    case 'update':
        $product_id = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);
        
        if ($product_id > 0) {
            if ($quantity > 0) {
                // Limit quantity to 10
                $_SESSION['cart'][$product_id] = min($quantity, 10);
            } else {
                // Remove if quantity is 0 or negative
                unset($_SESSION['cart'][$product_id]);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Cart updated successfully',
                'cart_count' => array_sum($_SESSION['cart'])
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid product ID'
            ]);
        }
        break;

    case 'remove':
        $product_id = (int)($_POST['product_id'] ?? 0);
        
        if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            echo json_encode([
                'success' => true,
                'message' => 'Product removed from cart',
                'cart_count' => array_sum($_SESSION['cart'])
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Product not found in cart'
            ]);
        }
        break;

    case 'clear':
        $_SESSION['cart'] = [];
        echo json_encode([
            'success' => true,
            'message' => 'Cart cleared successfully',
            'cart_count' => 0
        ]);
        break;

    case 'count':
        echo json_encode([
            'success' => true,
            'count' => array_sum($_SESSION['cart'])
        ]);
        break;

    case 'get':
        $cart_items = [];
        $total = 0;
        
        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $product = getProductById($product_id);
            if ($product) {
                $item = $product;
                $item['quantity'] = $quantity;
                $item['subtotal'] = $product['price'] * $quantity;
                $cart_items[] = $item;
                $total += $item['subtotal'];
            }
        }
        
        echo json_encode([
            'success' => true,
            'items' => $cart_items,
            'total' => $total,
            'count' => array_sum($_SESSION['cart'])
        ]);
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
        break;
}
?>