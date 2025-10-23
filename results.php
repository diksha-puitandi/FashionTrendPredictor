<?php
require_once 'session_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediction Results - Fashion Predictor</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #f39c12;
            --text-color: #333;
            --light-bg: #ecf0f1;
            --white: #ffffff;
            --card-bg: #fff;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .navbar .logo a {
            text-decoration: none;
            color: inherit;
            transition: color 0.3s ease;
        }

        .navbar .logo a:hover {
            color: var(--secondary-color);
        }

        .nav-links {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
            align-items: center;
            gap: 10px;
        }

        .nav-links li {
            margin: 0;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--primary-color);
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.2s ease;
            display: block;
        }

        .nav-links a:hover {
            color: var(--secondary-color);
            background-color: #f8f9fa;
        }

        .nav-links a.active {
            color: var(--secondary-color);
            background-color: rgba(52, 152, 219, 0.1);
            border-bottom: 2px solid var(--secondary-color);
        }

        /* Dropdown Menu Styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown .dropbtn {
            background: none;
            border: none;
            color: var(--primary-color);
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.2s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
        }

        .dropdown .dropbtn:hover {
            color: var(--secondary-color);
            background-color: #f8f9fa;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: var(--white);
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            z-index: 1000;
            top: 100%;
            left: 0;
            margin-top: 5px;
            border: 1px solid #e0e0e0;
        }

        .dropdown-content a {
            color: var(--primary-color);
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: all 0.2s ease;
            border-radius: 0;
            margin: 0;
        }

        .dropdown-content a:hover {
            background-color: #f8f9fa;
            color: var(--secondary-color);
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown:hover .dropbtn {
            color: var(--secondary-color);
            background-color: #f8f9fa;
        }

        /* Results Container */
        .results-container {
            padding: 40px 5%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .results-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .results-header h1 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .results-header p {
            font-size: 1.1rem;
            color: #666;
        }

        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .result-card {
            background: var(--white);
            padding: 30px;
            border-radius: 15px;
            box-shadow: var(--box-shadow);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .result-card:hover {
            transform: translateY(-5px);
        }

        .result-card h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.3rem;
        }

        .result-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .prediction-value {
            color: var(--success-color);
        }

        .accuracy-value {
            color: var(--secondary-color);
        }

        .confidence-value {
            color: var(--accent-color);
        }

        .confidence-bar {
            width: 100%;
            height: 20px;
            background-color: #ecf0f1;
            border-radius: 10px;
            overflow: hidden;
            margin: 15px 0;
        }

        .confidence-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--danger-color), var(--warning-color), var(--success-color));
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .confidence-text {
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
        }

        .model-info {
            background: var(--white);
            padding: 30px;
            border-radius: 15px;
            box-shadow: var(--box-shadow);
            margin-bottom: 40px;
        }

        .model-info h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            text-align: center;
        }

        .model-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .model-detail {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: center;
        }

        .model-detail h4 {
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .model-detail p {
            color: #666;
            margin: 0;
        }

        .chart-container {
            background: var(--white);
            padding: 30px;
            border-radius: 15px;
            box-shadow: var(--box-shadow);
            margin-bottom: 40px;
        }

        .chart-container h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            text-align: center;
        }

        .chart-wrapper {
            position: relative;
            height: 400px;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }

        .btn-secondary {
            background: #f8f9fa;
            color: var(--primary-color);
            border: 2px solid #e0e0e0;
        }

        .btn-secondary:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dropdown-content {
                position: static;
                display: none;
                width: 100%;
                box-shadow: none;
                border: 1px solid #e0e0e0;
                margin-top: 10px;
            }
            
            .dropdown:hover .dropdown-content {
                display: block;
            }

            .results-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }

            .btn {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo"><a href="index.php">Fashion Predictor</a></div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="predict.php">Predict Trend</a></li>
                <li><a href="insights.php">Dataset Insights</a></li>
                <li class="dropdown">
                    <a href="#" class="dropbtn">More <i class="fas fa-caret-down"></i></a>
                    <div class="dropdown-content">
                        <a href="index.php#about">About</a>
                        <a href="index.php#blog">Blog</a>
                        <a href="index.php#contact">Contact</a>
                    </div>
                </li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="results-container">
            <div class="results-header">
                <h1>Prediction Results</h1>
                <p>Your fashion trend prediction is ready!</p>
            </div>

            <div class="results-grid">
                <div class="result-card">
                    <h3>Prediction Score</h3>
                    <div class="result-value prediction-value" id="predictionValue">0</div>
                    <p>Out of 5</p>
                </div>

                <div class="result-card">
                    <h3>Model Accuracy</h3>
                    <div class="result-value accuracy-value" id="accuracyValue">0%</div>
                    <p>Model Accuracy</p>
                </div>

                <div class="result-card">
                    <h3>Confidence Level</h3>
                    <div class="result-value confidence-value" id="confidenceValue">0%</div>
                    <p>Model Confidence</p>
                    
                    <div class="confidence-bar">
                        <div class="confidence-fill" id="confidenceBar" style="width: 0%"></div>
                    </div>
                    <div class="confidence-text" id="confidenceText">Confidence: 0%</div>
                </div>
            </div>

            <div class="model-info">
                <h2>Model Information</h2>
                <div class="model-details">
                    <div class="model-detail">
                        <h4>Model Type</h4>
                        <p id="modelType">Loading...</p>
                    </div>
                    <div class="model-detail">
                        <h4>Target Variable</h4>
                        <p id="category">Loading...</p>
                    </div>
                    <div class="model-detail">
                        <h4>Estimated Years</h4>
                        <p id="estimatedYears">Loading...</p>
                    </div>
                    <div class="model-detail">
                        <h4>R² Score (0-1)</h4>
                        <p id="r2Score">Loading...</p>
                    </div>
                    <div class="model-detail">
                        <h4>Data Size</h4>
                        <p id="dataSize">Loading...</p>
                    </div>
                    <div class="model-detail">
                        <h4>Features Count</h4>
                        <p id="featuresCount">Loading...</p>
                    </div>
                </div>
            </div>

            <div class="chart-container">
                <h2>Trend Analysis Chart</h2>
                <div class="chart-wrapper">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <div class="action-buttons">
                <a href="predict.php" class="btn btn-primary">
                    <i class="fas fa-redo"></i> Make Another Prediction
                </a>
                <a href="insights.php" class="btn btn-secondary">
                    <i class="fas fa-chart-bar"></i> View Dataset Insights
                </a>
                <a href="index.php" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Fashion Predictor. All rights reserved.</p>
        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
    </footer>

    <script>
        // Load data from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const prediction = localStorage.getItem('prediction') || '0';
            const accuracy = localStorage.getItem('accuracy') || '0';
            const confidence = localStorage.getItem('confidence') || '0';
            const category = localStorage.getItem('category') || 'Unknown';
            const estimatedYears = localStorage.getItem('estimatedYears') || 'Unknown';
            const modelType = localStorage.getItem('modelType') || 'Ensemble Model';
            const r2Score = localStorage.getItem('r2') || '0';
            const dataSize = localStorage.getItem('dataSize') || '115 samples';
            const featuresCount = localStorage.getItem('featuresCount') || '9 features';

            // Update display values
            document.getElementById('predictionValue').textContent = prediction;
            document.getElementById('accuracyValue').textContent = (parseFloat(accuracy) * 100).toFixed(1) + '%';
            document.getElementById('confidenceValue').textContent = (parseFloat(confidence) * 100).toFixed(1) + '%';
            document.getElementById('category').textContent = category;
            document.getElementById('estimatedYears').textContent = estimatedYears;
            document.getElementById('modelType').textContent = modelType;
            document.getElementById('r2Score').textContent = parseFloat(r2Score).toFixed(3);
            document.getElementById('dataSize').textContent = dataSize;
            document.getElementById('featuresCount').textContent = featuresCount;

            // Update confidence progress bar
            if (confidence) {
                const confValue = parseFloat(confidence) * 100;
                document.getElementById('confidenceBar').style.width = confValue + '%';
                document.getElementById('confidenceText').textContent = 'Confidence: ' + confValue.toFixed(1) + '%';
            }

            // Create trend chart
            createTrendChart(parseFloat(prediction));
        });

        function createTrendChart(prediction) {
            const ctx = document.getElementById('trendChart').getContext('2d');
            
            // Generate sample trend data
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const trendData = months.map((month, index) => {
                // Create a trend that peaks around the prediction value
                const baseValue = prediction * 0.7;
                const variation = Math.sin((index / 12) * Math.PI * 2) * 0.5;
                return Math.max(0, Math.min(5, baseValue + variation + Math.random() * 0.5));
            });

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Trend Popularity',
                        data: trendData,
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 5,
                            title: {
                                display: true,
                                text: 'Popularity Score'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Months'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Fashion Trend Popularity Over Time'
                        }
                    }
                }
            });
        }

        // Prevent back button issues and ensure proper logout
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Page was loaded from cache, reload to ensure fresh state
                window.location.reload();
            }
        });

        // Clear any cached data and prevent back navigation to logged-in state
        if (window.history && window.history.pushState) {
            window.history.pushState(null, null, window.location.href);
            window.addEventListener('popstate', function(event) {
                // If user tries to go back, redirect to login
                window.location.href = 'login.php';
            });
        }
    </script>
</body>
</html>
