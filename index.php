<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashion Trend Prediction</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
    --primary-color: #2c3e50; /* A dark, professional blue */
    --secondary-color: #3498db; /* A vibrant, eye-catching blue */
    --accent-color: #f39c12; /* A warm orange for contrast */
    --text-color: #333;
    --light-bg: #ecf0f1;
    --white: #ffffff;
    --card-bg: #fff;
    --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background-color: var(--light-bg);
    color: var(--text-color);
}

/* Header and Navigation */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, var(--white) 0%, #f8f9fa 100%);
    padding: 1rem 5%;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 1000;
    backdrop-filter: blur(10px);
}

.navbar .logo {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--primary-color);
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.nav-links {
    list-style: none;
    display: flex;
    margin: 0;
    padding: 0;
    align-items: center;
}

.nav-links li {
    margin-left: 30px;
    position: relative;
}

.nav-links a {
    text-decoration: none;
    color: var(--primary-color);
    font-weight: 600;
    padding: 8px 16px;
    border-radius: 25px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.nav-links a::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(52, 152, 219, 0.1), transparent);
    transition: left 0.5s;
}

.nav-links a:hover::before {
    left: 100%;
}

.nav-links a:hover,
.nav-links a.active {
    color: var(--secondary-color);
    background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(52, 152, 219, 0.05));
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.2);
}

/* Dropdown Menu Styles */
.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: var(--white);
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 100;
    top: 100%;
    right: 0;
    border-radius: 5px;
}

.dropdown-content a {
    color: var(--primary-color);
    padding: 12px 16px;
    text-decoration: none;
    display: block; 
    font-weight: 500;
}

.dropdown-content a:hover {
    background-color: var(--light-bg);
}

.dropdown:hover .dropdown-content {
    display: block;
}

/* Hero Section */
.hero-section {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 100px 5% 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    gap: 60px;
    min-height: 80vh;
}

.hero-content {
    max-width: 600px;
    text-align: left;
    z-index: 2;
}

.hero-content h1 {
    font-size: 3.5rem;
    font-weight: 700;
    margin: 0 0 20px;
    line-height: 1.2;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.hero-subtitle {
    font-size: 1.3rem;
    line-height: 1.7;
    margin-bottom: 40px;
    opacity: 0.95;
}

.hero-stats {
    display: flex;
    gap: 40px;
    margin-bottom: 40px;
    flex-wrap: wrap;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--accent-color);
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.cta-button {
    display: inline-block;
    background: linear-gradient(135deg, var(--accent-color), #e67e22);
    color: var(--white);
    padding: 15px 35px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(243, 156, 18, 0.4);
    border: none;
    cursor: pointer;
}

.cta-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(243, 156, 18, 0.6);
    background: linear-gradient(135deg, #e67e22, var(--accent-color));
}

.hero-image-container {
    width: 500px;
    height: 400px;
    position: relative;
    z-index: 1;
}

.fashion-trend-visual {
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.trend-chart-placeholder {
    text-align: center;
    margin-bottom: 30px;
}

.trend-chart-placeholder i {
    font-size: 4rem;
    color: var(--accent-color);
    margin-bottom: 15px;
}

.trend-chart-placeholder h3 {
    font-size: 1.5rem;
    margin: 0;
    color: white;
}

.fashion-elements {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    justify-content: center;
}

.fashion-item {
    font-size: 2rem;
    padding: 15px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: float 3s ease-in-out infinite;
}

.fashion-item.item-1 { animation-delay: 0s; }
.fashion-item.item-2 { animation-delay: 0.5s; }
.fashion-item.item-3 { animation-delay: 1s; }
.fashion-item.item-4 { animation-delay: 1.5s; }

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

/* Container */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* Section Titles */
.section-title {
    font-size: 2.5rem;
    text-align: center;
    color: var(--primary-color);
    margin-bottom: 50px;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
    border-radius: 2px;
}

/* Analysis Info Section */
.analysis-info-section {
    padding: 80px 0;
    background: var(--white);
}

.analysis-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 40px;
    margin-top: 50px;
}

.analysis-card {
    text-align: center;
    padding: 40px 30px;
    background: var(--white);
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #f0f0f0;
}

.analysis-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.card-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
}

.card-icon i {
    font-size: 2rem;
    color: white;
}

.analysis-card h3 {
    font-size: 1.5rem;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.analysis-card p {
    color: #666;
    line-height: 1.6;
}

/* Features Section */
.features-section {
    padding: 80px 0;
    background: var(--light-bg);
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 50px;
}

.feature-card {
    background: var(--white);
    padding: 40px 30px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    text-align: center;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
    position: relative;
    overflow: hidden;
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(52, 152, 219, 0.05), transparent);
    transition: left 0.5s;
}

.feature-card:hover::before {
    left: 100%;
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.feature-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
}

.feature-icon i {
    font-size: 1.8rem;
    color: white;
}

.feature-card h3 {
    font-size: 1.4rem;
    color: var(--primary-color);
    margin-bottom: 15px;
    font-weight: 600;
}

.feature-card p {
    color: #666;
    line-height: 1.6;
    margin: 0;
}

/* CTA Section */
.cta-section {
    padding: 80px 0;
    background: linear-gradient(135deg, var(--primary-color), #34495e);
    color: white;
    text-align: center;
}

.cta-content h2 {
    font-size: 2.5rem;
    margin-bottom: 20px;
    font-weight: 700;
}

.cta-content p {
    font-size: 1.2rem;
    margin-bottom: 40px;
    opacity: 0.9;
}

.cta-buttons {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.cta-button.primary {
    background: linear-gradient(135deg, var(--accent-color), #e67e22);
    color: white;
    padding: 15px 35px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(243, 156, 18, 0.4);
}

.cta-button.primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(243, 156, 18, 0.6);
}

.cta-button.secondary {
    background: transparent;
    color: white;
    padding: 15px 35px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    border: 2px solid white;
}

.cta-button.secondary:hover {
    background: white;
    color: var(--primary-color);
    transform: translateY(-3px);
}

/* Footer */
footer {
    background-color: var(--primary-color);
    color: var(--white);
    text-align: center;
    padding: 2rem 0;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.social-icons a {
    color: var(--white);
    margin: 0 10px;
    font-size: 1.5rem;
    transition: color 0.3s ease;
}

.social-icons a:hover {
    color: var(--accent-color);
}

/* Form Page Styles (Predict & Contact) */
.form-container, .login-section {
    padding: 60px 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 120px);
}

.form-card, .login-card {
    background-color: var(--card-bg);
    padding: 40px;
    border-radius: 10px;
    box-shadow: var(--box-shadow);
    max-width: 600px;
    width: 100%;
    text-align: center;
}

.form-card h1, .login-card h1 {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.form-description, .login-description {
    font-size: 1rem;
    color: #666;
    margin-bottom: 30px;
}

.form-group {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 20px;
}

.form-group label {
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--text-color);
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    box-sizing: border-box;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--secondary-color);
}

.form-group .cta-button {
    width: 100%;
    margin-top: 10px;
    padding: 15px;
    font-size: 1.1rem;
    border: none;
    cursor: pointer;
}

/* About Page Styles */
.about-section {
    padding: 60px 5%;
    display: flex;
    justify-content: center;
}

.about-container {
    max-width: 900px;
    width: 100%;
}

.about-container h1 {
    font-size: 2.5rem;
    color: var(--primary-color);
    text-align: center;
    margin-bottom: 10px;
}

.about-description {
    text-align: center;
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 40px;
}

.about-content-card {
    background-color: var(--card-bg);
    padding: 40px;
    border-radius: 10px;
    box-shadow: var(--box-shadow);
    margin-bottom: 30px;
}

.about-content-card h2 {
    color: var(--primary-color);
    margin-top: 0;
    margin-bottom: 15px;
    border-bottom: 2px solid var(--secondary-color);
    padding-bottom: 5px;
    display: inline-block;
}

.about-content-card p {
    line-height: 1.7;
    color: var(--text-color);
}

.mission-vision {
    display: flex;
    gap: 30px;
    text-align: center;
}

.mission-vision .mission-card,
.mission-vision .vision-card {
    flex: 1;
    padding: 30px;
    background-color: var(--light-bg);
    border-radius: 8px;
}

.mission-vision i {
    color: var(--accent-color);
    margin-bottom: 15px;
}

.team-grid {
    display: flex;
    justify-content: center;
    gap: 40px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.team-member {
    text-align: center;
    width: 150px;
}

.member-photo-placeholder {
    width: 100px;
    height: 100px;
    background-color: #eee;
    border-radius: 50%;
    margin: 0 auto 10px;
    border: 3px solid var(--secondary-color);
}

.team-member h4 {
    margin: 0;
    font-size: 1.2rem;
    color: var(--primary-color);
}

.team-member p {
    margin: 5px 0 0;
    color: #888;
    font-size: 0.9rem;
}

.team-text {
    text-align: center;
    max-width: 700px;
    margin: 0 auto;
    font-style: italic;
}

/* Contact Page Styles */
.contact-section {
    padding: 60px 5%;
    display: flex;
    justify-content: center;
}

.contact-card {
    background-color: var(--card-bg);
    padding: 40px;
    border-radius: 10px;
    box-shadow: var(--box-shadow);
    max-width: 700px;
    width: 100%;
    text-align: center;
}

.contact-description {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 30px;
}

.contact-details {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 30px;
    border-bottom: 1px solid #eee;
    padding-bottom: 30px;
}

.contact-info-item {
    text-align: center;
    color: var(--primary-color);
}

.contact-info-item i {
    color: var(--secondary-color);
    margin-bottom: 10px;
}

.contact-info-item p {
    margin: 0;
    color: var(--text-color);
}

.contact-form .form-group {
    margin-bottom: 20px;
}

.contact-form .cta-button {
    width: 100%;
    margin-top: 10px;
    padding: 15px;
    font-size: 1.1rem;
    border: none;
    cursor: pointer;
}

/* Blog Page Styles */
.blog-section {
    padding: 60px 5%;
    display: flex;
    justify-content: center;
}

.blog-container {
    max-width: 1000px;
    width: 100%;
    text-align: center;
}

.blog-container h1 {
    font-size: 2.5rem;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.blog-description {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 40px;
}

.blog-posts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.blog-post-card {
    background-color: var(--card-bg);
    border-radius: 10px;
    box-shadow: var(--box-shadow);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.blog-post-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.post-image-placeholder {
    width: 100%;
    height: 200px;
    background-color: #eee;
    border-bottom: 1px solid #ddd;
}

.post-content {
    padding: 20px;
    text-align: left;
}

.post-content h3 {
    font-size: 1.4rem;
    color: var(--primary-color);
    margin-top: 0;
}

.post-content p {
    font-size: 0.95rem;
    color: #555;
    line-height: 1.6;
}

.read-more {
    display: inline-block;
    color: var(--secondary-color);
    text-decoration: none;
    font-weight: 600;
    margin-top: 10px;
    transition: color 0.3s ease;
}

.read-more:hover {
    color: var(--accent-color);
}

/* Log In Page Styles */
.login-card {
    max-width: 450px;
}

.form-links {
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
    font-size: 0.9rem;
}

.form-links a {
    color: var(--secondary-color);
    text-decoration: none;
    transition: color 0.3s ease;
}

.form-links a:hover {
    color: var(--accent-color);
}

/* Responsive Design */
@media (max-width: 768px) {
    .navbar {
        padding: 1rem 3%;
    }
    
    .nav-links {
        gap: 15px;
    }
    
    .nav-links li {
        margin-left: 15px;
    }
    
    .nav-links a {
        padding: 6px 12px;
        font-size: 0.9rem;
    }
    
    .hero-section {
        flex-direction: column;
        padding: 60px 3% 40px;
        text-align: center;
        gap: 40px;
    }
    
    .hero-content h1 {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .hero-stats {
        justify-content: center;
        gap: 20px;
    }
    
    .hero-image-container {
        width: 100%;
        max-width: 400px;
        height: 300px;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .analysis-grid,
    .features-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .cta-button.primary,
    .cta-button.secondary {
        width: 100%;
        max-width: 300px;
    }
}

@media (max-width: 480px) {
    .hero-content h1 {
        font-size: 2rem;
    }
    
    .hero-stats {
        flex-direction: column;
        gap: 15px;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .fashion-elements {
        gap: 10px;
    }
    
    .fashion-item {
        font-size: 1.5rem;
        padding: 10px;
    }
}
    </style>

</head>
<body>

    <header>
        <nav class="navbar">
            <div class="logo">Fashion Forecaster</div>
            <ul class="nav-links">
            <!-- <a href="log in.html">Log In</a> -->

                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="predict.html">Predict Trend</a></li>
                <li><a href="insights.html">Dataset Insights</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">More <i class="fas fa-caret-down"></i></a>
                    <div class="dropdown-content">
                        <a href="about.html">About</a>
                        <a href="contact.html">Contact</a>
                        <a href="blog.html">Blog</a>

                         <?php if (isset($_SESSION['username'])): ?>
        <!-- User is logged in -->
        <li><a href="logout.php">Logout</a></li>
    <?php else: ?>
        <!-- User is NOT logged in -->
        <li><a href="#" class="active" onclick="showForm('login')">Log In / Sign Up</a></li>
    <?php endif; ?>
                        <!-- <a href="login.html">Log In</a> -->
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <h1>Revolutionary Fashion Trend Analysis</h1>
                <p class="hero-subtitle">Harness the power of machine learning to predict fashion trends with unprecedented accuracy. Our advanced AI system analyzes multiple factors to forecast what will be popular in the fashion world.</p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">21.74%</span>
                        <span class="stat-label">Prediction Accuracy</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">9</span>
                        <span class="stat-label">Key Factors</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">115+</span>
                        <span class="stat-label">Data Points</span>
                    </div>
                </div>
                <a href="predict.html" class="cta-button">Start Predicting <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="hero-image-container">
                <div class="fashion-trend-visual">
                    <div class="trend-chart-placeholder">
                        <i class="fas fa-chart-line"></i>
                        <h3>Trend Analysis</h3>
                    </div>
                    <div class="fashion-elements">
                        <div class="fashion-item item-1">👗</div>
                        <div class="fashion-item item-2">👔</div>
                        <div class="fashion-item item-3">👕</div>
                        <div class="fashion-item item-4">👖</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Fashion Analysis Info Section -->
        <section class="analysis-info-section">
            <div class="container">
                <h2 class="section-title">How Our Fashion Trend Analysis Works</h2>
                <div class="analysis-grid">
                    <div class="analysis-card">
                        <div class="card-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h3>Machine Learning Models</h3>
                        <p>We use an ensemble of advanced algorithms including Random Forest, Gradient Boosting, SVM, and Logistic Regression to ensure accurate predictions.</p>
                    </div>
                    <div class="analysis-card">
                        <div class="card-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <h3>Comprehensive Data</h3>
                        <p>Our system analyzes age demographics, seasonal preferences, cultural influences, material choices, and celebrity endorsements to predict trends.</p>
                    </div>
                    <div class="analysis-card">
                        <div class="card-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3>Real-time Insights</h3>
                        <p>Get instant predictions with confidence scores and detailed analysis of why certain trends are predicted to be popular.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <div class="container">
                <h2 class="section-title">Why Choose Our Platform</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Data-Driven Insights</h3>
                        <p>Our predictions are powered by comprehensive analysis of market data, consumer behavior, and fashion industry trends.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-magic"></i>
                        </div>
                        <h3>Intuitive Interface</h3>
                        <p>A simple and elegant design makes it easy for anyone to get accurate trend predictions without technical knowledge.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h3>Stay Ahead</h3>
                        <p>Empower your decisions with knowledge of what's next in the ever-evolving world of fashion and style.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Target Audience Focus</h3>
                        <p>Get predictions tailored to specific demographics including age groups, gender preferences, and cultural influences.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-palette"></i>
                        </div>
                        <h3>Color & Pattern Analysis</h3>
                        <p>Advanced analysis of color trends, pattern preferences, and visual elements that drive fashion popularity.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>Celebrity Influence</h3>
                        <p>Factor in celebrity endorsements and influencer impact on fashion trends for more accurate predictions.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-content">
                    <h2>Ready to Predict the Next Big Fashion Trend?</h2>
                    <p>Join thousands of fashion enthusiasts, designers, and retailers who trust our AI-powered predictions.</p>
                    <div class="cta-buttons">
                        <a href="predict.html" class="cta-button primary">Start Predicting Now</a>
                        <a href="insights.html" class="cta-button secondary">View Dataset Insights</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Fashion Forecaster. All rights reserved.</p>
        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
    </footer>

</body>
</html>