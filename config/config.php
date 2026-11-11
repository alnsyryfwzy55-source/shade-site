<?php
/**
 * ============================================================
 * ملف الإعدادات العامة للمشروع
 * ============================================================
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'shade_site');
define('DB_USER', 'root');
define('DB_PASS', '');

define('APP_ENV', 'local');
define('BASE_URL', 'http://localhost/shade-site');

define('MAIL_FROM', 'no-reply@yoursite.com');
define('MAIL_FROM_NAME', 'لوحة التحكم');

define('APP_SECRET', 'change-this-secret-key-per-client-2026-xyz');

define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);
define('WEBP_QUALITY', 80);
define('MAX_IMAGE_WIDTH', 1600);

define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCK_MINUTES', 5);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', APP_ENV === 'local' ? '1' : '0');
ini_set('log_errors', '1');

date_default_timezone_set('Asia/Riyadh');

set_exception_handler(function ($e) {
    error_log($e->getMessage() . ' في ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    if (APP_ENV === 'local') {
        echo '<div style="font-family:sans-serif;direction:rtl;background:#fee;border:1px solid #c00;padding:18px;margin:20px;border-radius:8px;">';
        echo '<b>⚠️ خطأ غير متوقع (وضع التطوير المحلي فقط):</b><br>';
        echo htmlspecialchars($e->getMessage()) . '<br>';
        echo '<small>' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</small>';
        echo '</div>';
    } else {
        echo 'عذرًا، حدث خطأ غير متوقع. حاول مرة أخرى لاحقًا.';
    }
});
