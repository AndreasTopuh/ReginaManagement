<?php
// Test Revenue SQL Query
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'app/models/Booking.php';

echo "=== TESTING REVENUE SQL QUERIES ===\n\n";

try {
    $booking = new Booking();

    echo "1. Testing getRevenueSummary...\n";
    $result = $booking->getRevenueSummary('2024-01-01', '2024-12-31');
    echo "✓ getRevenueSummary works!\n";
    print_r($result);

    echo "\n2. Testing getRevenueByMonth...\n";
    $result = $booking->getRevenueByMonth(6);
    echo "✓ getRevenueByMonth works!\n";
    print_r($result);

    echo "\n3. Testing getRevenueByPeriod...\n";
    $result = $booking->getRevenueByPeriod('2024-01-01', '2024-12-31', 'monthly');
    echo "✓ getRevenueByPeriod works!\n";
    print_r($result);

    echo "\n4. Testing getRevenueByRoomType...\n";
    $result = $booking->getRevenueByRoomType('2024-01-01', '2024-12-31');
    echo "✓ getRevenueByRoomType works!\n";
    print_r($result);
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
