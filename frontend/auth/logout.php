
<?php
// auth/logout.php - Fixed Logout System
session_start();

// Log the logout activity
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $role = $_SESSION['user_role'] ?? 'unknown';
    error_log("User logout: {$username} ({$role}) at " . date('Y-m-d H:i:s'));
}

// Clear all session variables
$_SESSION = array();

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Clear remember me cookie
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Clear any output buffering
if (ob_get_level()) {
    ob_end_clean();
}

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login page
header("Location: login.php");
exit();
?>