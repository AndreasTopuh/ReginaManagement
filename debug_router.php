<?php
// Debug router script
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('INCLUDES_PATH', BASE_PATH . '/includes');

require_once CONFIG_PATH . '/config.php';
require_once APP_PATH . '/core/Router.php';

echo "<h1>Router Debug</h1>";

echo "<h2>Server Variables</h2>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "<br>";
echo "REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'Not set') . "<br>";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'Not set') . "<br>";

echo "<h2>URL Parsing Test</h2>";
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
echo "Parsed URI: $requestUri<br>";

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = rtrim(dirname($scriptName), '/');
echo "Script name: $scriptName<br>";
echo "Base path: $basePath<br>";

if ($basePath && $basePath !== '.' && strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
    echo "URI after base path removal: $requestUri<br>";
}

// Ensure we start with /
$requestUri = '/' . ltrim($requestUri, '/');
echo "Final URI: $requestUri<br>";

echo "<h2>Route Testing</h2>";
$router = new Router();
$router->get('/', 'AuthController@checkAuth');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/dashboard', 'DashboardController@index');
$router->get('/bookings/create', 'BookingController@create');

echo "Routes registered<br>";

// Test pattern matching
$testRoutes = ['/', '/login', '/dashboard', '/bookings/create'];
foreach ($testRoutes as $testRoute) {
    $pattern = '#^' . preg_replace('/\{([^}]+)\}/', '([^/]+)', $testRoute) . '$#';
    $match = preg_match($pattern, $requestUri);
    echo "Testing route '$testRoute' against '$requestUri': " . ($match ? 'MATCH' : 'NO MATCH') . "<br>";
}
?>
