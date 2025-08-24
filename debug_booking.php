<?php
// Debug script for booking issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define paths
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');
define('INCLUDES_PATH', BASE_PATH . '/includes');

// Load configuration
require_once CONFIG_PATH . '/config.php';

echo "<h1>Regina Hotel Booking Debug</h1>";

// Test database connection
echo "<h2>1. Database Connection Test</h2>";
try {
    require_once CONFIG_PATH . '/database.php';
    $db = Database::getInstance();
    echo "✅ Database connection successful<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    exit;
}

// Test session
echo "<h2>2. Session Test</h2>";
require_once APP_PATH . '/helpers/SessionManager.php';
SessionManager::start();
echo "✅ Session started<br>";
echo "Session ID: " . session_id() . "<br>";
echo "Logged in: " . (SessionManager::isLoggedIn() ? 'Yes' : 'No') . "<br>";

// Test models
echo "<h2>3. Model Loading Test</h2>";
try {
    require_once APP_PATH . '/models/User.php';
    require_once APP_PATH . '/models/Booking.php';
    require_once APP_PATH . '/models/Room.php';
    require_once APP_PATH . '/models/Floor.php';
    require_once APP_PATH . '/models/Guest.php';
    echo "✅ All models loaded successfully<br>";
} catch (Exception $e) {
    echo "❌ Model loading failed: " . $e->getMessage() . "<br>";
}

// Test guest model
echo "<h2>4. Guest Model Test</h2>";
try {
    $guestModel = new Guest();
    $idTypes = $guestModel->getIdTypes();
    echo "✅ Guest model works, ID types count: " . count($idTypes) . "<br>";
    foreach ($idTypes as $type) {
        echo "- " . $type['type_name'] . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Guest model failed: " . $e->getMessage() . "<br>";
}

// Test room model
echo "<h2>5. Room Model Test</h2>";
try {
    $roomModel = new Room();
    $rooms = $roomModel->getAllAvailableRooms();
    echo "✅ Room model works, available rooms count: " . count($rooms) . "<br>";
    foreach (array_slice($rooms, 0, 3) as $room) {
        echo "- Room {$room['room_number']} - {$room['type_name']} - {$room['price']}<br>";
    }
} catch (Exception $e) {
    echo "❌ Room model failed: " . $e->getMessage() . "<br>";
}

// Test booking creation (simulation)
echo "<h2>6. Booking Creation Simulation</h2>";
if (!SessionManager::isLoggedIn()) {
    echo "⚠️ Not logged in - simulating login<br>";
    // Simulate login for testing
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['name'] = 'Admin Test';
    $_SESSION['role_id'] = 2;
    $_SESSION['role_name'] = 'Admin';
}

echo "User ID: " . ($_SESSION['user_id'] ?? 'None') . "<br>";

try {
    $bookingModel = new Booking();
    echo "✅ Booking model instantiated<br>";
    
    // Test data
    $guest_data = [
        'full_name' => 'Test Guest',
        'id_type_id' => 1,
        'id_number' => '1234567890',
        'phone' => '081234567890',
        'email' => 'test@example.com'
    ];
    
    $booking_data = [
        'checkin_date' => '2025-08-25',
        'checkout_date' => '2025-08-26',
        'meal_plan' => 'BREAKFAST',
        'special_request' => 'Test booking'
    ];
    
    $rooms_data = [
        [
            'room_id' => 1,
            'price_per_night' => 100000
        ]
    ];
    
    echo "✅ Test data prepared<br>";
    
    // Just test the guest creation part
    $guestModel = new Guest();
    $guest_id = $guestModel->create($guest_data);
    echo "✅ Guest created with ID: $guest_id<br>";
    
    // Clean up test guest
    $db->execute("DELETE FROM guests WHERE id = ?", [$guest_id]);
    echo "✅ Test guest cleaned up<br>";
    
} catch (Exception $e) {
    echo "❌ Booking simulation failed: " . $e->getMessage() . "<br>";
    echo "Stack trace:<br><pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>7. URL Routing Test</h2>";
$request_uri = $_SERVER['REQUEST_URI'] ?? '';
echo "Current URI: $request_uri<br>";
echo "Base URL: " . BASE_URL . "<br>";

echo "<h2>Debug Complete</h2>";
echo "<a href='/reginahotel/public/bookings/create'>Try Booking Create</a><br>";
echo "<a href='/reginahotel/public/login'>Try Login</a>";
?>
