<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
include 'db_config.php';

// Check if user is logged in
$user = $_SESSION['user'] ?? null;
if (!$user) {
    header('Location: login.php');
    exit;
}

include 'products.php';

// Function to get user orders from database
function getUserOrders($username) {
    global $conn;
    $orders = [];
    
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // Decode JSON items
        $row['items'] = json_decode($row['items'], true);
        $orders[] = $row;
    }
    
    $stmt->close();
    return $orders;
}

// Function to get order status badge
function getStatusBadge($status) {
    $badges = [
        'pending' => ['class' => 'bg-warning', 'icon' => 'fas fa-clock', 'text' => 'Processing'],
        'confirmed' => ['class' => 'bg-info', 'icon' => 'fas fa-check-circle', 'text' => 'Confirmed'],
        'shipped' => ['class' => 'bg-primary', 'icon' => 'fas fa-shipping-fast', 'text' => 'Shipped'],
        'delivered' => ['class' => 'bg-success', 'icon' => 'fas fa-box-open', 'text' => 'Delivered'],
        'cancelled' => ['class' => 'bg-danger', 'icon' => 'fas fa-times-circle', 'text' => 'Cancelled']
    ];
    
    return $badges[$status] ?? $badges['pending'];
}

// Get user orders
$userOrders = getUserOrders($user['username']);

// Handle status filter
$statusFilter = $_GET['status'] ?? 'all';
if ($statusFilter !== 'all') {
    $userOrders = array_filter($userOrders, function($order) use ($statusFilter) {
        return $order['status'] === $statusFilter;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - GlowCare Skincare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .order-card {
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            border-radius: 15px;
            overflow: hidden;
        }
        
        .order-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(212, 132, 122, 0.15);
        }
        
        .order-header {
            background: linear-gradient(135deg, var(--light-bg), white);
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem;
        }
        
        .order-id {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .status-filter-btn {
            border: 2px solid var(--border-color);
            background: white;
            color: var(--text-color);
            border-radius: 25px;
            padding: 8px 20px;
            margin: 5px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .status-filter-btn:hover,
        .status-filter-btn.active {
            border-color: var(--primary-color);
            background: var(--primary-color);
            color: white;
            text-decoration: none;
        }
        
        .order-item {
            border-bottom: 1px solid var(--border-color);
            padding: 1rem;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .order-timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .order-timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--border-color);
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 1rem;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 6px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--primary-color);
            border: 3px solid white;
            box-shadow: 0 0 0 2px var(--primary-color);
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
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i>Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="shop.php"><i class="fas fa-shopping-bag me-1"></i>Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php"><i class="fas fa-info-circle me-2"></i>About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart me-1"></i>Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="welcome.php"><i class="fas fa-user me-1"></i>My Account</a></li>
                    <li class="nav-item"><a class="nav-link active" href="orders.php"><i class="fas fa-box me-1"></i>Orders</a></li>
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

    <!-- Orders Page Content -->
    <section class="py-5">
        <div class="container">
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="text-center mb-4">
                        <i class="fas fa-box me-2" style="color: var(--primary-color);"></i>My Orders
                    </h2>
                    
                    <!-- Status Filter -->
                    <div class="text-center mb-4">
                        <a href="orders.php?status=all" class="status-filter-btn <?= $statusFilter === 'all' ? 'active' : '' ?>">
                            <i class="fas fa-list me-1"></i>All Orders
                        </a>
                        <a href="orders.php?status=pending" class="status-filter-btn <?= $statusFilter === 'pending' ? 'active' : '' ?>">
                            <i class="fas fa-clock me-1"></i>Processing
                        </a>
                        <a href="orders.php?status=confirmed" class="status-filter-btn <?= $statusFilter === 'confirmed' ? 'active' : '' ?>">
                            <i class="fas fa-check-circle me-1"></i>Confirmed
                        </a>
                        <a href="orders.php?status=shipped" class="status-filter-btn <?= $statusFilter === 'shipped' ? 'active' : '' ?>">
                            <i class="fas fa-shipping-fast me-1"></i>Shipped
                        </a>
                        <a href="orders.php?status=delivered" class="status-filter-btn <?= $statusFilter === 'delivered' ? 'active' : '' ?>">
                            <i class="fas fa-box-open me-1"></i>Delivered
                        </a>
                    </div>
                </div>
            </div>

            <!-- Orders List -->
            <?php if (empty($userOrders)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-box fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No orders found</h4>
                    <p class="text-muted">
                        <?php if ($statusFilter === 'all'): ?>
                            You haven't placed any orders yet.
                        <?php else: ?>
                            You don't have any <?= htmlspecialchars($statusFilter) ?> orders.
                        <?php endif; ?>
                    </p>
                    <a href="shop.php" class="btn btn-primary">
                        <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($userOrders as $order): ?>
                        <div class="col-12 mb-4">
                            <div class="order-card">
                                <div class="order-header">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <div class="order-id"><?= htmlspecialchars($order['order_id']) ?></div>
                                            <small class="text-muted">Placed on <?= date('M j, Y', strtotime($order['order_date'])) ?></small>
                                        </div>
                                        <div class="col-md-3">
                                            <?php $badge = getStatusBadge($order['status']); ?>
                                            <span class="badge <?= $badge['class'] ?> fs-6">
                                                <i class="<?= $badge['icon'] ?> me-1"></i><?= $badge['text'] ?>
                                            </span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong class="fs-5">$<?= number_format($order['total'], 2) ?></strong>
                                            <br><small class="text-muted"><?= count($order['items']) ?> item(s)</small>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <button class="btn btn-outline-primary btn-sm" onclick="toggleOrderDetails('<?= $order['order_id'] ?>')">
                                                <i class="fas fa-chevron-down me-1"></i>View Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Order Details (Initially Hidden) -->
                                <div id="details-<?= $order['order_id'] ?>" class="order-details" style="display: none;">
                                    <div class="row">
                                        <!-- Order Items -->
                                        <div class="col-md-8">
                                            <div class="p-3">
                                                <h6 class="text-primary border-bottom pb-2 mb-3">Order Items</h6>
                                                <?php foreach ($order['items'] as $item): ?>
                                                    <div class="order-item">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-2">
                                                                <?php if (isset($item['product']['image'])): ?>
                                                                    <img src="<?= htmlspecialchars($item['product']['image']) ?>" 
                                                                         alt="<?= htmlspecialchars($item['product']['name']) ?>" 
                                                                         class="img-fluid rounded">
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6><?= htmlspecialchars($item['product']['name']) ?></h6>
                                                                <p class="text-muted small mb-0"><?= htmlspecialchars($item['product']['short_description'] ?? '') ?></p>
                                                            </div>
                                                            <div class="col-md-2 text-center">
                                                                <span class="badge bg-light text-dark">Qty: <?= $item['quantity'] ?></span>
                                                            </div>
                                                            <div class="col-md-2 text-end">
                                                                <strong>$<?= number_format($item['total'], 2) ?></strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Order Summary & Shipping -->
                                        <div class="col-md-4">
                                            <div class="p-3 bg-light">
                                                <h6 class="text-primary border-bottom pb-2 mb-3">Order Summary</h6>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Subtotal:</span>
                                                    <span>$<?= number_format($order['subtotal'], 2) ?></span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Shipping:</span>
                                                    <span>$<?= number_format($order['shipping_cost'], 2) ?></span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Tax:</span>
                                                    <span>$<?= number_format($order['tax'], 2) ?></span>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between fw-bold">
                                                    <span>Total:</span>
                                                    <span>$<?= number_format($order['total'], 2) ?></span>
                                                </div>
                                                
                                                <hr>
                                                <h6 class="text-primary mb-2">Shipping Address</h6>
                                                <address>
                                                    <strong><?= htmlspecialchars($order['shipping_name']) ?></strong><br>
                                                    <?= htmlspecialchars($order['shipping_address']) ?><br>
                                                    <?= htmlspecialchars($order['shipping_city']) ?>, <?= htmlspecialchars($order['shipping_province']) ?> <?= htmlspecialchars($order['shipping_zip']) ?><br>
                                                    <?= htmlspecialchars($order['shipping_country']) ?><br>
                                                    <abbr title="Phone">P:</abbr> <?= htmlspecialchars($order['shipping_phone']) ?>
                                                </address>
                                                
                                                <h6 class="text-primary mb-2">Payment Method</h6>
                                                <p><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $order['payment_method']))) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleOrderDetails(orderId) {
            const detailsDiv = document.getElementById('details-' + orderId);
            const button = detailsDiv.previousElementSibling.querySelector('button');
            const icon = button.querySelector('i');
            
            if (detailsDiv.style.display === 'none') {
                detailsDiv.style.display = 'block';
                icon.className = 'fas fa-chevron-up me-1';
                button.innerHTML = '<i class="fas fa-chevron-up me-1"></i>Hide Details';
            } else {
                detailsDiv.style.display = 'none';
                icon.className = 'fas fa-chevron-down me-1';
                button.innerHTML = '<i class="fas fa-chevron-down me-1"></i>View Details';
            }
        }
    </script>
    <script src="main.js"></script>
</body>
</html>