<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Prevent caching of pages with session data
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Get user information
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
$login_time = isset($_SESSION['login_time']) ? $_SESSION['login_time'] : time();

// Calculate session duration
$session_duration = time() - $login_time;
$hours = floor($session_duration / 3600);
$minutes = floor(($session_duration % 3600) / 60);

// Get user's name from database
$user_name = '';
try {
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "fashion_db";
    
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Check if name column exists, if not create it
    $checkColumn = $conn->query("SHOW COLUMNS FROM users LIKE 'name'");
    if ($checkColumn->num_rows === 0) {
        $conn->query("ALTER TABLE users ADD COLUMN name VARCHAR(50) DEFAULT ''");
    }
    
    $stmt = $conn->prepare("SELECT name FROM users WHERE user_id = ? AND username = ?");
    $stmt->bind_param("is", $user_id, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $user_name = $user_data['name'] ?: $username; // Use name if available, otherwise username
    } else {
        $user_name = $username;
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    $user_name = $username; // Fallback to username
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Fashion Predictor</title>
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

        /* Profile Container */
        .profile-container {
            padding: 40px 5%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .profile-header h1 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .profile-header p {
            font-size: 1.1rem;
            color: #666;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        .profile-card {
            background: var(--white);
            padding: 30px;
            border-radius: 15px;
            box-shadow: var(--box-shadow);
        }

        .profile-card h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .user-info {
            text-align: center;
        }

        .user-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 3rem;
            color: white;
        }

        .user-details {
            margin-bottom: 20px;
        }

        .user-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .user-detail:last-child {
            border-bottom: none;
        }

        .user-detail label {
            font-weight: 600;
            color: var(--primary-color);
        }

        .user-detail span {
            color: #666;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .prediction-history {
            margin-top: 20px;
        }

        .history-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .history-item:last-child {
            margin-bottom: 0;
        }

        .history-details {
            flex: 1;
        }

        .history-date {
            color: #666;
            font-size: 0.9rem;
        }

        .history-prediction {
            font-weight: 600;
            color: var(--primary-color);
        }

        .history-score {
            background: var(--secondary-color);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: 600;
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

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        /* Edit Profile Form Styles */
        .edit-form {
            max-width: 500px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
            font-weight: 600;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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

            .profile-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
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
                <li><a href="profile.php" class="active">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="profile-container">
            <div class="profile-header">
                <h1>User Profile</h1>
                <p>Manage your account and view your prediction history</p>
            </div>

            <div class="profile-grid">
                <!-- User Information Card -->
                <div class="profile-card user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2>Account Information</h2>
                    <div class="user-details">
                        <div class="user-detail">
                            <label>Name:</label>
                            <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
                        </div>
                        <div class="user-detail">
                            <label>Username:</label>
                            <span><?php echo htmlspecialchars($username); ?></span>
                        </div>
                        <div class="user-detail">
                            <label>User ID:</label>
                            <span><?php echo $user_id; ?></span>
                        </div>
                        <div class="user-detail">
                            <label>Session Duration:</label>
                            <span><?php echo $hours . 'h ' . $minutes . 'm'; ?></span>
                        </div>
                        <div class="user-detail">
                            <label>Member Since:</label>
                            <span><?php echo date('M Y', $login_time); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="profile-card">
                    <h2>Your Statistics</h2>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-value" id="totalPredictions">0</div>
                            <div class="stat-label">Total Predictions</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" id="avgAccuracy">0%</div>
                            <div class="stat-label">Avg. Accuracy</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" id="bestScore">0</div>
                            <div class="stat-label">Best Score</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value" id="thisMonth">0</div>
                            <div class="stat-label">This Month</div>
                        </div>
                    </div>

                    <div class="prediction-history">
                        <h3>Recent Predictions</h3>
                        <div class="history-item">
                            <div class="history-details">
                                <div class="history-prediction">Winter Fashion Trend</div>
                                <div class="history-date"><?php echo date('M d, Y H:i'); ?></div>
                            </div>
                            <div class="history-score">4/5</div>
                        </div>
                        <div class="history-item">
                            <div class="history-details">
                                <div class="history-prediction">Summer Collection</div>
                                <div class="history-date"><?php echo date('M d, Y H:i', time() - 3600); ?></div>
                            </div>
                            <div class="history-score">3/5</div>
                        </div>
                        <div class="history-item">
                            <div class="history-details">
                                <div class="history-prediction">Streetwear Style</div>
                                <div class="history-date"><?php echo date('M d, Y H:i', time() - 7200); ?></div>
                            </div>
                            <div class="history-score">5/5</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Section -->
            <div class="profile-card">
                <h2>Edit Profile</h2>
                <form id="editProfileForm" class="edit-form">
                    <div class="form-group">
                        <label for="newName">Full Name:</label>
                        <input type="text" id="newName" name="newName" value="<?php echo htmlspecialchars($user_name); ?>" placeholder="Enter your full name" required>
                    </div>
                    <div class="form-group">
                        <label for="currentPassword">Current Password:</label>
                        <input type="password" id="currentPassword" name="currentPassword" placeholder="Enter current password" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="cancelEdit()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
                <div id="updateMessage" class="message" style="display: none;"></div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="predict.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Make New Prediction
                </a>
                <a href="insights.php" class="btn btn-secondary">
                    <i class="fas fa-chart-bar"></i> View Insights
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <a href="logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
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
        // Load user statistics from localStorage or set defaults
        document.addEventListener('DOMContentLoaded', function() {
            // Get prediction data from localStorage
            const predictions = JSON.parse(localStorage.getItem('predictions') || '[]');
            const totalPredictions = predictions.length || 3; // Default to 3 for demo
            
            // Calculate statistics
            const avgAccuracy = predictions.length > 0 
                ? (predictions.reduce((sum, p) => sum + (p.accuracy || 0.75), 0) / predictions.length * 100).toFixed(1)
                : 75.0;
            
            const bestScore = predictions.length > 0 
                ? Math.max(...predictions.map(p => p.prediction || 3))
                : 5;
            
            const thisMonth = predictions.filter(p => {
                const predDate = new Date(p.date || Date.now());
                const now = new Date();
                return predDate.getMonth() === now.getMonth() && predDate.getFullYear() === now.getFullYear();
            }).length || 2;

            // Update display
            document.getElementById('totalPredictions').textContent = totalPredictions;
            document.getElementById('avgAccuracy').textContent = avgAccuracy + '%';
            document.getElementById('bestScore').textContent = bestScore + '/5';
            document.getElementById('thisMonth').textContent = thisMonth;

            // Set up edit profile form
            setupEditProfileForm();
        });

        function setupEditProfileForm() {
            const form = document.getElementById('editProfileForm');
            const messageDiv = document.getElementById('updateMessage');

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                const newName = formData.get('newName').trim();
                const currentPassword = formData.get('currentPassword');

                // Validate inputs
                if (!newName) {
                    showMessage('Please enter a name.', 'error');
                    return;
                }

                if (!currentPassword) {
                    showMessage('Please enter your current password.', 'error');
                    return;
                }

                // Submit form
                updateProfile(newName, currentPassword);
            });
        }

        function updateProfile(newName, currentPassword) {
            const formData = new FormData();
            formData.append('newName', newName);
            formData.append('currentPassword', currentPassword);

            fetch('update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Profile updated successfully!', 'success');
                    // Clear form
                    document.getElementById('editProfileForm').reset();
                    // Update displayed name if needed
                    updateDisplayedName(newName);
                } else {
                    showMessage(data.error || 'Failed to update profile.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while updating profile.', 'error');
            });
        }

        function showMessage(message, type) {
            const messageDiv = document.getElementById('updateMessage');
            messageDiv.textContent = message;
            messageDiv.className = `message ${type}`;
            messageDiv.style.display = 'block';
            
            // Hide message after 5 seconds
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 5000);
        }

        function updateDisplayedName(newName) {
            // Update any displayed name elements if they exist
            const nameElements = document.querySelectorAll('.user-name');
            nameElements.forEach(element => {
                element.textContent = newName;
            });
        }

        function cancelEdit() {
            document.getElementById('editProfileForm').reset();
            document.getElementById('updateMessage').style.display = 'none';
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
