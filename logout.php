<?php
/**
 * Logout Page
 * Fishing Log Application
 */

// Start session
session_start();

// Destroy session
session_destroy();

// Delete remember me cookie if exists
if (isset($_COOKIE['fishing_log_user'])) {
    setcookie('fishing_log_user', '', time() - 3600, '/');
}

// Redirect to login page
header('Location: login.html');
exit();
?>
