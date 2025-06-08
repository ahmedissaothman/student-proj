<?php
session_start();

// Check if user is already logged in and show dashboard link
$is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
$user_role = $is_logged_in ? $_SESSION['user_role'] : null;
$user_name = $is_logged_in ? $_SESSION['full_name'] : null;

// Include configuration if available
if (file_exists('includes/config.php')) {
    require_once 'includes/config.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSICT Offices Requirement Ordering System | Tanzania People's Defence Forces</title>
    
    <!-- Meta Tags -->
    <meta name="description" content="Digital platform for managing office supply requisitions at Military School of Information and Communication Technology (MSICT), Tanzania People's Defence Forces">
    <meta name="keywords" content="MSICT, military, ordering system, office supplies, Tanzania Defence Forces">
    <meta name="author" content="MSICT Development Team">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/components.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Index Page Specific Styles */
        .index-page {
            background: var(--light-gray);
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 4rem 0 6rem;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        
        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 2;
        }
        
        .hero-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        
        .hero-text h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        
        .hero-text h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            opacity: 0.9;
            color: var(--accent-color);
        }
        
        .hero-text p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.8;
            line-height: 1.6;
        }
        
        .hero-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .hero-btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .hero-btn-primary {
            background: var(--accent-color);
            color: var(--text-color);
        }
        
        .hero-btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }
        
        .hero-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        
        .hero-visual {
            text-align: center;
        }
        
        .system-mockup {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .mockup-screen {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .mockup-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e1e5e9;
        }
        
        .mockup-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        
        .dot-red { background: #ff5f56; }
        .dot-yellow { background: #ffbd2e; }
        .dot-green { background: #27ca3f; }
        
        .mockup-content {
            color: var(--text-color);
            text-align: left;
        }
        
        .mockup-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f3f4;
        }
        
        .mockup-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }
        
        /* Navigation Bar */
        .navbar {
            background: white;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 3px solid var(--primary-color);
        }
        
        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
        }
        
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: var(--primary-color);
        }
        
        .brand-logo {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .brand-text h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0;
        }
        
        .brand-text p {
            font-size: 0.8rem;
            margin: 0;
            opacity: 0.7;
        }
        
        .navbar-nav {
            display: flex;
            list-style: none;
            gap: 2rem;
            margin: 0;
            padding: 0;
        }
        
        .nav-link {
            text-decoration: none;
            color: var(--text-color);
            font-weight: 500;
            transition: color 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover,
        .nav-link.active {
            color: var(--primary-color);
        }
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -1.5rem;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary-color);
        }
        
        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .login-btn {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .login-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        
        .dashboard-btn {
            background: var(--accent-color);
            color: var(--text-color);
        }
        
        /* Features Section */
        .features-section {
            padding: 4rem 0;
            background: white;
        }
        
        .section-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .section-subtitle {
            font-size: 1.2rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: all 0.3s ease;
            border-top: 4px solid var(--primary-color);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
        }
        
        .feature-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .feature-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
            text-align: left;
        }
        
        .feature-list li {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }
        
        .feature-list i {
            color: var(--primary-color);
            font-size: 0.8rem;
        }
        
        /* Services Section */
        .services-section {
            padding: 4rem 0;
            background: var(--light-gray);
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }
        
        .service-card {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }
        
        .service-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .service-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        
        .service-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .service-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .service-features {
            list-style: none;
            padding: 0;
        }
        
        .service-features li {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .service-features i {
            color: #28a745;
            font-size: 0.8rem;
        }
        
        /* Statistics Section */
        .stats-section {
            padding: 4rem 0;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
        }
        
        .stat-item {
            padding: 1.5rem;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .stat-description {
            font-size: 0.9rem;
            opacity: 0.7;
            margin-top: 0.5rem;
        }
        
        /* About Section */
        .about-section {
            padding: 4rem 0;
            background: white;
        }
        
        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        
        .about-text h3 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
        
        .about-text p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }
        
        .about-highlights {
            list-style: none;
            padding: 0;
        }
        
        .about-highlights li {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            padding: 1rem;
            background: var(--light-gray);
            border-radius: var(--border-radius);
        }
        
        .highlight-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }
        
        .about-visual {
            position: relative;
        }
        
        .about-image {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
        }
        
        /* Contact Section */
        .contact-section {
            padding: 4rem 0;
            background: var(--light-gray);
        }
        
        .contact-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
        }
        
        .contact-info h3 {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }
        
        .contact-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        
        .contact-details h4 {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }
        
        .contact-details p {
            color: #666;
            margin: 0;
        }
        
        .quick-links {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }
        
        .quick-links h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
        
        .quick-links-list {
            list-style: none;
            padding: 0;
        }
        
        .quick-links-list li {
            margin-bottom: 1rem;
        }
        
        .quick-links-list a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            color: var(--text-color);
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: all 0.3s ease;
            background: var(--light-gray);
        }
        
        .quick-links-list a:hover {
            background: var(--primary-color);
            color: white;
            transform: translateX(5px);
        }
        
        .quick-link-icon {
            width: 35px;
            height: 35px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1rem;
        }
        
        .quick-links-list a:hover .quick-link-icon {
            background: white;
            color: var(--primary-color);
        }
        
        /* Mobile Menu */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary-color);
            cursor: pointer;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar-nav {
                display: none;
            }
            
            .mobile-menu-toggle {
                display: block;
            }
            
            .hero-content,
            .about-content,
            .contact-content {
                grid-template-columns: 1fr;
                gap: 2rem;
                text-align: center;
            }
            
            .hero-text h1 {
                font-size: 2.5rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .navbar-container {
                padding: 0 1rem;
            }
            
            .section-container {
                padding: 0 1rem;
            }
            
            .hero-container {
                padding: 0 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .hero-text h1 {
                font-size: 2rem;
            }
            
            .hero-actions {
                flex-direction: column;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .features-grid,
            .services-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="index-page">
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="#" class="navbar-brand">
                <div class="brand-logo">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="brand-text">
                    <h3>MSICT</h3>
                    <p>Ordering System</p>
                </div>
            </a>
            
            <ul class="navbar-nav">
                <li><a href="#home" class="nav-link active">Home</a></li>
                <li><a href="#features" class="nav-link">Features</a></li>
                <li><a href="#services" class="nav-link">Services</a></li>
                <li><a href="#about" class="nav-link">About</a></li>
                <li><a href="#contact" class="nav-link">Contact</a></li>
            </ul>
            
            <div class="navbar-actions">
                <?php if ($is_logged_in): ?>
                    <a href="dashboard/<?php echo $user_role; ?>-dashboard.php" class="login-btn dashboard-btn">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                    <a href="auth/logout.php" class="login-btn" style="background: #dc3545;">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                <?php else: ?>
                    <a href="auth/login.php" class="login-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        Login
                    </a>
                <?php endif; ?>
                
                <button class="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>MSICT Offices Requirement Ordering System</h1>
                    <h2>Tanzania People's Defence Forces</h2>
                    <p>Streamline your office supply requisitions with our secure, efficient, and user-friendly digital platform designed specifically for military operations at the Military School of Information and Communication Technology.</p>
                    
                    <div class="hero-actions">
                        <?php if (!$is_logged_in): ?>
                            <a href="auth/login.php" class="hero-btn hero-btn-primary">
                                <i class="fas fa-rocket"></i>
                                Get Started
                            </a>
                        <?php else: ?>
                            <a href="dashboard/<?php echo $user_role; ?>-dashboard.php" class="hero-btn hero-btn-primary">
                                <i class="fas fa-tachometer-alt"></i>
                                Go to Dashboard
                            </a>
                        <?php endif; ?>
                        <a href="#features" class="hero-btn hero-btn-secondary">
                            <i class="fas fa-info-circle"></i>
                            Learn More
                        </a>
                    </div>
                </div>
                
                <div class="hero-visual">
                    <div class="system-mockup">
                        <div class="mockup-screen">
                            <div class="mockup-header">
                                <div class="mockup-dot dot-red"></div>
                                <div class="mockup-dot dot-yellow"></div>
                                <div class="mockup-dot dot-green"></div>
                                <span style="margin-left: 1rem; font-size: 0.8rem; color: #666;">MSICT Dashboard</span>
                            </div>
                            <div class="mockup-content">
                                <div class="mockup-item">
                                    <div class="mockup-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; margin-bottom: 0.25rem;">New Request</div>
                                        <div style="font-size: 0.8rem; color: #666;">Submit office supply requisition</div>
                                    </div>
                                </div>
                                <div class="mockup-item">
                                    <div class="mockup-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; margin-bottom: 0.25rem;">Track Orders</div>
                                        <div style="font-size: 0.8rem; color: #666;">Monitor request status in real-time</div>
                                    </div>
                                </div>
                                <div class="mockup-item">
                                    <div class="mockup-icon">
                                        <i class="fas fa-chart-bar"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; margin-bottom: 0.25rem;">View Reports</div>
                                        <div style="font-size: 0.8rem; color: #666;">Access comprehensive analytics</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="section-container">
            <div class="section-header">
                <h2 class="section-title">Powerful Features</h2>
                <p class="section-subtitle">Discover how our system transforms office supply management with cutting-edge features designed for military efficiency and security.</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Military-Grade Security</h3>
                    <p class="feature-description">Advanced encryption and secure authentication protocols protect all sensitive military data and communications.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Multi-factor authentication</li>
                        <li><i class="fas fa-check"></i> Role-based access control</li>
                        <li><i class="fas fa-check"></i> Encrypted data transmission</li>
                        <li><i class="fas fa-check"></i> Audit trails and logging</li>
                    </ul>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="feature-title">Real-Time Tracking</h3>
                    <p class="feature-description">Monitor your requisitions from submission to delivery with live status updates and notifications.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Live order status</li>
                        <li><i class="fas fa-check"></i> Automated notifications</li>
                        <li><i class="fas fa-check"></i> Delivery tracking</li>
                        <li><i class="fas fa-check"></i> History and timeline</li>
                    </ul>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="feature-title">Multi-Department Collaboration</h3>
                    <p class="feature-description">Seamless coordination between Quarter Master Office, CI Office, Intelligence Office, and other MSICT departments.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Department-specific workflows</li>
                        <li><i class="fas fa-check"></i> Cross-department approvals</li>
                        <li><i class="fas fa-check"></i> Collaborative planning</li>
                        <li><i class="fas fa-check"></i> Resource sharing</li>
                    </ul>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="feature-title">Advanced Analytics</h3>
                    <p class="feature-description">Comprehensive reporting and data analytics to optimize procurement decisions and resource allocation.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Custom reports</li>
                        <li><i class="fas fa-check"></i> Usage analytics</li>
                        <li><i class="fas fa-check"></i> Budget tracking</li>
                        <li><i class="fas fa-check"></i> Performance metrics</li>
                    </ul>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">Mobile Responsive</h3>
                    <p class="feature-description">Access the system from any device - desktop, tablet, or smartphone with full functionality maintained.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Cross-device compatibility</li>
                        <li><i class="fas fa-check"></i> Touch-friendly interface</li>
                        <li><i class="fas fa-check"></i> Offline capabilities</li>
                        <li><i class="fas fa-check"></i> Progressive web app</li>
                    </ul>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h3 class="feature-title">Automated Workflows</h3>
                    <p class="feature-description">Streamlined approval processes with automated routing and intelligent business rules for efficient operations.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check"></i> Smart routing</li>
                        <li><i class="fas fa-check"></i> Approval hierarchies</li>
                        <li><i class="fas fa-check"></i> Business rules engine</li>
                        <li><i class="fas fa-check"></i> Exception handling</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="section-container">
            <div class="section-header">
                <h2 class="section-title">Our Services</h2>
                <p class="section-subtitle">Comprehensive solutions tailored for MSICT departments to enhance operational efficiency and maintain military standards.</p>
            </div>
            
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-header">
                        <div class="service-icon">
                            <i class="fas fa-file-plus"></i>
                        </div>
                        <h3 class="service-title">Digital Requisition Management</h3>
                    </div>
                    <p class="service-description">Replace paper-based processes with digital forms, automated approvals, and electronic document management for improved efficiency.</p>
                    <ul class="service-features">
                        <li><i class="fas fa-check"></i> Electronic forms and templates</li>
                        <li><i class="fas fa-check"></i> Digital signatures and approvals</li>
                        <li><i class="fas fa-check"></i> Document version control</li>
                        <li><i class="fas fa-check"></i> Automated routing and escalation</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-header">
                        <div class="service-icon">
                            <i class="fas fa-warehouse"></i>
                        </div>
                        <h3 class="service-title">Inventory Integration</h3>
                    </div>
                    <p class="service-description">Connect with inventory systems to provide real-time stock levels, automatic reorder points, and supply chain visibility.</p>
                    <ul class="service-features">
                        <li><i class="fas fa-check"></i> Real-time stock levels</li>
                        <li><i class="fas fa-check"></i> Automatic reorder alerts</li>
                        <li><i class="fas fa-check"></i> Supply chain tracking</li>
                        <li><i class="fas fa-check"></i> Vendor management</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-header">
                        <div class="service-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h3 class="service-title">Role-Based Access Control</h3>
                    </div>
                    <p class="service-description">Secure authentication system with granular permissions ensuring only authorized personnel access sensitive information.</p>
                    <ul class="service-features">
                        <li><i class="fas fa-check"></i> Multi-level user roles</li>
                        <li><i class="fas fa-check"></i> Department-based permissions</li>
                        <li><i class="fas fa-check"></i> Security clearance integration</li>
                        <li><i class="fas fa-check"></i> Access audit logs</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-header">
                        <div class="service-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3 class="service-title">Smart Notifications</h3>
                    </div>
                    <p class="service-description">Intelligent notification system keeping all stakeholders informed about request status, approvals, and important updates.</p>
                    <ul class="service-features">
                        <li><i class="fas fa-check"></i> Email and SMS alerts</li>
                        <li><i class="fas fa-check"></i> Real-time dashboard updates</li>
                        <li><i class="fas fa-check"></i> Customizable notification rules</li>
                        <li><i class="fas fa-check"></i> Escalation procedures</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-header">
                        <div class="service-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <h3 class="service-title">Business Intelligence</h3>
                    </div>
                    <p class="service-description">Advanced reporting and analytics tools providing insights for better decision-making and resource optimization.</p>
                    <ul class="service-features">
                        <li><i class="fas fa-check"></i> Interactive dashboards</li>
                        <li><i class="fas fa-check"></i> Custom report builder</li>
                        <li><i class="fas fa-check"></i> Data visualization</li>
                        <li><i class="fas fa-check"></i> Predictive analytics</li>
                    </ul>
                </div>
                
                <div class="service-card">
                    <div class="service-header">
                        <div class="service-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="service-title">24/7 Support</h3>
                    </div>
                    <p class="service-description">Round-the-clock technical support and system maintenance ensuring continuous operation and quick issue resolution.</p>
                    <ul class="service-features">
                        <li><i class="fas fa-check"></i> 24/7 helpdesk support</li>
                        <li><i class="fas fa-check"></i> System monitoring</li>
                        <li><i class="fas fa-check"></i> Regular maintenance</li>
                        <li><i class="fas fa-check"></i> User training programs</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="section-container">
            <div class="section-header">
                <h2 class="section-title" style="color: white;">System Performance</h2>
                <p class="section-subtitle" style="color: rgba(255,255,255,0.8);">Real-time statistics showcasing the efficiency and impact of our ordering system.</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">1,247</span>
                    <div class="stat-label">Total Requests Processed</div>
                    <div class="stat-description">Since system deployment</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number">98.5%</span>
                    <div class="stat-label">System Uptime</div>
                    <div class="stat-description">Reliable 24/7 availability</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number">45%</span>
                    <div class="stat-label">Time Reduction</div>
                    <div class="stat-description">In processing requests</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number">12</span>
                    <div class="stat-label">Active Departments</div>
                    <div class="stat-description">Using the system</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number">156</span>
                    <div class="stat-label">Active Users</div>
                    <div class="stat-description">Military personnel</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number">99.2%</span>
                    <div class="stat-label">User Satisfaction</div>
                    <div class="stat-description">Based on feedback</div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="section-container">
            <div class="about-content">
                <div class="about-text">
                    <h3>About MSICT Ordering System</h3>
                    <p>The MSICT Offices Requirement Ordering System represents a significant digital transformation initiative at the Military School of Information and Communication Technology, Tanzania People's Defence Forces.</p>
                    
                    <p>Developed by a dedicated team of military IT specialists and supervised by Lt. Mwala, this system addresses the critical need for efficient, secure, and transparent management of office supply requisitions across all MSICT departments.</p>
                    
                    <p>Our solution eliminates paper-based bottlenecks, enhances inter-departmental coordination, and provides real-time visibility into procurement processes while maintaining the highest security standards required in a military environment.</p>
                    
                    <ul class="about-highlights">
                        <li>
                            <div class="highlight-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div>
                                <strong>Educational Excellence</strong><br>
                                Supporting ICT training and military education at MSICT
                            </div>
                        </li>
                        <li>
                            <div class="highlight-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <strong>Military Standards</strong><br>
                                Built to meet strict military security and operational requirements
                            </div>
                        </li>
                        <li>
                            <div class="highlight-icon">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <div>
                                <strong>Innovation Focus</strong><br>
                                Leveraging modern technology for operational efficiency
                            </div>
                        </li>
                        <li>
                            <div class="highlight-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <strong>Team Collaboration</strong><br>
                                Developed by Group 3 - DIT Class 2023 students
                            </div>
                        </li>
                    </ul>
                </div>
                
                <div class="about-visual">
                    <div class="about-image">
                        <i class="fas fa-university"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section">
        <div class="section-container">
            <div class="section-header">
                <h2 class="section-title">Contact Information</h2>
                <p class="section-subtitle">Get in touch with our support team for assistance, training, or system inquiries.</p>
            </div>
            
            <div class="contact-content">
                <div class="contact-info">
                    <h3>Get Support</h3>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h4>MSICT Location</h4>
                            <p>Military School of Information and Communication Technology<br>
                            Tanzania People's Defence Forces<br>
                            Dar es Salaam, Tanzania</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Support Hotline</h4>
                            <p>+255 22 xxx xxxx (Internal: 2450)<br>
                            Available 24/7 for system emergencies</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Email Support</h4>
                            <p>support@msict-ordering.mil.tz<br>
                            it-support@msict.mil.tz</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Support Hours</h4>
                            <p>Monday - Friday: 07:00 - 17:00<br>
                            Emergency Support: 24/7</p>
                        </div>
                    </div>
                </div>
                
                <div class="quick-links">
                    <h3>Quick Links</h3>
                    <ul class="quick-links-list">
                        <li>
                            <a href="auth/login.php">
                                <div class="quick-link-icon">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                                <div>
                                    <strong>System Login</strong><br>
                                    <small>Access your account</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="auth/forgot-password.php">
                                <div class="quick-link-icon">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div>
                                    <strong>Password Recovery</strong><br>
                                    <small>Reset your password</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="modules/requests/new-request.php">
                                <div class="quick-link-icon">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div>
                                    <strong>New Request</strong><br>
                                    <small>Submit requisition</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="modules/reports/analytics.php">
                                <div class="quick-link-icon">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <div>
                                    <strong>System Reports</strong><br>
                                    <small>View analytics</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#" onclick="openUserManual()">
                                <div class="quick-link-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div>
                                    <strong>User Manual</strong><br>
                                    <small>Documentation & guides</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#" onclick="openTrainingSchedule()">
                                <div class="quick-link-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div>
                                    <strong>Training Schedule</strong><br>
                                    <small>Upcoming sessions</small>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-top">
            <div class="footer-content">
                <!-- System Information -->
                <div class="footer-section">
                    <div class="system-info">
                        <div class="system-logo">
                            <div class="logo-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="system-details">
                                <h2>MSICT Ordering System</h2>
                                <p>Tanzania People's Defence Forces</p>
                            </div>
                        </div>
                        <div class="system-version">Version 1.0.0</div>
                        <div class="last-updated">Last Updated: <?php echo date('F Y'); ?></div>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="footer-section">
                    <h3><i class="fas fa-link"></i> Quick Links</h3>
                    <ul class="quick-links">
                        <li><a href="#home"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="#features"><i class="fas fa-star"></i> Features</a></li>
                        <li><a href="#services"><i class="fas fa-cogs"></i> Services</a></li>
                        <li><a href="#about"><i class="fas fa-info-circle"></i> About</a></li>
                        <li><a href="auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    </ul>
                </div>
                
                <!-- Contact Information -->
                <div class="footer-section">
                    <h3><i class="fas fa-address-book"></i> Contact Info</h3>
                    <div class="contact-info">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <span>MSICT, Dar es Salaam</span>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <span>+255 22 xxx xxxx</span>
                        </div>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <span>support@msict.mil.tz</span>
                        </div>
                    </div>
                </div>
                
                <!-- Support Information -->
                <div class="footer-section">
                    <h3><i class="fas fa-life-ring"></i> Support</h3>
                    <div class="support-section">
                        <div class="support-hours">
                            <h4>Support Hours</h4>
                            <p>Mon-Fri: 07:00-17:00</p>
                            <p>Emergency: 24/7</p>
                        </div>
                        <div class="emergency-contact">
                            <h4><i class="fas fa-exclamation-triangle"></i> Emergency</h4>
                            <p>Internal: 2450</p>
                            <p>Critical systems only</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="copyright">
                    <p>&copy; <?php echo date('Y'); ?> Tanzania People's Defence Forces - MSICT. All rights reserved.</p>
                </div>
                
                <div class="footer-stats">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-server"></i>
                        </div>
                        <span>Server: Online</span>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <span><?php echo rand(15, 45); ?> Active Users</span>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <span><?php echo date('H:i'); ?> Local Time</span>
                    </div>
                </div>
                
                <div class="system-status">
                    <div class="status-indicator">
                        <div class="status-dot online"></div>
                        <span>All Systems Operational</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- JavaScript Files -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/dashboard.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scrolling for navigation links
            const navLinks = document.querySelectorAll('.nav-link, .hero-btn-secondary');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href.startsWith('#')) {
                        e.preventDefault();
                        const target = document.querySelector(href);
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    }
                });
            });
            
            // Back to top button functionality
            const backToTop = document.getElementById('backToTop');
            
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTop.classList.add('visible');
                } else {
                    backToTop.classList.remove('visible');
                }
            });
            
            backToTop.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
            
            // Active navigation highlighting
            const sections = document.querySelectorAll('section[id]');
            const navItems = document.querySelectorAll('.nav-link');
            
            function highlightNavigation() {
                let current = '';
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.clientHeight;
                    if (pageYOffset >= (sectionTop - 200)) {
                        current = section.getAttribute('id');
                    }
                });
                
                navItems.forEach(item => {
                    item.classList.remove('active');
                    if (item.getAttribute('href') === '#' + current) {
                        item.classList.add('active');
                    }
                });
            }
            
            window.addEventListener('scroll', highlightNavigation);
            
            // Mobile menu toggle
            const mobileToggle = document.querySelector('.mobile-menu-toggle');
            const navbar = document.querySelector('.navbar-nav');
            
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function() {
                    navbar.classList.toggle('show');
                });
            }
            
            // Animate statistics on scroll
            const statNumbers = document.querySelectorAll('.stat-number');
            const animateStats = () => {
                statNumbers.forEach(stat => {
                    const rect = stat.getBoundingClientRect();
                    if (rect.top < window.innerHeight && rect.bottom > 0) {
                        const finalValue = stat.textContent;
                        if (!stat.hasAttribute('data-animated')) {
                            stat.setAttribute('data-animated', 'true');
                            animateNumber(stat, finalValue);
                        }
                    }
                });
            };
            
            function animateNumber(element, finalValue) {
                const isPercentage = finalValue.includes('%');
                const numericValue = parseFloat(finalValue.replace(/[^0-9.]/g, ''));
                let current = 0;
                const increment = numericValue / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= numericValue) {
                        current = numericValue;
                        clearInterval(timer);
                    }
                    element.textContent = isPercentage ? 
                        current.toFixed(1) + '%' : 
                        Math.floor(current).toLocaleString();
                }, 30);
            }
            
            window.addEventListener('scroll', animateStats);
            animateStats(); // Check on load
        });
        
        // Helper functions for quick links
        function openUserManual() {
            alert('User Manual will be available in the next release. Please contact IT Support for assistance.');
        }
        
        function openTrainingSchedule() {
            alert('Training schedule feature coming soon. Contact your supervisor for current training information.');
        }
        
        // System status check (simulated)
        function checkSystemStatus() {
            // In a real implementation, this would make an AJAX call to check system health
            const statusElements = document.querySelectorAll('.status-indicator');
            statusElements.forEach(element => {
                // Simulate random status check
                const isOnline = Math.random() > 0.1; // 90% uptime simulation
                const dot = element.querySelector('.status-dot');
                const text = element.querySelector('span');
                
                if (isOnline) {
                    dot.className = 'status-dot online';
                    text.textContent = 'All Systems Operational';
                } else {
                    dot.className = 'status-dot offline';
                    text.textContent = 'System Maintenance';
                }
            });
        }
        
        // Check system status every 5 minutes
        setInterval(checkSystemStatus, 300000);
        
        // Initial status check
        checkSystemStatus();
    </script>
</body>
</html>