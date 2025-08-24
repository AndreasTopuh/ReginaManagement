<?php
// Final debug for POST booking
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== FINAL DEBUG BOOKING POST ===\n";

// Simulate real request environment
$_SERVER['REQUEST_URI'] = '/reginahotel/public/bookings/create';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['SCRIPT_NAME'] = '/reginahotel/public/index.php';
$_SERVER['HTTP_HOST'] = '103.162.115.122';

// Set session as logged in
session_start();
$_SESSION['user_id'] = 4;
$_SESSION['username'] = 'testuser';
$_SESSION['name'] = 'Test User';
$_SESSION['role_id'] = 2;
$_SESSION['role_name'] = 'Admin';

echo "Session user_id: " . ($_SESSION['user_id'] ?? 'None') . "\n";

// Complete POST data
$_POST = [
    'guest_name' => 'Debug Guest',
    'id_type_id' => '1',
    'id_number' => '1111111111',
    'phone' => '081234567890',
    'email' => 'debug@test.com',
    'checkin_date' => '2025-08-26',
    'checkout_date' => '2025-08-27',
    'meal_plan' => 'BREAKFAST',
    'special_request' => 'Debug test',
    'selected_rooms' => ['1']
];

echo "POST data set with " . count($_POST) . " fields\n";

// Load app exactly like index.php but without output buffering issues
define('PUBLIC_PATH', __DIR__ . '/public');
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('STORAGE_PATH', BASE_PATH . '/storage');

require_once CONFIG_PATH . '/config.php';
require_once APP_PATH . '/core/Router.php';
require_once APP_PATH . '/core/BaseController.php';
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/Booking.php';
require_once APP_PATH . '/models/Room.php';
require_once APP_PATH . '/models/Floor.php';
require_once APP_PATH . '/models/Guest.php';
require_once APP_PATH . '/helpers/SessionManager.php';

$router = new Router();

// Add all routes like in index.php
$router->get('/', 'AuthController@checkAuth');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

$router->get('/dashboard', 'DashboardController@index');

$router->get('/bookings', 'BookingController@index');
$router->get('/bookings/create', 'BookingController@create');
$router->post('/bookings/create', 'BookingController@store');
$router->post('/bookings', 'BookingController@store');

echo "Routes added\n";

echo "Attempting dispatch...\n";

// Capture any redirect or output
ob_start();
try {
    $router->dispatch();
    echo "Dispatch completed successfully\n";
} catch (Exception $e) {
    echo "Exception during dispatch: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
$output = ob_get_clean();

echo "Output captured: " . strlen($output) . " characters\n";
if (strlen($output) > 0) {
    echo "First 500 chars:\n" . substr($output, 0, 500) . "\n";
}
?>
