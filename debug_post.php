<?php
// Debug POST handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('INCLUDES_PATH', BASE_PATH . '/includes');

require_once CONFIG_PATH . '/config.php';
require_once APP_PATH . '/core/Router.php';
require_once APP_PATH . '/core/BaseController.php';
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/helpers/SessionManager.php';

echo "POST Debug Test\n";
echo "===============\n";

// Simulate POST request
$_SERVER['REQUEST_URI'] = '/reginahotel/public/login';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['SCRIPT_NAME'] = '/reginahotel/public/index.php';
$_POST['username'] = 'testuser';
$_POST['password'] = 'test123';

echo "Simulated POST to: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Method: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "POST data: username=" . $_POST['username'] . ", password=" . $_POST['password'] . "\n\n";

// Initialize router and add routes
$router = new Router();
$router->post('/login', 'AuthController@login');

echo "Route added: POST /login -> AuthController@login\n\n";

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
$pattern = '#^/login$#';
echo "Testing pattern: $pattern\n";
echo "Against URI: $requestUri\n";
echo "Match result: " . (preg_match($pattern, $requestUri) ? 'MATCH' : 'NO MATCH') . "\n\n";

try {
    echo "Attempting to dispatch...\n";
    $router->dispatch();
} catch (Exception $e) {
    echo "Error during dispatch: " . $e->getMessage() . "\n";
}
?>
