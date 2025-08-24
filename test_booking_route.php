<?php
// Test simple routing POST
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set up environment
$_SERVER['REQUEST_URI'] = '/reginahotel/public/bookings/create';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['SCRIPT_NAME'] = '/reginahotel/public/index.php';

// Complete POST data for booking
$_POST = [
    'guest_name' => 'Test Guest',
    'id_type_id' => '1',
    'id_number' => '1234567890',
    'phone' => '081234567890',
    'email' => 'test@example.com',
    'checkin_date' => '2025-08-26',
    'checkout_date' => '2025-08-27',
    'meal_plan' => 'BREAKFAST',
    'special_request' => 'Test booking from debug',
    'selected_rooms' => ['1'] // Room ID 1
];

$_SESSION['user_id'] = 4; // Test user

// Include the main index.php logic
define('PUBLIC_PATH', __DIR__ . '/public');
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('STORAGE_PATH', BASE_PATH . '/storage');

// Load configuration
require_once CONFIG_PATH . '/config.php';

// Load core classes
require_once APP_PATH . '/core/Router.php';
require_once APP_PATH . '/core/BaseController.php';

// Load models
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/Booking.php';
require_once APP_PATH . '/models/Room.php';
require_once APP_PATH . '/models/Floor.php';
require_once APP_PATH . '/models/Guest.php';

// Load helpers
require_once APP_PATH . '/helpers/SessionManager.php';

// Initialize router
$router = new Router();

// Add the booking routes
$router->get('/bookings/create', 'BookingController@create');
$router->post('/bookings/create', 'BookingController@store');

echo "Testing POST to /bookings/create...\n";

try {
    $router->dispatch();
    echo "Route dispatched successfully\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
