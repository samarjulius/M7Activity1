<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - GlowCare Skincare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Enhanced About Page Styles */
        .about-hero {
            background: linear-gradient(135deg, var(--primary-color), var(--dark-accent));
            color: white;
            padding: 4rem 0;
        }
        
        .feature-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(212, 132, 122, 0.2);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--dark-accent));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 1.8rem;
        }
        
        .team-section {
            background: white;
            padding: 4rem 0;
        }
        
        .team-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .team-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        
        .team-card:hover::before {
            left: 100%;
        }
        
        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(212, 132, 122, 0.2);
            border-color: var(--primary-color);
        }
        
        /* Updated Team Avatar for Real Photos */
        .team-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            overflow: hidden;
            border: 4px solid var(--primary-color);
            box-shadow: 0 10px 30px rgba(212, 132, 122, 0.3);
            position: relative;
            background: #f8f9fa;
        }
        
        .team-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .team-avatar:hover img {
            transform: scale(1.1);
        }
        
        /* Placeholder for when no image is provided */
        .team-avatar-placeholder {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--dark-accent));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            box-shadow: 0 10px 30px rgba(212, 132, 122, 0.3);
            border: 4px solid var(--primary-color);
        }
        
        .team-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-accent);
            margin-bottom: 0.5rem;
        }
        
        .team-role {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .team-info {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .university-badge {
            background: linear-gradient(135deg,rgb(114, 30, 30),rgb(152, 42, 42));
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 1rem;
        }
        
        .development-info {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 3rem 0;
            border-radius: 20px;
            margin: 2rem 0;
        }
        
        .tech-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-top: 1.5rem;
        }
        
        .tech-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--dark-accent));
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .project-timeline {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }
        
        .timeline-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .timeline-item:hover {
            background: var(--light-bg);
            transform: translateX(5px);
        }
        
        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.2rem;
        }
        
        /* Photo upload instruction */
        .photo-instruction {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .photo-instruction .instruction-icon {
            color: #856404;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        
        .photo-instruction .instruction-text {
            color: #856404;
            font-weight: 600;
            margin: 0;
        }
        
        @media (max-width: 768px) {
            .team-avatar,
            .team-avatar-placeholder {
                width: 120px;
                height: 120px;
            }
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
                    <li class="nav-item"><a class="nav-link active" href="about.php"><i class="fas fa-info-circle me-1"></i>About Us</a></li>
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
    <section class="about-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">About GlowCare</h1>
                    <p class="lead mb-4">
                        Dedicated to bringing you the finest skincare products with natural ingredients 
                        and innovative formulations for your perfect glow.
                    </p>
                    <div class="d-flex gap-3">
                        <div class="text-center">
                            <div class="h2 fw-bold mb-0">5+</div>
                            <small>Years Experience</small>
                        </div>
                        <div class="text-center">
                            <div class="h2 fw-bold mb-0">1000+</div>
                            <small>Happy Customers</small>
                        </div>
                        <div class="text-center">
                            <div class="h2 fw-bold mb-0">50+</div>
                            <small>Premium Products</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="fas fa-spa fa-10x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="fw-bold mb-4">Our Story</h2>
                    <p class="lead text-muted mb-4">
                        GlowCare was born from a passion for natural beauty and healthy skin. 
                        We believe that everyone deserves to feel confident and radiant in their own skin.
                    </p>
                    <p class="text-muted">
                        Our journey began with a simple mission: to create skincare products that not only 
                        deliver results but also nurture and protect your skin with the finest natural ingredients. 
                        Each product is carefully crafted with love and backed by science.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Why Choose GlowCare?</h2>
                    <p class="text-muted">Discover what makes us different</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card card h-100 p-4 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Natural Ingredients</h5>
                        <p class="text-muted">
                            We use only the finest natural and organic ingredients, 
                            sourced sustainably from around the world.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card card h-100 p-4 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-flask"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Scientific Formulation</h5>
                        <p class="text-muted">
                            Our products are developed with cutting-edge research 
                            and tested for effectiveness and safety.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card card h-100 p-4 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Cruelty-Free</h5>
                        <p class="text-muted">
                            We're committed to ethical practices and never test 
                            our products on animals.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card card h-100 p-4 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-recycle"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Sustainable</h5>
                        <p class="text-muted">
                            Our packaging is eco-friendly and we're committed 
                            to reducing our environmental impact.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card card h-100 p-4 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Expert Support</h5>
                        <p class="text-muted">
                            Our skincare experts are always here to help you 
                            choose the perfect products for your skin.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card card h-100 p-4 text-center">
                        <div class="feature-icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Quality Guarantee</h5>
                        <p class="text-muted">
                            We stand behind our products with a 100% satisfaction 
                            guarantee and quality promise.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Development Team Section -->
    <section class="team-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Meet the Development Team</h2>
                    <p class="text-muted">The talented students who brought GlowCare to life</p>
                </div>
            </div>
        
            
            <div class="row justify-content-center g-4">
                <div class="col-lg-5 col-md-6">
                    <div class="team-card">
                        <!-- Photo container for Julius -->
                        <div class="team-avatar">
                            <img src="pictures/samar.png" alt="Julius A. Samar" 
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="team-avatar-placeholder" style="display: none;">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                        </div>
                        <div class="team-name">Julius A. Samar</div>
                        <div class="team-role">CCSS Vice President</div>
                        <div class="team-info">
                            Specializing in backend development, database design, and system architecture. 
                            Passionate about creating efficient and scalable web applications.
                        </div>
                        <div class="university-badge">
                            <i class="fas fa-university me-2"></i>
                            University of the East - IT 2nd Year
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-5 col-md-6">
                    <div class="team-card">
                        <!-- Photo container for Gilliane -->
                        <div class="team-avatar">
                            <img src="pictures/villon.jpg" alt="Gilliane Gail Villon" 
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="team-avatar-placeholder" style="display: none;">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                        </div>
                        <div class="team-name">Gilliane Gail D. Villon</div>
                        <div class="team-role">UE President</div>
                        <div class="team-info">
                            Focused on creating beautiful, user-friendly interfaces and ensuring optimal user experience. 
                            Expertise in modern web design and responsive layouts.
                        </div>
                        <div class="university-badge">
                            <i class="fas fa-university me-2"></i>
                            University of the East - IT 2nd Year
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Development Information -->
            <div class="development-info">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 mx-auto text-center">
                            <h3 class="fw-bold mb-3">
                                <i class="fas fa-code me-2"></i>About This Project
                            </h3>
                            <p class="text-muted mb-4">
                                GlowCare Skincare E-commerce Website was developed as a comprehensive web application 
                                showcasing modern web development techniques and best practices. This project demonstrates 
                                the integration of frontend and backend technologies to create a fully functional 
                                e-commerce platform.
                            </p>
                            
                            <div class="tech-stack">
                                <span class="tech-badge"><i class="fab fa-php me-2"></i>PHP</span>
                                <span class="tech-badge"><i class="fas fa-database me-2"></i>MySQL</span>
                                <span class="tech-badge"><i class="fab fa-html5 me-2"></i>HTML5</span>
                                <span class="tech-badge"><i class="fab fa-css3-alt me-2"></i>CSS3</span>
                                <span class="tech-badge"><i class="fab fa-js me-2"></i>JavaScript</span>
                                <span class="tech-badge"><i class="fab fa-bootstrap me-2"></i>Bootstrap 5</span>
                                <span class="tech-badge"><i class="fas fa-server me-2"></i>XAMPP</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-5">
                        <div class="col-lg-6">
                            <div class="project-timeline">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-tasks me-2"></i>Development Highlights
                                </h5>
                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="fas fa-database"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Database Integration</div>
                                        <div class="text-muted small">Complete MySQL database implementation with XAMPP</div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">User Authentication</div>
                                        <div class="text-muted small">Secure login and registration system</div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">E-commerce Features</div>
                                        <div class="text-muted small">Shopping cart, product catalog, and order management</div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Responsive Design</div>
                                        <div class="text-muted small">Mobile-first approach with modern UI/UX</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="project-timeline">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-graduation-cap me-2"></i>Academic Achievement
                                </h5>
                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="fas fa-university"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Institution</div>
                                        <div class="text-muted small">University of the East</div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="fas fa-laptop-code"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Program</div>
                                        <div class="text-muted small">Bachelor of Science in Information Technology</div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Year Level</div>
                                        <div class="text-muted small">Second Year Students</div>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-icon">
                                        <i class="fas fa-project-diagram"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Project Type</div>
                                        <div class="text-muted small">Web Development Portfolio Project</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="fw-bold mb-3">Ready to Start Your Skincare Journey?</h2>
                    <p class="lead mb-4">
                        Discover our premium collection and find the perfect products for your skin type.
                    </p>
                    <a href="shop.php" class="btn btn-light btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Shop Now
                    </a>
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
    <script src="main.js"></script>
</body>
</html>