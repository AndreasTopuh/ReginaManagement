<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'app/models/Booking.php';

echo "=== DEBUGGING DETAILED REVENUE ===\n\n";

$booking = new Booking();

// Test with current month dates
$date_from = '2025-08-01';
$date_to = '2025-08-31';

echo "Testing getDetailedRevenue with dates: $date_from to $date_to\n\n";

try {
    $result = $booking->getDetailedRevenue($date_from, $date_to);

    echo "Result count: " . count($result) . "\n";
    echo "Result data:\n";
    print_r($result);

    // Test with different parameters
    echo "\n--- Testing with all parameters ---\n";
    $result2 = $booking->getDetailedRevenue($date_from, $date_to, '', '', 'created_at', 'DESC');
    echo "Result count with all params: " . count($result2) . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
