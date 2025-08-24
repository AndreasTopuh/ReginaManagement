<?php
// Debug POST /bookings
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('INCLUDES_PATH', BASE_PATH . '/includes');

require_once CONFIG_PATH . '/config.php';
require_once APP_PATH . '/core/Router.php';

echo "POST /bookings Debug\n";
echo "====================\n";

// Simulate POST request to /bookings
$_SERVER['REQUEST_URI'] = '/reginahotel/public/bookings';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['SCRIPT_NAME'] = '/reginahotel/public/index.php';

echo "URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Method: " . $_SERVER['REQUEST_METHOD'] . "\n\n";

// Initialize router
$router = new Router();
$router->post('/bookings', 'BookingController@store');

echo "Route registered: POST /bookings -> BookingController@store\n\n";

// Test URL parsing
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
echo "Original URI: $requestUri\n";

$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = rtrim(dirname($scriptName), '/');
echo "Base path: $basePath\n";

if ($basePath && $basePath !== '.' && strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
    echo "URI after base removal: $requestUri\n";
}

$requestUri = '/' . ltrim($requestUri, '/');
echo "Final URI: $requestUri\n\n";

// Test pattern matching
$pattern = '#^/bookings$#';
echo "Testing pattern: $pattern\n";
echo "Against URI: $requestUri\n";
echo "Match result: " . (preg_match($pattern, $requestUri) ? 'MATCH' : 'NO MATCH') . "\n";
?>
