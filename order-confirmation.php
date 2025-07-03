<?php
session_start();

// Check if there's a recent order
$order = $_SESSION['last_order'] ?? null;
if (!$order) {
    header('Location: shop.php');
    exit;
}

// Check if user is logged in
$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - GlowCare Skincare</title>
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
                        <a class="nav-link" href="about.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart me-1"></i>Cart
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
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Order Confirmation Content -->
    <section class="py-5">
        <div class="container">
            <!-- Success Message -->
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <div class="success-icon mb-4">
                        <i class="fas fa-check-circle fa-5x text-success"></i>
                    </div>
                    <h1 class="display-4 fw-bold mb-3" style="color: var(--primary-color);">
                        Order Confirmed!
                    </h1>
                    <p class="lead text-muted mb-4">
                        Thank you for your purchase! Your order has been successfully placed and will be processed shortly.
                    </p>
                    <div class="alert alert-success border-0 shadow">
                        <h5 class="alert-heading">
                            <i class="fas fa-receipt me-2"></i>Order ID: <?= htmlspecialchars($order['order_id']) ?>
                        </h5>
                        <p class="mb-0">Please keep this order ID for your records. You'll receive an email confirmation shortly.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Order Details -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-box me-2"></i>Order Details
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Order Date</h6>
                                    <p><?= date('F j, Y g:i A', strtotime($order['order_date'])) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Order Status</h6>
                                    <span class="badge bg-warning fs-6">Processing</span>
                                </div>
                            </div>

                            <!-- Ordered Items -->
                            <h6 class="mb-3">Items Ordered (<?= count($order['items']) ?>)</h6>
                            <?php foreach ($order['items'] as $item): ?>
                            <div class="d-flex align-items-center border-bottom py-3">
                                <img src="<?= htmlspecialchars($item['product']['image']) ?>" 
                                     alt="<?= htmlspecialchars($item['product']['name']) ?>" 
                                     class="img-fluid rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?= htmlspecialchars($item['product']['name']) ?></h6>
                                    <p class="text-muted mb-1 small"><?= htmlspecialchars($item['product']['short_description']) ?></p>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($item['product']['category']) ?></span>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted small">Qty: <?= $item['quantity'] ?></div>
                                    <div class="fw-bold">$<?= number_format($item['total'], 2) ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    <div class="card border-0 shadow">
                        <div class="card-header bg-info text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-truck me-2"></i>Shipping Information
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted">Shipping Address</h6>
                                    <address>
                                        <strong><?= htmlspecialchars($order['customer_info']['shipping_name']) ?></strong><br>
                                        <?= htmlspecialchars($order['customer_info']['shipping_address']) ?><br>
                                        <?= htmlspecialchars($order['customer_info']['shipping_city']) ?>, 
                                        <?= htmlspecialchars($order['customer_info']['shipping_province']) ?> 
                                        <?= htmlspecialchars($order['customer_info']['shipping_zip']) ?><br>
                                        <?= htmlspecialchars($order['customer_info']['shipping_country']) ?>
                                    </address>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">Contact Information</h6>
                                    <p>
                                        <i class="fas fa-envelope me-2"></i><?= htmlspecialchars($order['customer_info']['shipping_email']) ?><br>
                                        <i class="fas fa-phone me-2"></i><?= htmlspecialchars($order['customer_info']['shipping_phone']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Estimated Delivery:</strong> 3-5 business days
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow sticky-top" style="top: 20px;">
                        <div class="card-header bg-success text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-calculator me-2"></i>Order Summary
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>$<?= number_format($order['subtotal'], 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span>
                                    <?php if ($order['shipping'] == 0): ?>
                                        <span class="text-success">FREE</span>
                                    <?php else: ?>
                                        $<?= number_format($order['shipping'], 2) ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax (8%):</span>
                                <span>$<?= number_format($order['tax'], 2) ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total Paid:</strong>
                                <strong class="text-success h5">$<?= number_format($order['total'], 2) ?></strong>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-muted">Payment Method</h6>
                                <p class="mb-0">
                                    <?php if ($order['customer_info']['payment_method'] === 'credit_card'): ?>
                                        <i class="fas fa-credit-card me-2"></i>Credit Card
                                    <?php else: ?>
                                        <i class="fab fa-paypal me-2"></i>PayPal
                                    <?php endif; ?>
                                </p>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="shop.php" class="btn btn-primary">
                                    <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                                </a>
                                <a href="welcome.php" class="btn btn-outline-primary">
                                    <i class="fas fa-user me-2"></i>My Account
                                </a>
                            </div>

                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-envelope me-1"></i>Order confirmation sent to your email
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- What's Next -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card border-0 shadow">
                        <div class="card-header bg-dark text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-clock me-2"></i>What Happens Next?
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center mb-3">
                                    <i class="fas fa-cog fa-3x text-primary mb-3"></i>
                                    <h6>Order Processing</h6>
                                    <p class="text-muted small">We'll process your order and prepare it for shipment within 24 hours.</p>
                                </div>
                                <div class="col-md-4 text-center mb-3">
                                    <i class="fas fa-shipping-fast fa-3x text-warning mb-3"></i>
                                    <h6>Shipping</h6>
                                    <p class="text-muted small">Your order will be shipped within 1-2 business days with tracking information.</p>
                                </div>
                                <div class="col-md-4 text-center mb-3">
                                    <i class="fas fa-home fa-3x text-success mb-3"></i>
                                    <h6>Delivery</h6>
                                    <p class="text-muted small">Expect delivery within 3-5 business days. Enjoy your new skincare products!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5><i class="fas fa-spa me-2"></i>GlowCare</h5>
                    <p class="text-light">Premium skincare products for your natural glow.</p>
                    <div class="mt-3">
                        <small class="text-light">
                            <i class="fas fa-code me-2"></i>
                            Developed by Julius A. Samar & Gilliane Gail Villon<br>
                            <i class="fas fa-university me-2"></i>
                            University of the East - IT Students
                        </small>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-light text-decoration-none">Home</a></li>
                        <li><a href="shop.php" class="text-light text-decoration-none">Shop</a></li>
                        <li><a href="about.php" class="text-light text-decoration-none">About Us</a></li>
                        <li><a href="contact.php" class="text-light text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6>Contact Info</h6>
                    <p class="text-light small">
                        <i class="fas fa-envelope me-2"></i>support@glowcare.com<br>
                        <i class="fas fa-phone me-2"></i>+1 (555) 123-4567<br>
                        <i class="fas fa-map-marker-alt me-2"></i>Manila, Philippines
                    </p>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="text-light mb-2">
                    &copy; 2024 GlowCare Skincare. All rights reserved.
                </p>
                <p class="text-light small">
                    <i class="fas fa-heart text-danger me-1"></i>
                    Made with love by Julius A. Samar & Gilliane Gail D. Villon
                </p>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
