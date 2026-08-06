<?php
/**
 * Entry Point - Fishing Log Application
 * Redirects to login page
 */

require_once __DIR__ . '/config/config.php';
startSecureSession();

// Check if user is already logged in
if (isset($_SESSION['id_pengguna'])) {
    header('Location: views/dashboard.php');
    exit();
}

// Redirect to login page
header('Location: views/login.html');
exit();
