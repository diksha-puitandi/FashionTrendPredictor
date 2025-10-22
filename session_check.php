<?php
session_start();

// Prevent caching of pages with session data
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check if user is logged in with user_id validation
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    // Clear any existing session data
    $_SESSION = array();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Optional: Check session timeout (24 hours)
$session_timeout = 24 * 60 * 60; // 24 hours in seconds
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $session_timeout) {
    // Session expired
    $_SESSION = array();
    session_destroy();
    header("Location: login.php?expired=1");
    exit;
}
?>
