<?php
/**
 * Logout Page
 * Fishing Log Application
 */

require_once __DIR__ . '/config/config.php';
startSecureSession();

// Destroy session
$_SESSION = [];
session_destroy();

// Delete remember me cookie if exists
if (isset($_COOKIE['fishing_log_user'])) {
    setcookie('fishing_log_user', '', time() - 3600, '/');
}
setcookie('wongsibuk_csrf', '', time() - 3600, '/');

// Redirect to login page
header('Location: login.html');
exit();
?>
