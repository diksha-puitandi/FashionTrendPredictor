<?php
require_once 'session_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Predict Trend - Fashion Predictor</title>
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

        /* Form Styles */
        .form-container {
            padding: 60px 5%;
            display: flex;
            justify-content: center;
        }

        .form-card {
            background-color: var(--card-bg);
            padding: 40px;
            border-radius: 15px;
            box-shadow: var(--box-shadow);
            max-width: 600px;
            width: 100%;
        }

        .form-card h1 {
            font-size: 2.5rem;
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 10px;
        }

        .form-description {
            text-align: center;
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .form-group select,
        .form-group input[type="range"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        .form-group select:focus {
            outline: none;
            border-color: var(--secondary-color);
        }

        .range-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .range-value {
            font-weight: 600;
            color: var(--secondary-color);
            min-width: 30px;
            text-align: center;
        }

        .cta-button {
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            width: 100%;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }

        .cta-button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .cta-button.secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .cta-button.secondary:hover {
            background: linear-gradient(135deg, #5a6268 0%, #3d4449 100%);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
        }

        .form-buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-top: 30px;
        }

        .form-buttons .cta-button {
            width: auto;
            min-width: 150px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .form-buttons {
                flex-direction: column;
                gap: 10px;
            }
            
            .form-buttons .cta-button {
                width: 100%;
            }
            
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
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo"><a href="index.php">Fashion Predictor</a></div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="predict.php" class="active">Predict Trend</a></li>
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
        <section class="form-container">
            <div class="form-card">
                <h1>Predict Fashion Trend</h1>
                <p class="form-description">Fill in the details below to get an AI-powered prediction of fashion trend popularity.</p>

                <form id="predictionForm" class="login-form">
                    <div class="form-group">
                        <label for="age">Age:</label>
                        <select id="age" name="age" required>
                            <option value="">Select Age Group</option>
                            <option value="under_18">Under 18</option>
                            <option value="18_to_25">18 to 25</option>
                            <option value="25_to_30">25 to 30</option>
                            <option value="31_and_above">31 and above</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="season_weather">Season & Weather Suitability:</label>
                        <select id="season_weather" name="season_weather" required>
                            <option value="">Select Season & Weather</option>
                            <option value="spring_warm">Spring and Warm</option>
                            <option value="summer_hot">Summer and Hot</option>
                            <option value="fall_mild">Fall and Mild</option>
                            <option value="winter_cold">Winter and Cold</option>
                            <option value="all_seasons">All Seasons</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="target_audience">Target Audience:</label>
                        <select id="target_audience" name="target_audience" required>
                            <option value="">Select Target Audience</option>
                            <option value="men">Men</option>
                            <option value="women">Women</option>
                            <option value="unisex">Unisex</option>
                            <option value="kids">Kids</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category_fit">Category & Fit:</label>
                        <select id="category_fit" name="category_fit" required>
                            <option value="">Select Category & Fit</option>
                            <option value="casual">Casual</option>
                            <option value="formal">Formal</option>
                            <option value="sportswear">Sportswear</option>
                            <option value="streetwear">Streetwear</option>
                            <option value="vintage">Vintage</option>
                            <option value="designer">Designer</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="material_fabric">Material / Fabric Type:</label>
                        <select id="material_fabric" name="material_fabric" required>
                            <option value="">Select Material / Fabric</option>
                            <option value="cotton">Cotton</option>
                            <option value="denim">Denim</option>
                            <option value="silk">Silk</option>
                            <option value="leather">Leather</option>
                            <option value="synthetic">Synthetic</option>
                            <option value="others">Others</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="cultural_trend">Cultural or Trend Influence:</label>
                        <select id="cultural_trend" name="cultural_trend" required>
                            <option value="">Select Cultural Influence</option>
                            <option value="western">Western</option>
                            <option value="eastern">Eastern</option>
                            <option value="african">African</option>
                            <option value="latin">Latin</option>
                            <option value="global">Global</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="color_pattern">Color & Pattern Type:</label>
                        <select id="color_pattern" name="color_pattern" required>
                            <option value="">Select Color & Pattern</option>
                            <option value="solid">Solid</option>
                            <option value="striped">Striped</option>
                            <option value="polka_dot">Polka Dot</option>
                            <option value="floral">Floral</option>
                            <option value="geometric">Geometric</option>
                            <option value="abstract">Abstract</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="boldness">Boldness & Emotional Impact (1-10):</label>
                        <div class="range-container">
                            <input type="range" id="boldness" name="boldness" min="1" max="10" value="5" required>
                            <span class="range-value" id="boldnessValue">5</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="celebrity_promotion">Was it Promoted by Celebrity or Influencer?</label>
                        <select id="celebrity_promotion" name="celebrity_promotion" required>
                            <option value="">Select Option</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="first_seen">Where Did You First See This?</label>
                        <select id="first_seen" name="first_seen" required>
                            <option value="">Select Option</option>
                            <option value="social_media">Social Media</option>
                            <option value="magazine">Magazine</option>
                            <option value="tv">TV</option>
                            <option value="store">Store</option>
                            <option value="friend">Friend</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="cta-button" id="predictBtn">Predict Trend</button>
                        <button type="button" class="cta-button secondary" id="resetBtn">Reset Form</button>
                    </div>
                </form>
            </div>
        </section>
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
        // Update range value display
        document.getElementById('boldness').addEventListener('input', function() {
            document.getElementById('boldnessValue').textContent = this.value;
        });

        // Reset button functionality
        document.getElementById('resetBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to reset the form? All entered data will be lost.')) {
                document.getElementById('predictionForm').reset();
                // Reset any visual feedback
                const predictBtn = document.getElementById('predictBtn');
                predictBtn.disabled = false;
                predictBtn.textContent = 'Predict Trend';
            }
        });

        // Form submission
        document.getElementById('predictionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const predictBtn = document.getElementById('predictBtn');
            
            // Disable button and show loading
            predictBtn.disabled = true;
            predictBtn.textContent = 'Predicting...';
            
            fetch('predict_trend.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Store prediction results in localStorage and navigate to results page
                    const modelInfo = data.model_info || {};
                    const trainAccuracy = modelInfo.train_accuracy || 0;
                    const testAccuracy = modelInfo.test_accuracy || 0;
                    const overfittingGap = modelInfo.overfitting_gap || 0;
                    const r2Score = modelInfo.r2_score || 0;
                    const noLeakage = modelInfo.no_leakage || false;
                    const prediction = data.prediction || 0;
                    const category = data.category || 'Unknown';
                    const estimatedYears = data.estimated_years || 'Unknown';
                    const confidence = data.confidence || 0;

                    // Store results in localStorage
                    localStorage.setItem('prediction', prediction);
                    localStorage.setItem('accuracy', testAccuracy);
                    localStorage.setItem('r2', r2Score);
                    localStorage.setItem('confidence', confidence);
                    localStorage.setItem('category', category);
                    localStorage.setItem('estimatedYears', estimatedYears);
                    localStorage.setItem('modelType', modelInfo.model_name || 'Ensemble Model');
                    localStorage.setItem('dataSize', '115 samples');
                    localStorage.setItem('featuresCount', '9 features');

                    // Navigate to results page
                    window.location.href = 'results.php';
                } else {
                    alert('Prediction failed: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while making the prediction: ' + error.message);
            })
            .finally(() => {
                // Re-enable button
                predictBtn.disabled = false;
                predictBtn.textContent = 'Predict Trend';
            });
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