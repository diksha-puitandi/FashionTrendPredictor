<?php
require_once 'session_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dataset Insights - Fashion Predictor</title>
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

        /* Insights Container */
        .insights-container {
            padding: 40px 5%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .insights-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .insights-header h1 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .insights-header p {
            font-size: 1.1rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-bottom: 50px;
        }

        .chart-card {
            background: var(--white);
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .chart-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .chart-card h3 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 1.3rem;
            text-align: center;
        }

        .chart-wrapper {
            position: relative;
            height: 300px;
            width: 100%;
        }

        /* Stats Section */
        .stats-section {
            background: var(--white);
            padding: 40px;
            border-radius: 15px;
            box-shadow: var(--box-shadow);
            margin-bottom: 40px;
        }

        .stats-section h2 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
        }

        .stat-item {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-3px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
            font-size: 1rem;
            font-weight: 600;
        }

        /* Footer */
        footer {
            background-color: var(--primary-color);
            color: var(--white);
            text-align: center;
            padding: 30px 0;
            margin-top: 50px;
        }

        .social-icons {
            margin-top: 15px;
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

            .charts-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .chart-wrapper {
                height: 250px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
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
                <li><a href="insights.php" class="active">Dataset Insights</a></li>
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
        <div class="insights-container">
            <div class="insights-header">
                <h1>Dataset Insights & Analytics</h1>
                <p>Comprehensive analysis of our fashion trend prediction dataset with interactive visualizations and statistical insights.</p>
            </div>

            <!-- Charts Grid - 3 rows, 2 columns each -->
            <div class="charts-grid">
                <!-- Row 1 -->
                <div class="chart-card">
                    <h3>Age Group Distribution</h3>
                    <div class="chart-wrapper">
                        <canvas id="ageChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <h3>Season & Weather Preferences</h3>
                    <div class="chart-wrapper">
                        <canvas id="seasonChart"></canvas>
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="chart-card">
                    <h3>Target Audience Breakdown</h3>
                    <div class="chart-wrapper">
                        <canvas id="audienceChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <h3>Material & Fabric Types</h3>
                    <div class="chart-wrapper">
                        <canvas id="materialChart"></canvas>
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="chart-card">
                    <h3>Cultural Trend Influences</h3>
                    <div class="chart-wrapper">
                        <canvas id="culturalChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <h3>Popularity Score Distribution</h3>
                    <div class="chart-wrapper">
                        <canvas id="popularityChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Statistics Section -->
            <div class="stats-section">
                <h2>Dataset Statistics</h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number">115</div>
                        <div class="stat-label">Total Records</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">9</div>
                        <div class="stat-label">Key Features</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">80%</div>
                        <div class="stat-label">Model Accuracy</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">4</div>
                        <div class="stat-label">Age Categories</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">5</div>
                        <div class="stat-label">Season Types</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">6</div>
                        <div class="stat-label">Material Types</div>
                    </div>
                </div>
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
        // Chart.js configuration
        Chart.defaults.font.family = 'Poppins, sans-serif';
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#666';

        // Color palette for charts
        const colors = {
            primary: '#3498db',
            secondary: '#2c3e50',
            accent: '#f39c12',
            success: '#27ae60',
            warning: '#f39c12',
            danger: '#e74c3c',
            info: '#17a2b8',
            light: '#6c757d'
        };

        // Chart 1: Age Group Distribution (Pie Chart)
        const ageCtx = document.getElementById('ageChart').getContext('2d');
        new Chart(ageCtx, {
            type: 'pie',
            data: {
                labels: ['18 to 25', '25 to 30', '31 and above', 'Under 18'],
                datasets: [{
                    data: [45, 32, 28, 10],
                    backgroundColor: [
                        colors.primary,
                        colors.accent,
                        colors.success,
                        colors.warning
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    title: {
                        display: false
                    }
                }
            }
        });

        // Chart 2: Season & Weather Preferences (Bar Chart)
        const seasonCtx = document.getElementById('seasonChart').getContext('2d');
        new Chart(seasonCtx, {
            type: 'bar',
            data: {
                labels: ['Winter & Cold', 'All Seasons', 'Summer & Hot', 'Spring & Warm', 'Fall & Mild'],
                datasets: [{
                    label: 'Number of Responses',
                    data: [38, 25, 22, 18, 12],
                    backgroundColor: [
                        colors.primary,
                        colors.accent,
                        colors.success,
                        colors.warning,
                        colors.danger
                    ],
                    borderColor: [
                        colors.secondary,
                        colors.secondary,
                        colors.secondary,
                        colors.secondary,
                        colors.secondary
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        }
                    }
                }
            }
        });

        // Chart 3: Target Audience Breakdown (Doughnut Chart)
        const audienceCtx = document.getElementById('audienceChart').getContext('2d');
        new Chart(audienceCtx, {
            type: 'doughnut',
            data: {
                labels: ['Women', 'Men', 'Unisex', 'Kids'],
                datasets: [{
                    data: [48, 35, 25, 7],
                    backgroundColor: [
                        colors.primary,
                        colors.accent,
                        colors.success,
                        colors.warning
                    ],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        // Chart 4: Material & Fabric Types (Horizontal Bar Chart)
        const materialCtx = document.getElementById('materialChart').getContext('2d');
        new Chart(materialCtx, {
            type: 'bar',
            data: {
                labels: ['Cotton', 'Denim', 'Leather', 'Synthetic', 'Silk', 'Others'],
                datasets: [{
                    label: 'Popularity Count',
                    data: [42, 28, 20, 15, 8, 2],
                    backgroundColor: [
                        colors.primary,
                        colors.accent,
                        colors.success,
                        colors.warning,
                        colors.danger,
                        colors.info
                    ],
                    borderColor: colors.secondary,
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        }
                    }
                }
            }
        });

        // Chart 5: Cultural Trend Influences (Polar Area Chart)
        const culturalCtx = document.getElementById('culturalChart').getContext('2d');
        new Chart(culturalCtx, {
            type: 'polarArea',
            data: {
                labels: ['Vintage', 'K-pop', 'Streetwear', 'Minimalist', 'Boho', 'Y2K', 'Other'],
                datasets: [{
                    data: [35, 28, 22, 18, 15, 12, 8],
                    backgroundColor: [
                        colors.primary,
                        colors.accent,
                        colors.success,
                        colors.warning,
                        colors.danger,
                        colors.info,
                        colors.light
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        }
                    }
                }
            }
        });

        // Chart 6: Popularity Score Distribution (Line Chart)
        const popularityCtx = document.getElementById('popularityChart').getContext('2d');
        new Chart(popularityCtx, {
            type: 'line',
            data: {
                labels: ['1', '2', '3', '4', '5'],
                datasets: [{
                    label: 'Number of Responses',
                    data: [15, 28, 35, 25, 12],
                    borderColor: colors.primary,
                    backgroundColor: colors.primary + '20',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: colors.primary,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Popularity Score (1-5)'
                        }
                    }
                }
            }
        });

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
