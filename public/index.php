<?php
/**
 * Regina Hotel Management System
 * Single Entry Point
 */

// Define paths
define('PUBLIC_PATH', __DIR__);
define('BASE_PATH', dirname(__DIR__));
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

// === AUTHENTICATION ROUTES ===
$router->get('/', 'AuthController@checkAuth');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// === DASHBOARD ROUTES ===
$router->get('/dashboard', 'DashboardController@index');

// === BOOKING ROUTES ===
$router->get('/bookings', 'BookingController@index');
$router->get('/bookings/create', 'BookingController@create');
$router->post('/bookings/create', 'BookingController@store');
$router->post('/bookings', 'BookingController@store');
$router->post('/bookings/checkAvailability', 'BookingController@checkAvailability');
$router->get('/bookings/{id}', 'BookingController@show');
$router->post('/bookings/{id}/update', 'BookingController@update');
$router->post('/bookings/{id}/checkin', 'BookingController@checkin');
$router->post('/bookings/{id}/checkout', 'BookingController@checkout');
$router->post('/bookings/{id}/cancel', 'BookingController@cancel');

// === ROOM ROUTES ===
$router->get('/rooms', 'RoomController@index');
$router->get('/rooms/create', 'RoomController@create');
$router->post('/rooms', 'RoomController@store');
$router->get('/rooms/{id}/edit', 'RoomController@edit');
$router->post('/rooms/{id}', 'RoomController@update');

// === FLOOR ROUTES ===
$router->get('/floors', 'FloorController@index');
$router->get('/floors/create', 'FloorController@create');
$router->post('/floors', 'FloorController@store');
$router->get('/floors/{id}', 'FloorController@show');
$router->get('/floors/{id}/edit', 'FloorController@edit');
$router->post('/floors/{id}', 'FloorController@update');
$router->post('/floors/{id}/delete', 'FloorController@delete');

// === USER ROUTES ===
$router->get('/users', 'UserController@index');
$router->get('/users/create', 'UserController@create');
$router->post('/users', 'UserController@store');
$router->get('/users/{id}/edit', 'UserController@edit');
$router->post('/users/{id}', 'UserController@update');
$router->post('/users/{id}/delete', 'UserController@delete');
$router->post('/users/{id}/toggle-status', 'UserController@toggleStatus');

// === REVENUE ROUTES ===
$router->get('/revenue', 'RevenueController@index');
$router->get('/revenue/detailed', 'RevenueController@detailed');
$router->get('/revenue/export', 'RevenueController@export');
$router->get('/revenue/chart-data', 'RevenueController@chartData');

// === PROFILE ROUTES ===
$router->get('/profile', 'UserController@profile');
$router->post('/profile', 'UserController@updateProfile');

// === API ROUTES (for AJAX) ===
$router->get('/api/rooms/available', 'RoomController@getAvailableRooms');
$router->get('/api/bookings/statistics', 'BookingController@getStatistics');

// Dispatch the route
try {
    $router->dispatch();
} catch (Exception $e) {
    error_log("Application error: " . $e->getMessage());
    
    if (APP_DEBUG) {
        echo "<h1>Application Error</h1>";
        echo "<pre>" . $e->getMessage() . "\n" . $e->getTraceAsString() . "</pre>";
    } else {
        echo "<h1>Something went wrong</h1>";
        echo "<p>Please try again later.</p>";
    }
}
