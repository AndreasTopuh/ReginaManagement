<?php

/**
 * Regina Hotel Management System
 * Main Configuration File
 */

// Error reporting (production mode)
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Production settings - uncomment for production
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/storage/logs/php_errors.log');

// Base paths (only define if not already defined)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
    define('APP_PATH', BASE_PATH . '/app');
    define('PUBLIC_PATH', BASE_PATH . '/public');
    define('CONFIG_PATH', BASE_PATH . '/config');
    define('INCLUDES_PATH', BASE_PATH . '/includes');
}

// Start session with security
require_once APP_PATH . '/helpers/SessionManager.php';
SessionManager::start();

// Simple Base URL configuration for regina.kawanua.org
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';

define('BASE_URL', $protocol . '://' . $host);
define('ASSETS_URL', BASE_URL . '/assets');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'regina_hotel');
define('DB_USER', 'hotel_admin');
define('DB_PASS', 'passwordku123');

// Midtrans configuration
define('MIDTRANS_SERVER_KEY', 'your-server-key-here');
define('MIDTRANS_CLIENT_KEY', 'your-client-key-here');
define('MIDTRANS_IS_PRODUCTION', false);
define('MIDTRANS_MERCHANT_ID', 'your-merchant-id-here');

// Application settings
define('APP_NAME', 'Regina Hotel Management System');
define('APP_VERSION', '1.0.0');
define('APP_DEBUG', false);
define('TIMEZONE', 'Asia/Makassar');

// Set timezone
date_default_timezone_set(TIMEZONE);

// Auto-load classes
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/',
        APP_PATH . '/helpers/',
        APP_PATH . '/core/',
        CONFIG_PATH . '/',
        INCLUDES_PATH . '/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Include database connection
require_once CONFIG_PATH . '/database.php';

// Helper functions
function redirect($url)
{
    $redirectUrl = rtrim(BASE_URL, '/') . '/' . ltrim($url, '/');
    header("Location: " . $redirectUrl);
    exit();
}

function asset($path)
{
    return rtrim(ASSETS_URL, '/') . '/' . ltrim($path, '/');
}

function url($path = '')
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function isSecureConnection()
{
    return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
}

function isLoggedIn()
{
    return SessionManager::isLoggedIn();
}

function hasPermission($allowed_roles)
{
    if (!isLoggedIn()) {
        return false;
    }

    if (is_string($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }

    return in_array(SessionManager::getUserRole(), $allowed_roles);
}

function requireLogin()
{
    SessionManager::requireLogin();
}

function requirePermission($allowed_roles)
{
    SessionManager::requireRole($allowed_roles);
}

function formatCurrency($amount)
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function formatDate($date, $format = 'd/m/Y')
{
    if (empty($date) || $date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '-';
    }
    return date($format, strtotime($date));
}

function formatDateTime($datetime, $format = 'd/m/Y H:i')
{
    if (empty($datetime) || $datetime == '0000-00-00 00:00:00') {
        return '-';
    }
    return date($format, strtotime($datetime));
}

function generateBookingCode()
{
    $db = Database::getInstance();

    // Get last booking code
    $result = $db->fetchOne("SELECT booking_code FROM bookings ORDER BY id DESC LIMIT 1");

    if ($result) {
        $last_code = $result['booking_code'];
        $number = (int) substr($last_code, 3) + 1;
    } else {
        $number = 1;
    }

    return 'BK_' . str_pad($number, 4, '0', STR_PAD_LEFT);
}

// Flash messages
function setFlashMessage($type, $message)
{
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage()
{
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

// User photo helper functions
function getUserPhotoUrl($photo, $size = 'profile')
{
    if (empty($photo)) {
        return null;
    }

    $filename = $photo;

    // Get appropriate size variant
    switch ($size) {
        case 'thumbnail':
            $filename = str_replace('.', '_thumb.', $photo);
            break;
        case 'avatar':
            $filename = str_replace('.', '_avatar.', $photo);
            break;
        case 'profile':
        default:
            // Use original filename
            break;
    }

    // Check if size variant exists, fallback to original
    $path = PUBLIC_PATH . '/images/imageUsers/' . $filename;
    if (file_exists($path)) {
        return BASE_URL . '/images/imageUsers/' . htmlspecialchars($filename);
    }

    // Fallback to original photo
    return BASE_URL . '/images/imageUsers/' . htmlspecialchars($photo);
}

function getUserPhotoPath($photo, $size = 'profile')
{
    if (empty($photo)) {
        return null;
    }

    $filename = $photo;

    switch ($size) {
        case 'thumbnail':
            $filename = str_replace('.', '_thumb.', $photo);
            break;
        case 'avatar':
            $filename = str_replace('.', '_avatar.', $photo);
            break;
    }

    return PUBLIC_PATH . '/images/imageUsers/' . $filename;
}

function getUserInitials($name)
{
    $parts = explode(' ', trim($name));
    $initials = '';

    foreach ($parts as $part) {
        if (!empty($part)) {
            $initials .= strtoupper(substr($part, 0, 1));
            if (strlen($initials) >= 2) {
                break;
            }
        }
    }

    return $initials ?: 'U';
}

function displayUserAvatar($user, $size = 40, $classes = '', $imageSize = 'avatar')
{
    $defaultClasses = 'user-avatar';
    if ($size <= 32) {
        $defaultClasses .= ' user-avatar-small';
    }
    $allClasses = trim($defaultClasses . ' ' . $classes);

    if (!empty($user['photo'])) {
        // Use appropriate image size based on display size
        if ($size <= 50) {
            $imageSize = 'avatar';
        } elseif ($size <= 150) {
            $imageSize = 'thumbnail';
        } else {
            $imageSize = 'profile';
        }

        $photoUrl = getUserPhotoUrl($user['photo'], $imageSize);
        return '<img src="' . $photoUrl . '" alt="' . htmlspecialchars($user['name']) . '" 
                     class="' . $allClasses . '" 
                     style="width: ' . $size . 'px; height: ' . $size . 'px; object-fit: cover; border-radius: 50%;">';
    } else {
        $initials = getUserInitials($user['name']);
        $fontSize = floor($size * 0.4);
        return '<div class="user-avatar-initials ' . $allClasses . ' d-flex align-items-center justify-content-center text-white" style="width: ' . $size . 'px; height: ' . $size . 'px; font-size: ' . $fontSize . 'px; border-radius: 50%;">' . $initials . '</div>';
    }
}
