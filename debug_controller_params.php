<?php
echo "=== DEBUGGING CONTROLLER PARAMETERS ===\n\n";

// Simulate URL parameters from detailed revenue page
$_GET['action'] = 'detailed';
$_GET['date_from'] = '2025-08-01';
$_GET['date_to'] = '2025-08-31';
$_GET['status'] = '';
$_GET['sort_by'] = 'created_at';
$_GET['sort_order'] = 'DESC';

// Simulate processing like controller
$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to = $_GET['date_to'] ?? date('Y-m-t');
$status = $_GET['status'] ?? '';
$room_type = $_GET['room_type'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'created_at';
$sort_order = $_GET['sort_order'] ?? 'DESC';

echo "Parameters being used:\n";
echo "date_from: $date_from\n";
echo "date_to: $date_to\n";
echo "status: '$status'\n";
echo "room_type: '$room_type'\n";
echo "sort_by: $sort_by\n";
echo "sort_order: $sort_order\n\n";

// Test with these parameters
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'app/models/Booking.php';

$booking = new Booking();
$result = $booking->getDetailedRevenue($date_from, $date_to, $status, $room_type, $sort_by, $sort_order);

echo "Result count: " . count($result) . "\n";
if (count($result) > 0) {
    echo "First booking: " . $result[0]['booking_code'] . " - " . $result[0]['guest_name'] . "\n";
}
