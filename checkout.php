<?php
session_start();

// Include database configuration
include 'db_config.php';
include 'products.php';

// Check if user is logged in
$user = $_SESSION['user'] ?? null;
if (!$user) {
    header('Location: login.php');
    exit;
}

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
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
$shipping = $subtotal > 75 ? 0 : 9.99;
$tax = $subtotal * 0.08;
$total = $subtotal + $shipping + $tax;

// Handle form submission
$errors = [];
$orderData = [
    'shipping_name' => $user['fullname'],
    'shipping_email' => $user['email'],
    'shipping_phone' => $user['phone'],
    'shipping_address' => $user['street'],
    'shipping_city' => $user['city'],
    'shipping_province' => $user['province'],
    'shipping_zip' => $user['zip'],
    'shipping_country' => $user['country'],
    'payment_method' => '',
    'card_number' => '',
    'card_expiry' => '',
    'card_cvv' => '',
    'card_name' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    foreach ($orderData as $key => $default) {
        $orderData[$key] = trim($_POST[$key] ?? $default);
    }
    
    // Validate required fields
    $requiredFields = [
        'shipping_name' => 'Full name is required',
        'shipping_email' => 'Email is required',
        'shipping_phone' => 'Phone number is required',
        'shipping_address' => 'Address is required',
        'shipping_city' => 'City is required',
        'shipping_province' => 'Province/State is required',
        'shipping_zip' => 'ZIP code is required',
        'shipping_country' => 'Country is required',
        'payment_method' => 'Payment method is required'
    ];
    
    foreach ($requiredFields as $field => $message) {
        if (empty($orderData[$field])) {
            $errors[$field] = $message;
        }
    }
    
    // Validate email
    if (!empty($orderData['shipping_email']) && !filter_var($orderData['shipping_email'], FILTER_VALIDATE_EMAIL)) {
        $errors['shipping_email'] = 'Please enter a valid email address';
    }
    
    // Validate payment method specific fields
    if ($orderData['payment_method'] === 'credit_card') {
        if (empty($orderData['card_number'])) {
            $errors['card_number'] = 'Card number is required';
        } elseif (!preg_match('/^\d{16}$/', str_replace(' ', '', $orderData['card_number']))) {
            $errors['card_number'] = 'Please enter a valid 16-digit card number';
        }
        
        if (empty($orderData['card_expiry'])) {
            $errors['card_expiry'] = 'Expiry date is required';
        } elseif (!preg_match('/^\d{2}\/\d{2}$/', $orderData['card_expiry'])) {
            $errors['card_expiry'] = 'Please enter expiry in MM/YY format';
        }
        
        if (empty($orderData['card_cvv'])) {
            $errors['card_cvv'] = 'CVV is required';
        } elseif (!preg_match('/^\d{3,4}$/', $orderData['card_cvv'])) {
            $errors['card_cvv'] = 'Please enter a valid 3 or 4 digit CVV';
        }
        
        if (empty($orderData['card_name'])) {
            $errors['card_name'] = 'Cardholder name is required';
        }
    }
    
    // If no errors, process the order
    if (empty($errors)) {
        // Generate order ID
        $orderId = 'GC' . date('Ymd') . rand(1000, 9999);
        
        // Prepare order data for database storage
        $items_json = json_encode($cartItems);
        
        // Insert order into database
        $stmt = $conn->prepare("INSERT INTO orders (order_id, user_id, shipping_name, shipping_email, shipping_phone, shipping_address, shipping_city, shipping_province, shipping_zip, shipping_country, payment_method, card_number, card_expiry, card_cvv, card_name, items, subtotal, shipping_cost, tax, total, status, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
        
        $stmt->bind_param("ssssssssssssssssdddd", 
            $orderId,
            $user['username'],
            $orderData['shipping_name'],
            $orderData['shipping_email'],
            $orderData['shipping_phone'],
            $orderData['shipping_address'],
            $orderData['shipping_city'],
            $orderData['shipping_province'],
            $orderData['shipping_zip'],
            $orderData['shipping_country'],
            $orderData['payment_method'],
            $orderData['card_number'],
            $orderData['card_expiry'],
            $orderData['card_cvv'],
            $orderData['card_name'],
            $items_json,
            $subtotal,
            $shipping,
            $tax,
            $total
        );
        
        if ($stmt->execute()) {
            // Store order info for confirmation page
            $_SESSION['last_order'] = [
                'order_id' => $orderId,
                'user_id' => $user['username'],
                'customer_info' => $orderData,
                'items' => $cartItems,
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'tax' => $tax,
                'total' => $total,
                'order_date' => date('Y-m-d H:i:s'),
                'status' => 'pending'
            ];
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            $stmt->close();
            header('Location: order-confirmation.php');
            exit;
        } else {
            $errors['general'] = 'Order processing failed. Please try again.';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - GlowCare Skincare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
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
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i>Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="shop.php"><i class="fas fa-shopping-bag me-1"></i>Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart me-1"></i>Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="welcome.php"><i class="fas fa-user me-1"></i>My Account</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
                    <li class="nav-item">
                        <span class="navbar-text">
                            <i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($user['fullname']) ?>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Checkout Content -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-4">
                    <h2 class="text-center">
                        <i class="fas fa-credit-card me-2" style="color: var(--primary-color);"></i>Secure Checkout
                    </h2>
                    <p class="text-center text-muted">Complete your order securely</p>
                </div>
            </div>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="row">
                    <!-- Left Column - Forms -->
                    <div class="col-lg-8">
                        <!-- Shipping Information -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Shipping Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="shipping_name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control <?= isset($errors['shipping_name']) ? 'is-invalid' : '' ?>" 
                                               id="shipping_name" name="shipping_name" value="<?= htmlspecialchars($orderData['shipping_name']) ?>" required>
                                        <div class="invalid-feedback"><?= $errors['shipping_name'] ?? '' ?></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="shipping_email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control <?= isset($errors['shipping_email']) ? 'is-invalid' : '' ?>" 
                                               id="shipping_email" name="shipping_email" value="<?= htmlspecialchars($orderData['shipping_email']) ?>" required>
                                        <div class="invalid-feedback"><?= $errors['shipping_email'] ?? '' ?></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="shipping_phone" class="form-label">Phone Number *</label>
                                        <input type="tel" class="form-control <?= isset($errors['shipping_phone']) ? 'is-invalid' : '' ?>" 
                                               id="shipping_phone" name="shipping_phone" value="<?= htmlspecialchars($orderData['shipping_phone']) ?>" required>
                                        <div class="invalid-feedback"><?= $errors['shipping_phone'] ?? '' ?></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="shipping_address" class="form-label">Street Address *</label>
                                        <input type="text" class="form-control <?= isset($errors['shipping_address']) ? 'is-invalid' : '' ?>" 
                                               id="shipping_address" name="shipping_address" value="<?= htmlspecialchars($orderData['shipping_address']) ?>" required>
                                        <div class="invalid-feedback"><?= $errors['shipping_address'] ?? '' ?></div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="shipping_city" class="form-label">City *</label>
                                        <input type="text" class="form-control <?= isset($errors['shipping_city']) ? 'is-invalid' : '' ?>" 
                                               id="shipping_city" name="shipping_city" value="<?= htmlspecialchars($orderData['shipping_city']) ?>" required>
                                        <div class="invalid-feedback"><?= $errors['shipping_city'] ?? '' ?></div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="shipping_province" class="form-label">Province/State *</label>
                                        <input type="text" class="form-control <?= isset($errors['shipping_province']) ? 'is-invalid' : '' ?>" 
                                               id="shipping_province" name="shipping_province" value="<?= htmlspecialchars($orderData['shipping_province']) ?>" required>
                                        <div class="invalid-feedback"><?= $errors['shipping_province'] ?? '' ?></div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="shipping_zip" class="form-label">ZIP Code *</label>
                                        <input type="text" class="form-control <?= isset($errors['shipping_zip']) ? 'is-invalid' : '' ?>" 
                                               id="shipping_zip" name="shipping_zip" value="<?= htmlspecialchars($orderData['shipping_zip']) ?>" required>
                                        <div class="invalid-feedback"><?= $errors['shipping_zip'] ?? '' ?></div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="shipping_country" class="form-label">Country *</label>
                                        <input type="text" class="form-control <?= isset($errors['shipping_country']) ? 'is-invalid' : '' ?>" 
                                               id="shipping_country" name="shipping_country" value="<?= htmlspecialchars($orderData['shipping_country']) ?>" required>
                                        <div class="invalid-feedback"><?= $errors['shipping_country'] ?? '' ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Payment Method *</label>
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" 
                                                           <?= $orderData['payment_method'] === 'credit_card' ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="credit_card">
                                                        <i class="fas fa-credit-card me-2"></i>Credit Card
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal"
                                                           <?= $orderData['payment_method'] === 'paypal' ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="paypal">
                                                        <i class="fab fa-paypal me-2"></i>PayPal
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="payment_method" id="gcash" value="gcash"
                                                           <?= $orderData['payment_method'] === 'gcash' ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="gcash">
                                                        <i class="fas fa-mobile-alt me-2"></i>GCash
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if (isset($errors['payment_method'])): ?>
                                            <div class="text-danger small"><?= $errors['payment_method'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Credit Card Fields -->
                                <div id="credit-card-fields" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="card_number" class="form-label">Card Number</label>
                                            <input type="text" class="form-control <?= isset($errors['card_number']) ? 'is-invalid' : '' ?>" 
                                                   id="card_number" name="card_number" value="<?= htmlspecialchars($orderData['card_number']) ?>" 
                                                   placeholder="1234 5678 9012 3456">
                                            <div class="invalid-feedback"><?= $errors['card_number'] ?? '' ?></div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="card_expiry" class="form-label">Expiry</label>
                                            <input type="text" class="form-control <?= isset($errors['card_expiry']) ? 'is-invalid' : '' ?>" 
                                                   id="card_expiry" name="card_expiry" value="<?= htmlspecialchars($orderData['card_expiry']) ?>" 
                                                   placeholder="MM/YY">
                                            <div class="invalid-feedback"><?= $errors['card_expiry'] ?? '' ?></div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label for="card_cvv" class="form-label">CVV</label>
                                            <input type="text" class="form-control <?= isset($errors['card_cvv']) ? 'is-invalid' : '' ?>" 
                                                   id="card_cvv" name="card_cvv" value="<?= htmlspecialchars($orderData['card_cvv']) ?>" 
                                                   placeholder="123">
                                            <div class="invalid-feedback"><?= $errors['card_cvv'] ?? '' ?></div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="card_name" class="form-label">Cardholder Name</label>
                                            <input type="text" class="form-control <?= isset($errors['card_name']) ? 'is-invalid' : '' ?>" 
                                                   id="card_name" name="card_name" value="<?= htmlspecialchars($orderData['card_name']) ?>" 
                                                   placeholder="John Doe">
                                            <div class="invalid-feedback"><?= $errors['card_name'] ?? '' ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Order Summary -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <!-- Order Items -->
                                <?php foreach ($cartItems as $item): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?= htmlspecialchars($item['product']['name']) ?></h6>
                                            <small class="text-muted">Qty: <?= $item['quantity'] ?></small>
                                        </div>
                                        <div class="text-end">
                                            <strong>$<?= number_format($item['total'], 2) ?></strong>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <!-- Order Totals -->
                                <div class="border-top pt-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <span>$<?= number_format($subtotal, 2) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Shipping:</span>
                                        <span><?= $shipping > 0 ? '$' . number_format($shipping, 2) : 'FREE' ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Tax:</span>
                                        <span>$<?= number_format($tax, 2) ?></span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold fs-5">
                                        <span>Total:</span>
                                        <span class="text-primary">$<?= number_format($total, 2) ?></span>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-lock me-2"></i>Place Order
                                    </button>
                                    <a href="cart.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Cart
                                    </a>
                                </div>

                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Your payment information is secure and encrypted
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show/hide credit card fields based on payment method
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const creditCardFields = document.getElementById('credit-card-fields');
                if (this.value === 'credit_card') {
                    creditCardFields.style.display = 'block';
                } else {
                    creditCardFields.style.display = 'none';
                }
            });
        });

        // Set initial state
        document.addEventListener('DOMContentLoaded', function() {
            const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
            if (selectedPayment && selectedPayment.value === 'credit_card') {
                document.getElementById('credit-card-fields').style.display = 'block';
            }
        });
    </script>
    <script src="main.js"></script>
</body>
</html>