<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

// Set content type to JSON
header('Content-Type: application/json');

// Get form data
$newName = trim($_POST['newName'] ?? '');
$currentPassword = $_POST['currentPassword'] ?? '';
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Validate inputs
if (empty($newName)) {
    echo json_encode(["success" => false, "error" => "Name is required"]);
    exit;
}

if (empty($currentPassword)) {
    echo json_encode(["success" => false, "error" => "Current password is required"]);
    exit;
}

// Validate name length
if (strlen($newName) < 2 || strlen($newName) > 50) {
    echo json_encode(["success" => false, "error" => "Name must be between 2 and 50 characters"]);
    exit;
}

// Database connection
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "fashion_db";

try {
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // First, verify the current password
    $stmt = $conn->prepare("SELECT password FROM user WHERE user_id = ? AND username = ?");
    $stmt->bind_param("is", $user_id, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("User not found");
    }
    
    $user = $result->fetch_assoc();
    
    // Verify password (assuming passwords are stored as plain text for this demo)
    // In production, you should use password_verify() with hashed passwords
    if ($currentPassword !== $user['password']) {
        echo json_encode(["success" => false, "error" => "Current password is incorrect"]);
        exit;
    }
    
    // Update the user's name
    // First, check if we need to add a 'name' column to the user table
    $checkColumn = $conn->query("SHOW COLUMNS FROM user LIKE 'name'");
    if ($checkColumn->num_rows === 0) {
        // Add name column if it doesn't exist
        $conn->query("ALTER TABLE user ADD COLUMN name VARCHAR(50) DEFAULT ''");
    }
    
    $updateStmt = $conn->prepare("UPDATE user SET name = ? WHERE user_id = ? AND username = ?");
    $updateStmt->bind_param("sis", $newName, $user_id, $username);
    
    if ($updateStmt->execute()) {
        if ($updateStmt->affected_rows > 0) {
            // Update successful
            echo json_encode([
                "success" => true, 
                "message" => "Profile updated successfully",
                "newName" => $newName
            ]);
        } else {
            echo json_encode(["success" => false, "error" => "No changes made to profile"]);
        }
    } else {
        throw new Exception("Failed to update profile: " . $updateStmt->error);
    }
    
    $updateStmt->close();
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
