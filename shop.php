<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'products.php';

// Enhanced product categories for skincare
$categories = [
    'all' => 'All Products',
    'face-care' => 'Face Care',
    'body-care' => 'Body Care',
    'sun-protection' => 'Sun Protection',
    'cleansers' => 'Cleansers',
    'serums' => 'Serums & Treatments',
    'moisturizers' => 'Moisturizers',
    'toners' => 'Toners & Mists',
    'masks' => 'Face Masks',
    'eye-care' => 'Eye Care'
];

// Filter by category
$selectedCategory = $_GET['category'] ?? 'all';
$filteredProducts = $products;

if ($selectedCategory !== 'all') {
    // Map main categories to subcategories
    $categoryMapping = [
        'face-care' => ['cleansers', 'serums', 'moisturizers', 'toners', 'masks', 'eye-care', 'treatments'],
        'body-care' => ['moisturizers', 'cleansers'],
        'sun-protection' => ['sunscreens']
    ];
    
    if (isset($categoryMapping[$selectedCategory])) {
        $filteredProducts = array_filter($products, function($product) use ($categoryMapping, $selectedCategory) {
            return in_array($product['category'], $categoryMapping[$selectedCategory]);
        });
    } else {
        $filteredProducts = array_filter($products, function($product) use ($selectedCategory) {
            return $product['category'] === $selectedCategory;
        });
    }
}

// Sort functionality
$sort = $_GET['sort'] ?? 'name';
$products_sorted = $filteredProducts;

switch ($sort) {
    case 'price_low':
        usort($products_sorted, fn($a, $b) => $a['price'] <=> $b['price']);
        break;
    case 'price_high':
        usort($products_sorted, fn($a, $b) => $b['price'] <=> $a['price']);
        break;
    case 'newest':
        usort($products_sorted, fn($a, $b) => $b['id'] <=> $a['id']);
        break;
    case 'recommended':
        usort($products_sorted, fn($a, $b) => $b['rating'] <=> $a['rating']);
        break;
    default:
        usort($products_sorted, fn($a, $b) => strcmp($a['name'], $b['name']));
}

$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - GlowCare Skincare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Enhanced Category Cards */
        .category-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: 2px solid transparent;
            background: linear-gradient(135deg, var(--light-bg), white);
            position: relative;
            overflow: hidden;
        }
        
        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        
        .category-card:hover::before {
            left: 100%;
        }
        
        .category-card:hover {
            transform: translateY(-8px) scale(1.02);
            border-color: var(--primary-color);
            box-shadow: 0 15px 40px rgba(212, 132, 122, 0.3);
        }
        
        .category-card.active {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, var(--primary-color), var(--dark-accent));
            color: white;
            transform: translateY(-5px);
        }
        
        .category-card.active .category-icon {
            color: white !important;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .category-icon {
            font-size: 2.8rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        /* Advanced Filter Section */
        .filter-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            position: relative;
            overflow: hidden;
        }
        
        .filter-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(212, 132, 122, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .filter-content {
            position: relative;
            z-index: 2;
        }
        
        /* Advanced Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
            padding: 1rem 0;
        }
        
        /* Enhanced Product Cards */
        .product-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            background: white;
        }
        
        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(212, 132, 122, 0.1), rgba(160, 120, 109, 0.1));
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }
        
        .product-card:hover::before {
            opacity: 1;
        }
        
        .product-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 20px 50px rgba(212, 132, 122, 0.25);
            border-color: var(--primary-color);
        }
        
        .product-card-content {
            position: relative;
            z-index: 2;
        }
        
        /* Product Image Container */
        .product-image-container {
            position: relative;
            overflow: hidden;
            height: 220px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }
        
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        
        .product-card:hover .product-image {
            transform: scale(1.1);
        }
        
        /* Product Badges */
        .product-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(135deg, var(--primary-color), var(--dark-accent));
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            z-index: 3;
            box-shadow: 0 4px 15px rgba(212, 132, 122, 0.3);
        }
        
        /* Product Actions Overlay */
        .product-actions {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            opacity: 0;
            transform: translateX(20px);
            transition: all 0.3s ease;
            z-index: 3;
        }
        
        .product-card:hover .product-actions {
            opacity: 1;
            transform: translateX(0);
        }
        
        .action-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .action-btn.view-btn {
            background: rgba(255, 255, 255, 0.9);
            color: var(--primary-color);
        }
        
        .action-btn.view-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }
        
        .action-btn.add-btn {
            background: rgba(212, 132, 122, 0.9);
            color: white;
        }
        
        .action-btn.add-btn:hover {
            background: var(--dark-accent);
            transform: scale(1.1);
        }
        
        /* Rating Stars */
        .rating-stars {
            display: flex;
            gap: 2px;
            margin-bottom: 10px;
        }
        
        .star {
            color: #ffc107;
            font-size: 1rem;
        }
        
        .star.empty {
            color: #e9ecef;
        }
        
        /* Price Display */
        .product-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        /* Advanced Sort Dropdown */
        .sort-dropdown {
            border-radius: 25px;
            border: 2px solid var(--border-color);
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        
        .sort-dropdown:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(212, 132, 122, 0.25);
        }
        
        /* Product Details Modal Styles */
        .modal-content {
            border-radius: 20px;
            border: none;
            overflow: hidden;
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), var(--dark-accent));
            color: white;
            border: none;
            padding: 1.5rem 2rem;
        }
        
        .modal-body {
            padding: 2rem;
        }
        
        .product-detail-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 1.5rem;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
        }
        
        .feature-list li {
            padding: 8px 0;
            border-bottom: 1px solid var(--border-color);
            position: relative;
            padding-left: 25px;
        }
        
        .feature-list li:before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .feature-list li:last-child {
            border-bottom: none;
        }
        
        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: 1.5rem;
            }
            
            .category-card {
                margin-bottom: 1rem;
            }
            
            .filter-section {
                padding: 1.5rem;
            }
        }
        
        /* Search Bar Enhancement */
        .search-container {
            position: relative;
            margin-bottom: 2rem;
        }
        
        .search-input {
            border-radius: 25px;
            border: 2px solid var(--border-color);
            padding: 12px 50px 12px 20px;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(212, 132, 122, 0.25);
            outline: none;
        }
        
        .search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary-color);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .search-btn:hover {
            background: var(--dark-accent);
            transform: translateY(-50%) scale(1.1);
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
                    <li class="nav-item"><a class="nav-link active" href="shop.php"><i class="fas fa-shopping-bag me-1"></i>Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php"><i class="fas fa-info-circle me-2"></i>About Us</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart me-1"></i>Cart
                            <span class="badge bg-primary cart-badge ms-1" style="display: none;">0</span>
                        </a>
                    </li>
                    <?php if ($user): ?>
                        <li class="nav-item"><a class="nav-link" href="welcome.php"><i class="fas fa-user me-1"></i>My Account</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
                        <li class="nav-item">
                            <span class="navbar-text">
                                <i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($user['fullname']) ?>
                            </span>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="register.php"><i class="fas fa-user-plus me-1"></i>Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-white text-center py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h1 class="display-4 fw-bold mb-3">Our Skincare Collection</h1>
                    <p class="lead mb-4">Discover premium skincare products crafted with natural ingredients for your perfect glow</p>
                </div>
            </div>
        </div>
    </section>

    <div class="container my-5">
        <!-- Advanced Search Bar -->
        <div class="search-container">
            <input type="text" class="search-input" id="productSearch" placeholder="Search for products...">
            <button class="search-btn" type="button">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <!-- Enhanced Filter Section -->
        <div class="filter-section">
            <div class="filter-content">
                <div class="row align-items-center mb-4">
                    <div class="col-lg-8">
                        <h3 class="fw-bold mb-0">
                            <i class="fas fa-filter me-2"></i>Filter Products
                        </h3>
                        <p class="text-muted mb-0">Find your perfect skincare match</p>
                    </div>
                    <div class="col-lg-4">
                        <select class="form-select sort-dropdown" onchange="location.href='?category=<?= $selectedCategory ?>&sort=' + this.value">
                            <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Sort by Name</option>
                            <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="recommended" <?= $sort === 'recommended' ? 'selected' : '' ?>>Most Popular</option>
                            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                        </select>
                    </div>
                </div>

                <!-- Enhanced Category Filter -->
                <div class="row g-3">
                    <?php 
                    $categoryIcons = [
                        'all' => 'fas fa-th-large',
                        'face-care' => 'fas fa-smile',
                        'body-care' => 'fas fa-hand-holding-heart',
                        'sun-protection' => 'fas fa-sun',
                        'cleansers' => 'fas fa-tint',
                        'serums' => 'fas fa-vial',
                        'moisturizers' => 'fas fa-snowflake',
                        'toners' => 'fas fa-spray-can',
                        'masks' => 'fas fa-mask',
                        'eye-care' => 'fas fa-eye'
                    ];
                    
                    foreach ($categories as $key => $name): 
                        $isActive = $selectedCategory === $key;
                        $icon = $categoryIcons[$key] ?? 'fas fa-tag';
                    ?>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                            <a href="?category=<?= $key ?>&sort=<?= $sort ?>" class="text-decoration-none">
                                <div class="category-card text-center p-3 h-100 <?= $isActive ? 'active' : '' ?>">
                                    <i class="<?= $icon ?> category-icon"></i>
                                    <h6 class="fw-bold mb-0"><?= $name ?></h6>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Products Display -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="fw-bold">
                        <i class="fas fa-shopping-bag me-2 text-primary"></i>
                        <?= $categories[$selectedCategory] ?> 
                        <span class="badge bg-primary ms-2"><?= count($products_sorted) ?></span>
                    </h2>
                    <div class="text-muted">
                        Showing <?= count($products_sorted) ?> products
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Product Grid -->
        <div class="product-grid" id="productGrid">
            <?php foreach ($products_sorted as $product): ?>
                <div class="product-card" data-product-name="<?= strtolower($product['name']) ?>">
                    <div class="product-card-content">
                        <div class="product-image-container">
                            <?php if ($product['rating'] >= 4.5): ?>
                                <span class="product-badge">Best Seller</span>
                            <?php elseif (!$product['in_stock']): ?>
                                <span class="product-badge" style="background: linear-gradient(135deg, #dc3545, #c82333);">Out of Stock</span>
                            <?php endif; ?>
                            
                            <img src="<?= htmlspecialchars($product['image']) ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>" 
                                 class="product-image"
                                 onerror="this.src='https://via.placeholder.com/300x220?text=Product+Image'">
                            
                            <div class="product-actions">
                                <button class="action-btn view-btn" 
                                        onclick="showProductDetails(<?= $product['id'] ?>)" 
                                        title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if ($product['in_stock']): ?>
                                    <button class="action-btn add-btn add-to-cart" 
                                            data-product-id="<?= $product['id'] ?>" 
                                            title="Add to Cart">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card-body p-3">
                            <div class="rating-stars mb-2">
                                <?php 
                                $fullStars = floor($product['rating']);
                                $hasHalfStar = ($product['rating'] - $fullStars) >= 0.5;
                                
                                for ($i = 1; $i <= 5; $i++): 
                                    if ($i <= $fullStars): ?>
                                        <i class="fas fa-star star"></i>
                                    <?php elseif ($i == $fullStars + 1 && $hasHalfStar): ?>
                                        <i class="fas fa-star-half-alt star"></i>
                                    <?php else: ?>
                                        <i class="fas fa-star star empty"></i>
                                    <?php endif;
                                endfor; ?>
                                <span class="ms-2 text-muted small">(<?= $product['rating'] ?>)</span>
                            </div>
                            
                            <h5 class="card-title fw-bold mb-2"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text text-muted small mb-3"><?= htmlspecialchars($product['short_description']) ?></p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="product-price">$<?= number_format($product['price'], 2) ?></div>
                                <span class="badge bg-light text-dark"><?= ucfirst($product['category']) ?></span>
                            </div>
                            
                            <?php if (!$product['in_stock']): ?>
                                <div class="mt-2">
                                    <span class="badge bg-danger">Out of Stock</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($products_sorted)): ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h3 class="text-muted">No products found</h3>
                <p class="text-muted">Try adjusting your filters or search terms</p>
                <a href="shop.php" class="btn btn-primary">View All Products</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Product Details Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">
                        <i class="fas fa-info-circle me-2"></i>Product Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <div class="text-center py-5">
                        <div class="loading-spinner"></div>
                        <p class="mt-3 text-muted">Loading product details...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    <script src="main.js"></script>
    <script>
        // Product search functionality
        document.getElementById('productSearch').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const productCards = document.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                const productName = card.getAttribute('data-product-name');
                if (productName.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Show product details in modal
        function showProductDetails(productId) {
            const modal = new bootstrap.Modal(document.getElementById('productModal'));
            const modalContent = document.getElementById('modalContent');
            
            // Show loading state
            modalContent.innerHTML = `
                <div class="text-center py-5">
                    <div class="loading-spinner"></div>
                    <p class="mt-3 text-muted">Loading product details...</p>
                </div>
            `;
            
            modal.show();
            
            // Fetch product details
            fetch(`get_product_details.php?id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const product = data.product;
                        modalContent.innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <img src="${product.image}" alt="${product.name}" class="product-detail-image">
                                </div>
                                <div class="col-md-6">
                                    <h3 class="fw-bold text-primary mb-3">${product.name}</h3>
                                    
                                    <div class="rating-stars mb-3">
                                        ${generateStars(product.rating)}
                                        <span class="ms-2 text-muted">(${product.rating}/5)</span>
                                    </div>
                                    
                                    <h4 class="text-primary mb-3">$${parseFloat(product.price).toFixed(2)}</h4>
                                    
                                    <p class="text-muted mb-4">${product.description}</p>
                                    
                                    <div class="mb-4">
                                        <h6 class="fw-bold mb-2">Key Features:</h6>
                                        <ul class="feature-list">
                                            ${product.features.map(feature => `<li>${feature}</li>`).join('')}
                                        </ul>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <h6 class="fw-bold mb-2">Ingredients:</h6>
                                        <p class="small text-muted">${product.ingredients}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <h6 class="fw-bold mb-2">Usage Instructions:</h6>
                                        <p class="small text-muted">${product.usage}</p>
                                    </div>
                                    
                                    ${product.in_stock ? 
                                        `<button class="btn btn-primary btn-lg w-100 add-to-cart" data-product-id="${product.id}">
                                            <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                        </button>` : 
                                        `<button class="btn btn-secondary btn-lg w-100" disabled>
                                            <i class="fas fa-times me-2"></i>Out of Stock
                                        </button>`
                                    }
                                </div>
                            </div>
                        `;
                        
                        // Re-initialize cart functionality for the new button
                        initializeCart();
                    } else {
                        modalContent.innerHTML = `
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h5>Product not found</h5>
                                <p class="text-muted">Sorry, we couldn't load the product details.</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalContent.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                            <h5>Error loading product</h5>
                            <p class="text-muted">Please try again later.</p>
                        </div>
                    `;
                });
        }

        // Generate star rating HTML
        function generateStars(rating) {
            let starsHtml = '';
            const fullStars = Math.floor(rating);
            const hasHalfStar = (rating - fullStars) >= 0.5;
            
            for (let i = 1; i <= 5; i++) {
                if (i <= fullStars) {
                    starsHtml += '<i class="fas fa-star star"></i>';
                } else if (i === fullStars + 1 && hasHalfStar) {
                    starsHtml += '<i class="fas fa-star-half-alt star"></i>';
                } else {
                    starsHtml += '<i class="fas fa-star star empty"></i>';
                }
            }
            
            return starsHtml;
        }

        // Initialize cart functionality when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeCart();
        });
    </script>
</body>
</html>