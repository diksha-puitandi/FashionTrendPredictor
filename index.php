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
    background-color: var(--white);
    padding: 1rem 5%;
    box-shadow: var(--box-shadow);
}

.navbar .logo {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
}

.nav-links {
    list-style: none;
    display: flex;
    margin: 0;
    padding: 0;
}

.nav-links li {
    margin-left: 20px;
}

.nav-links a {
    text-decoration: none;
    color: var(--primary-color);
    font-weight: 600;
    transition: color 0.3s ease;
}

.nav-links a:hover,
.nav-links a.active {
    color: var(--secondary-color);
    border-bottom: 2px solid var(--secondary-color);
    padding-bottom: 5px;
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
    padding: 80px 5%;
    background: linear-gradient(135deg, var(--white), var(--light-bg));
    gap: 40px;
}

.hero-content {
    max-width: 600px;
    text-align: left;
}

.hero-content h1 {
    font-size: 3rem;
    color: var(--primary-color);
    margin: 0 0 20px;
}

.hero-content p {
    font-size: 1.2rem;
    line-height: 1.6;
    margin-bottom: 30px;
}

.cta-button {
    display: inline-block;
    background-color: var(--secondary-color);
    color: var(--white);
    padding: 12px 25px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: transform 0.3s ease, background-color 0.3s ease;
}

.cta-button:hover {
    background-color: #2980b9;
    transform: translateY(-3px);
}

.hero-image-container {
    width: 400px;
    height: 300px;
    background-color: #ccc;
    border-radius: 10px;
}

/* Features Section */
.features-section {
    display: flex;
    justify-content: center;
    padding: 60px 5%;
    gap: 30px;
    flex-wrap: wrap;
    text-align: center;
}

.feature-card {
    background-color: var(--white);
    padding: 30px;
    border-radius: 10px;
    box-shadow: var(--box-shadow);
    width: 300px;
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-10px);
}

.feature-card i {
    color: var(--accent-color);
    margin-bottom: 20px;
}

.feature-card h2 {
    font-size: 1.5rem;
    margin-top: 0;
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
</style>
</head>
<body>

    <header>
        <nav class="navbar">
            <div class="logo">Fashion Forecaster</div>
            <ul class="nav-links">
            <!-- <a href="log in.html">Log In</a> -->

                <li><a href="index.html" class="active">Home</a></li>
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
        <section class="hero-section">
            <div class="hero-content">
                <h1>Predict the Future of Fashion</h1>
                <p>Use our intelligent system to predict fashion trends based on real-time data and key factors. Stay ahead of the curve and make smarter style choices.</p>
                <a href="predict.html" class="cta-button">Start Predicting <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="hero-image-container">
                </div>
        </section>

        <section class="features-section">
            <div class="feature-card">
                <i class="fas fa-chart-line fa-3x"></i>
                <h2>Data-Driven Insights</h2>
                <p>Our predictions are powered by comprehensive analysis of market data and consumer behavior.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-magic fa-3x"></i>
                <h2>Intuitive Interface</h2>
                <p>A simple and elegant design makes it easy for anyone to get accurate trend predictions.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-lightbulb fa-3x"></i>
                <h2>Stay Ahead</h2>
                <p>Empower your decisions with knowledge of what's next in the ever-evolving world of fashion.</p>
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