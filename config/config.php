<?php
$servername = "localhost";   // or 127.0.0.1
$username   = "root";        // default for XAMPP
$password   = "";            // default is empty
$database   = "your_database_name";  // change to your DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
