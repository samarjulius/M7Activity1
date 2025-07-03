<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'products.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle AJAX requests for cart operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    $productId = (int)($_POST['product_id'] ?? 0);
    
    switch ($action) {
        case 'add':
            $quantity = (int)($_POST['quantity'] ?? 1);
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] += $quantity;
            } else {
                $_SESSION['cart'][$productId] = $quantity;
            }
            // Limit quantity to 10
            if ($_SESSION['cart'][$productId] > 10) {
                $_SESSION['cart'][$productId] = 10;
            }
            echo json_encode(['success' => true, 'message' => 'Product added to cart']);
            exit;
            
        case 'update':
            $quantity = max(0, (int)($_POST['quantity'] ?? 0));
            if ($quantity > 0) {
                $_SESSION['cart'][$productId] = min($quantity, 10);
            } else {
                unset($_SESSION['cart'][$productId]);
            }
            echo json_encode(['success' => true]);
            exit;
            
        case 'remove':
            unset($_SESSION['cart'][$productId]);
            echo json_encode(['success' => true, 'message' => 'Product removed from cart']);
            exit;
            
        case 'clear':
            $_SESSION['cart'] = [];
            echo json_encode(['success' => true, 'message' => 'Cart cleared']);
            exit;
    }
}

// Get cart items with product details
$cartItems = [];
$subtotal = 0;

foreach ($_SESSION['cart'] as $productId => $quantity) {
    $product = getProductById($productId);
    if ($product) {
        $itemTotal = $product['price'] * $quantity;
        $cartItems[] = [
            'product' => $product,
            'quantity' => $quantity,
            'total' => $itemTotal
        ];
        $subtotal += $itemTotal;
    }
}

// Calculate totals
$shipping = $subtotal > 0 ? ($subtotal > 75 ? 0 : 9.99) : 0;
$tax = $subtotal * 0.08; // 8% tax rate
$total = $subtotal + $shipping + $tax;

// Check if user is logged in
$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - GlowCare Skincare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .quantity-btn {
            width: 35px;
            height: 35px;
            border: 2px solid var(--primary-color);
            background: white;
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
            font-size: 16px;
        }
        
        .quantity-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }
        
        .quantity-btn:active {
            transform: scale(0.95);
        }
        
        .quantity-display {
            min-width: 40px;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            color: var(--primary-color);
        }
        
        .clear-cart-btn {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }
        
        .clear-cart-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
            background: linear-gradient(135deg, #c82333, #bd2130);
        }
        
        .item-total {
            font-weight: bold;
            color: var(--primary-color);
            font-size: 1.1em;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-spa me-2"></i>GlowCare
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="shop.php">
                            <i class="fas fa-shopping-bag me-1"></i>Shop
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">
                            <i class="fas fa-info-circle me-2"></i>About Us
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="cart.php">
                            <i class="fas fa-shopping-cart me-1"></i>Cart
                            <span class="badge bg-primary cart-badge ms-1" id="cart-count"><?= array_sum($_SESSION['cart']) ?></span>
                        </a>
                    </li>
                    <?php if ($user): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="welcome.php">
                                <i class="fas fa-user me-1"></i>My Account
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </a>
                        </li>
                        <li class="nav-item">
                            <span class="navbar-text">
                                <i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($user['fullname']) ?>
                            </span>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Cart Header -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-8 mx-auto">
                    <h1 class="display-4 fw-bold mb-3" style="color: var(--primary-color);">
                        <i class="fas fa-shopping-cart me-3"></i>Shopping Cart
                    </h1>
                    <p class="lead text-muted">
                        Review your selected items and proceed to checkout when ready.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Cart Content -->
    <section class="py-5">
        <div class="container">
            <?php if (empty($cartItems)): ?>
                <!-- Empty Cart -->
                <div class="row justify-content-center">
                    <div class="col-lg-6 text-center">
                        <div class="card border-0 shadow">
                            <div class="card-body py-5">
                                <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
                                <h3 class="fw-bold mb-3">Your cart is empty</h3>
                                <p class="text-muted mb-4">
                                    Looks like you haven't added any items to your cart yet. Start shopping to fill it up!
                                </p>
                                <a href="shop.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <!-- Cart Items -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow mb-4">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">
                                    <i class="fas fa-list me-2"></i>Cart Items (<?= count($cartItems) ?>)
                                </h4>
                                <button class="clear-cart-btn" onclick="clearCart()">
                                    <i class="fas fa-trash me-2"></i>Clear Cart
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <?php foreach ($cartItems as $item): ?>
                                    <div class="cart-item border-bottom p-4" data-product-id="<?= $item['product']['id'] ?>">
                                        <div class="row align-items-center">
                                            <div class="col-md-3">
                                                <img src="<?= htmlspecialchars($item['product']['image']) ?>" 
                                                     class="img-fluid rounded" 
                                                     alt="<?= htmlspecialchars($item['product']['name']) ?>"
                                                     style="height: 80px; width: 80px; object-fit: cover;">
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="fw-bold mb-1"><?= htmlspecialchars($item['product']['name']) ?></h6>
                                                <p class="text-muted small mb-0"><?= htmlspecialchars($item['product']['short_description']) ?></p>
                                                <span class="text-primary fw-bold">$<?= number_format($item['product']['price'], 2) ?></span>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="quantity-controls">
                                                    <button class="quantity-btn" onclick="updateQuantity(<?= $item['product']['id'] ?>, -1)">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <span class="quantity-display" id="quantity-<?= $item['product']['id'] ?>"><?= $item['quantity'] ?></span>
                                                    <button class="quantity-btn" onclick="updateQuantity(<?= $item['product']['id'] ?>, 1)">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-2 text-end">
                                                <div class="item-total" id="total-<?= $item['product']['id'] ?>">
                                                    $<?= number_format($item['total'], 2) ?>
                                                </div>
                                                <button class="btn btn-outline-danger btn-sm mt-2" onclick="removeFromCart(<?= $item['product']['id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-calculator me-2"></i>Order Summary
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="subtotal">$<?= number_format($subtotal, 2) ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <span id="shipping">
                                        <?php if ($shipping == 0): ?>
                                            <span class="text-success">FREE</span>
                                        <?php else: ?>
                                            $<?= number_format($shipping, 2) ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax:</span>
                                    <span id="tax">$<?= number_format($tax, 2) ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold fs-5 text-primary">
                                    <span>Total:</span>
                                    <span id="total">$<?= number_format($total, 2) ?></span>
                                </div>
                                
                                <?php if ($subtotal > 0 && $subtotal < 75): ?>
                                    <div class="alert alert-info mt-3">
                                        <small>
                                            <i class="fas fa-info-circle me-1"></i>
                                            Add $<?= number_format(75 - $subtotal, 2) ?> more for free shipping!
                                        </small>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-grid gap-2 mt-4">
                                    <?php if ($user): ?>
                                        <a href="checkout.php" class="btn btn-primary btn-lg">
                                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                                        </a>
                                    <?php else: ?>
                                        <a href="login.php" class="btn btn-primary btn-lg">
                                            <i class="fas fa-sign-in-alt me-2"></i>Login to Checkout
                                        </a>
                                    <?php endif; ?>
                                    <a href="shop.php" class="btn btn-outline-primary">
                                        <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="main.js"></script>
</body>
</html>