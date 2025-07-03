<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'products.php';
$user = $_SESSION['user'] ?? null;

// Get featured products (top-rated products)
$featured_products = array_filter($products, function($product) {
    return $product['rating'] >= 4.5;
});
$featured_products = array_slice($featured_products, 0, 6);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlowCare - Premium Skincare for Your Natural Glow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Enhanced Navigation */
        .navbar {
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-size: 2rem !important;
            font-weight: 800 !important;
            color: var(--primary-color) !important;
        }
        
        .navbar-brand i {
            font-size: 2.2rem !important;
            color: var(--primary-color) !important;
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
            font-size: 1.1rem;
            padding: 0.8rem 1.2rem !important;
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
            transform: translateY(-2px);
        }
        
        .navbar-nav .nav-link.active {
            color: var(--primary-color) !important;
            font-weight: 600;
        }

        /* Advanced Homepage Styles */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--dark-accent));
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding-top: 100px;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="rgba(255,255,255,0.1)" points="0,0 1000,300 1000,1000 0,700"/></svg>');
            background-size: cover;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            animation: fadeInUp 1s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .hero-title {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .hero-subtitle {
            font-size: 1.4rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            font-weight: 300;
            line-height: 1.6;
        }
        
        .hero-cta {
            display: flex;
            gap: 1.5rem;
            margin-top: 2.5rem;
        }
        
        .btn-hero {
            padding: 18px 35px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
            font-size: 1.1rem;
        }
        
        .btn-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-hero:hover::before {
            left: 100%;
        }
        
        .btn-hero:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        
        .btn-primary-hero {
            background: white;
            color: var(--primary-color);
        }
        
        .btn-outline-hero {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-outline-hero:hover {
            background: white;
            color: var(--primary-color);
        }
        
        .hero-icon {
            font-size: 20rem !important;
            opacity: 0.3;
            animation: pulse 3s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { 
                transform: scale(1);
                opacity: 0.3;
            }
            50% { 
                transform: scale(1.05);
                opacity: 0.5;
            }
        }
        
        /* Featured Products Section */
        .featured-section {
            padding: 6rem 0;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            position: relative;
            overflow: hidden;
        }
        
        .featured-section::before {
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
        
        .section-title {
            font-size: 3rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--dark-accent);
            position: relative;
            z-index: 2;
        }
        
        .section-subtitle {
            text-align: center;
            color: #6c757d;
            font-size: 1.3rem;
            margin-bottom: 4rem;
            position: relative;
            z-index: 2;
        }
        
        .product-card-home {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            position: relative;
            height: 100%;
        }
        
        .product-card-home:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 60px rgba(212, 132, 122, 0.25);
        }
        
        .product-image-home {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .product-card-home:hover .product-image-home {
            transform: scale(1.1);
        }
        
        .product-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.8rem;
        }
        
        .stars {
            color: #ffc107;
            font-size: 1rem;
        }
        
        .product-price-home {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        /* Brand Story Section */
        .brand-story {
            padding: 6rem 0;
            background: white;
        }
        
        .story-card {
            background: linear-gradient(135deg, var(--light-bg), white);
            border-radius: 25px;
            padding: 3.5rem;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            height: 100%;
        }
        
        .story-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(212, 132, 122, 0.2);
        }
        
        .story-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--dark-accent));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            color: white;
            font-size: 2.5rem;
            box-shadow: 0 10px 30px rgba(212, 132, 122, 0.3);
        }
        
        /* Statistics Section */
        .stats-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, var(--primary-color), var(--dark-accent));
            color: white;
        }
        
        .stat-card {
            text-align: center;
            padding: 3rem;
            background: rgba(255,255,255,0.15);
            border-radius: 20px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-10px);
            background: rgba(255,255,255,0.25);
        }
        
        .stat-number {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: white;
        }
        
        .stat-label {
            font-size: 1.2rem;
            font-weight: 600;
            opacity: 0.9;
        }
        
        /* Testimonials Section */
        .testimonials-section {
            padding: 6rem 0;
            background: #f8f9fa;
        }
        
        .testimonial-card {
            background: white;
            border-radius: 25px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            height: 100%;
        }
        
        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(212, 132, 122, 0.2);
        }
        
        .testimonial-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 2rem;
            background: linear-gradient(135deg, var(--primary-color), var(--dark-accent));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            box-shadow: 0 10px 30px rgba(212, 132, 122, 0.3);
        }
        
        .testimonial-text {
            font-style: italic;
            margin-bottom: 2rem;
            color: #6c757d;
            font-size: 1.2rem;
            line-height: 1.6;
        }
        
        .testimonial-author {
            font-weight: 600;
            color: var(--dark-accent);
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }
        
        /* Newsletter Section - Fixed Layout */
        .newsletter-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, var(--dark-accent), var(--primary-color));
            color: white;
        }
        
        .newsletter-container {
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }
        
        .newsletter-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .newsletter-description {
            font-size: 1.2rem;
            margin-bottom: 3rem;
            opacity: 0.9;
        }
        
        .newsletter-form {
            display: flex;
            gap: 1rem;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .newsletter-input {
            flex: 1;
            border: none;
            border-radius: 50px;
            padding: 18px 25px;
            font-size: 1.1rem;
            background: rgba(255,255,255,0.95);
            transition: all 0.3s ease;
        }
        
        .newsletter-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
            background: white;
        }
        
        .newsletter-btn {
            border-radius: 50px;
            padding: 18px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 2px solid white;
            background: white;
            color: var(--primary-color);
            transition: all 0.3s ease;
            white-space: nowrap;
            font-size: 1.1rem;
        }
        
        .newsletter-btn:hover {
            background: transparent;
            color: white;
            transform: translateY(-2px);
        }
        
        /* Scroll Animations */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }
        
        .animate-on-scroll.animated {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.8rem;
            }
            
            .hero-subtitle {
                font-size: 1.2rem;
            }
            
            .hero-cta {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }
            
            .btn-hero {
                width: 100%;
                max-width: 280px;
            }
            
            .section-title {
                font-size: 2.2rem;
            }
            
            .section-subtitle {
                font-size: 1.1rem;
            }
            
            .story-card {
                padding: 2.5rem;
                margin-bottom: 2rem;
            }
            
            .newsletter-form {
                flex-direction: column;
                gap: 1rem;
            }
            
            .newsletter-btn {
                width: 100%;
            }
            
            .hero-icon {
                font-size: 12rem !important;
            }
        }
        
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.6rem !important;
            }
            
            .navbar-brand i {
                font-size: 1.8rem !important;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-spa me-2"></i>GlowCare
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php"><i class="fas fa-home me-1"></i>Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="shop.php"><i class="fas fa-shopping-bag me-1"></i>Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php"><i class="fas fa-info-circle me-1"></i>About Us</a></li>
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
    <section class="hero-section text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title">Your Natural Glow Starts Here</h1>
                        <p class="hero-subtitle">
                            Discover premium skincare products crafted with the finest natural ingredients. 
                            Transform your skin and embrace your natural beauty with GlowCare.
                        </p>
                        <div class="hero-cta">
                            <a href="shop.php" class="btn btn-hero btn-primary-hero">
                                <i class="fas fa-shopping-bag me-2"></i>Shop Now
                            </a>
                            <a href="about.php" class="btn btn-hero btn-outline-hero">
                                <i class="fas fa-info-circle me-2"></i>Learn More
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="fas fa-spa hero-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="featured-section">
        <div class="container">
            <div class="animate-on-scroll">
                <h2 class="section-title">Featured Products</h2>
                <p class="section-subtitle">Our best-selling skincare essentials loved by thousands</p>
            </div>
            
            <div class="row g-4">
                <?php foreach ($featured_products as $product): ?>
                    <div class="col-lg-4 col-md-6 animate-on-scroll">
                        <div class="product-card-home">
                            <div class="position-relative">
                                <img src="<?= htmlspecialchars($product['image']) ?>" 
                                     alt="<?= htmlspecialchars($product['name']) ?>" 
                                     class="product-image-home"
                                     onerror="this.src='https://via.placeholder.com/300x250?text=Product+Image'">
                                <?php if ($product['rating'] >= 4.5): ?>
                                    <span class="badge bg-primary position-absolute top-0 start-0 m-3 px-3 py-2">Best Seller</span>
                                <?php endif; ?>
                            </div>
                            <div class="p-4">
                                <div class="product-rating">
                                    <div class="stars">
                                        <?php 
                                        $fullStars = floor($product['rating']);
                                        $hasHalfStar = ($product['rating'] - $fullStars) >= 0.5;
                                        
                                        for ($i = 1; $i <= 5; $i++): 
                                            if ($i <= $fullStars): ?>
                                                <i class="fas fa-star"></i>
                                            <?php elseif ($i == $fullStars + 1 && $hasHalfStar): ?>
                                                <i class="fas fa-star-half-alt"></i>
                                            <?php else: ?>
                                                <i class="far fa-star"></i>
                                            <?php endif;
                                        endfor; ?>
                                    </div>
                                    <span class="text-muted small">(<?= $product['rating'] ?>)</span>
                                </div>
                                <h5 class="fw-bold mb-2"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="text-muted mb-3"><?= htmlspecialchars($product['short_description']) ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="product-price-home">$<?= number_format($product['price'], 2) ?></div>
                                    <button class="btn btn-primary add-to-cart" data-product-id="<?= $product['id'] ?>">
                                        <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-5 animate-on-scroll">
                <a href="shop.php" class="btn btn-primary btn-lg px-5 py-3">
                    <i class="fas fa-eye me-2"></i>View All Products
                </a>
            </div>
        </div>
    </section>

    <!-- Brand Story Section -->
    <section class="brand-story">
        <div class="container">
            <div class="animate-on-scroll">
                <h2 class="section-title">Why Choose GlowCare?</h2>
                <p class="section-subtitle">Our commitment to natural beauty and healthy skin</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 animate-on-scroll">
                    <div class="story-card">
                        <div class="story-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Natural Ingredients</h4>
                        <p class="text-muted">
                            We carefully select the finest natural and organic ingredients 
                            from around the world to create products that nurture your skin.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 animate-on-scroll">
                    <div class="story-card">
                        <div class="story-icon">
                            <i class="fas fa-flask"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Science-Backed</h4>
                        <p class="text-muted">
                            Our formulations are developed with cutting-edge research 
                            and tested for safety and effectiveness by skincare experts.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 animate-on-scroll">
                    <div class="story-card">
                        <div class="story-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Cruelty-Free</h4>
                        <p class="text-muted">
                            We believe in ethical beauty. All our products are cruelty-free 
                            and never tested on animals.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6 animate-on-scroll">
                    <div class="stat-card">
                        <div class="stat-number" data-target="69">0</div>
                        <div class="stat-label">Happy Customers</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 animate-on-scroll">
                    <div class="stat-card">
                        <div class="stat-number" data-target="20">0</div>
                        <div class="stat-label">Premium Products</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 animate-on-scroll">
                    <div class="stat-card">
                        <div class="stat-number" data-target="34">0</div>
                        <div class="stat-label">Satisfaction Rate</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 animate-on-scroll">
                    <div class="stat-card">
                        <div class="stat-number" data-target="35">0</div>
                        <div class="stat-label">Years Experience</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <div class="animate-on-scroll">
                <h2 class="section-title">What Our Customers Say</h2>
                <p class="section-subtitle">Real stories from real people who love GlowCare</p>
            </div>
            
            <div class="row g-4 mt-4">
                <div class="col-lg-4 col-md-6 animate-on-scroll">
                    <div class="testimonial-card">
                        <div class="testimonial-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="testimonial-text">
                            "GlowCare has completely transformed my skincare routine. 
                            My skin has never looked better!"
                        </div>
                        <div class="testimonial-author">Sarah Johnson</div>
                        <div class="stars text-warning">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 animate-on-scroll">
                    <div class="testimonial-card">
                        <div class="testimonial-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="testimonial-text">
                            "Natural ingredients that actually work. I'm amazed by the results 
                            and will definitely continue using GlowCare."
                        </div>
                        <div class="testimonial-author">Emily Chen</div>
                        <div class="stars text-warning">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 animate-on-scroll">
                    <div class="testimonial-card">
                        <div class="testimonial-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="testimonial-text">
                            "Fast shipping, excellent customer service, and products that 
                            deliver on their promises. Highly recommended!"
                        </div>
                        <div class="testimonial-author">Michael Rodriguez</div>
                        <div class="stars text-warning">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-container animate-on-scroll">
                <h2 class="newsletter-title">Stay Updated</h2>
                <p class="newsletter-description">
                    Get the latest updates on new products, skincare tips, and exclusive offers.
                </p>
                <form class="newsletter-form" id="newsletterForm">
                    <input type="email" class="newsletter-input" 
                           placeholder="Enter your email address" required>
                    <button class="btn newsletter-btn" type="submit">
                        <i class="fas fa-paper-plane me-2"></i>Subscribe
                    </button>
                </form>
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
    <script src="main.js"></script>
    <script>
        // Scroll animations
        function animateOnScroll() {
            const elements = document.querySelectorAll('.animate-on-scroll');
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('animated');
                }
            });
        }
        
        // Counter animation
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                const increment = target / 100;
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target.toLocaleString();
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current).toLocaleString();
                    }
                }, 20);
            });
        }
        
        // Newsletter form
        document.getElementById('newsletterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            alert('Thank you for subscribing! We\'ll keep you updated with our latest offers.');
            this.reset();
        });
        
        // Initialize animations
        document.addEventListener('DOMContentLoaded', function() {
            animateOnScroll();
            
            // Animate counters when they come into view
            const statsSection = document.querySelector('.stats-section');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                        observer.disconnect();
                    }
                });
            });
            
            observer.observe(statsSection);
        });
        
        // Scroll event listener
        window.addEventListener('scroll', animateOnScroll);
    </script>
</body>
</html>