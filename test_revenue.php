<?php
// Test Revenue System
echo "=== REVENUE SYSTEM TEST ===\n\n";

// Include necessary files
require_once 'config/config.php';
require_once 'app/models/Booking.php';
require_once 'app/controllers/RevenueController.php';

echo "✓ Config and model files loaded successfully\n";

// Check if revenue.php file exists and is readable
if (file_exists('revenue.php') && is_readable('revenue.php')) {
    echo "✓ revenue.php entry point exists and is readable\n";
} else {
    echo "✗ revenue.php file missing or not readable\n";
}

// Check if RevenueController exists
if (class_exists('RevenueController')) {
    echo "✓ RevenueController class exists\n";

    // Check if required methods exist
    $methods = ['index', 'detailed', 'export', 'chart_data'];
    foreach ($methods as $method) {
        if (method_exists('RevenueController', $method)) {
            echo "✓ RevenueController::{$method}() method exists\n";
        } else {
            echo "✗ RevenueController::{$method}() method missing\n";
        }
    }
} else {
    echo "✗ RevenueController class not found\n";
}

// Check if Booking model has revenue methods
if (class_exists('Booking')) {
    echo "✓ Booking model class exists\n";

    $revenue_methods = [
        'getRevenueSummary',
        'getRevenueByPeriod',
        'getRevenueByRoomType',
        'getRevenueByMonth',
        'getTopRevenueRooms',
        'getDetailedRevenue',
        'getBookingStatistics',
        'getPaymentMethodStats',
        'getRevenueChartData',
        'getOccupancyStats',
        'getRevenueGrowth'
    ];

    foreach ($revenue_methods as $method) {
        if (method_exists('Booking', $method)) {
            echo "✓ Booking::{$method}() method exists\n";
        } else {
            echo "✗ Booking::{$method}() method missing\n";
        }
    }
} else {
    echo "✗ Booking model class not found\n";
}

// Check if view files exist
$view_files = [
    'app/views/revenue/index.php',
    'app/views/revenue/detailed.php'
];

foreach ($view_files as $file) {
    if (file_exists($file) && is_readable($file)) {
        echo "✓ {$file} exists and is readable\n";
    } else {
        echo "✗ {$file} missing or not readable\n";
    }
}

// Check helper functions
$helper_functions = ['formatCurrency', 'formatDate', 'hasPermission'];
foreach ($helper_functions as $func) {
    if (function_exists($func)) {
        echo "✓ {$func}() function exists\n";
    } else {
        echo "✗ {$func}() function missing\n";
    }
}

echo "\n=== REVENUE SYSTEM TEST COMPLETE ===\n";
echo "If all items show ✓, the revenue system is properly installed!\n";
echo "Access the revenue system at: http://your-domain/revenue.php\n";
echo "Note: Only Owner and Admin users can access revenue reports.\n";
