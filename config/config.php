<?php
/** Application configuration. Set secrets in the web-server environment, never in Git. */

function envValue($name, $default = null) {
    $value = getenv($name);
    return ($value === false || $value === '') ? $default : $value;
}

define('DB_HOST', envValue('DB_HOST', 'localhost'));
define('DB_USER', envValue('DB_USER', ''));
define('DB_PASS', envValue('DB_PASS', ''));
define('DB_NAME', envValue('DB_NAME', ''));
define('APP_ENV', envValue('APP_ENV', 'production'));

date_default_timezone_set(envValue('APP_TIMEZONE', 'Asia/Jakarta'));

spl_autoload_register(function ($className) {
    if (!preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $className)) {
        return;
    }
    $file = __DIR__ . '/../classes/' . $className . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

function setCorsHeaders() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: same-origin');
    header('Cache-Control: no-store, private');
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowedOrigins = array_filter(array_map('trim', explode(',', envValue('ALLOWED_ORIGINS', ''))));
    if ($origin !== '' && in_array($origin, $allowedOrigins, true)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Vary: Origin');
        header('Access-Control-Allow-Credentials: true');
    }
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');
}

function startSecureSession() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? null) === '443');
    session_name('wongsibuk_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    session_start();
}

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function issueCsrfCookie() {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? null) === '443');
    setcookie('wongsibuk_csrf', csrfToken(), [
        'expires' => 0, 'path' => '/', 'secure' => $secure,
        'httponly' => false, 'samesite' => 'Lax',
    ]);
}

function requireCsrfToken() {
    $provided = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    $cookie = $_COOKIE['wongsibuk_csrf'] ?? '';
    if (!is_string($provided) || !is_string($cookie)
        || !hash_equals(csrfToken(), $provided) || !hash_equals($cookie, $provided)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid']);
        exit();
    }
}

function apiErrorResponse() {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan pada server']);
}
