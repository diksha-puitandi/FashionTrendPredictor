<?php
session_start();

// ✅ Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "fashion_db";

$conn = new mysqli($host, $user, $pass, $db);

// ✅ Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $message = "All fields are required!";
    } else {
        // ✅ Check if email already exists
        $stmt = $conn->prepare("SELECT user_id FROM user WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Email already registered!";
        } else {
            // ✅ Hash password and insert user
            #$hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $conn->insert_id; // Get the auto-generated user_id
                $_SESSION['login_time'] = time(); // Set login time for session timeout
                header("Location: index.php");
                exit;
            } else {
                $message = "Registration failed. Please try again!";
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Fashion Forecaster</title>
    <link rel="stylesheet" href="styles.css">
<style>
/* ==========================
   Registration / Login Forms
   ========================== */
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

.form-container {
    padding: 60px 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 120px);
    background: linear-gradient(135deg, var(--white), var(--light-bg));
}

.form-card {
    background-color: var(--card-bg);
    padding: 40px;
    border-radius: 15px;
    box-shadow: var(--box-shadow);
    max-width: 400px;
    width: 100%;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.form-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
}

.form-card h1 {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 20px;
    font-weight: 700;
}

.form-card p {
    font-size: 0.95rem;
    margin-top: 15px;
    color: #555;
}

/* Input Fields */
.form-card .form-group {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 20px;
    width: 100%;
}

.form-card input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    box-sizing: border-box;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-card input:focus {
    outline: none;
    border-color: var(--secondary-color);
    box-shadow: 0 0 6px rgba(52, 152, 219, 0.3);
}

/* Button */
.form-card button {
    width: 100%;
    padding: 14px;
    font-size: 1.1rem;
    border: none;
    border-radius: 50px;
    background-color: var(--secondary-color);
    color: var(--white);
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.form-card button:hover {
    background-color: #2980b9;
    transform: translateY(-2px);
}

/* Links */
.form-card a {
    color: var(--secondary-color);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.form-card a:hover {
    color: var(--accent-color);
}

/* Error message */
.form-card .error-message {
    color: red;
    margin-bottom: 15px;
    font-weight: 500;
}
</style>
</head>
<body>

<div class="form-container">
    <div class="form-card">
        <h1>Register</h1>
        <?php if ($message != ''): ?>
            <p class="error-message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Name" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="cta-button">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>

</body>
</html>
