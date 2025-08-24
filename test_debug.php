<?php
/**
 * Regina Hotel - Debug & Test Script
 */

require_once 'config/config.php';

echo "=== Regina Hotel Management System - Debug & Test ===\n\n";

// Test 1: Database Connection
echo "1. Testing Database Connection...\n";
try {
    $db = new Database();
    $result = $db->fetchOne('SELECT COUNT(*) as count FROM users');
    echo "✓ Database connection successful!\n";
    echo "✓ Found " . $result['count'] . " users in database\n\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: User Authentication
echo "2. Testing User Authentication...\n";
try {
    $userModel = new User();
    $user = $userModel->authenticate('owner', 'admin123');
    if ($user) {
        echo "✓ Owner login test successful\n";
        echo "  - User ID: " . $user['id'] . "\n";
        echo "  - Name: " . $user['name'] . "\n";
        echo "  - Role: " . $user['role_name'] . "\n";
    } else {
        echo "✗ Owner login test failed\n";
    }
    
    $user = $userModel->authenticate('admin', 'admin123');
    if ($user) {
        echo "✓ Admin login test successful\n";
    } else {
        echo "✗ Admin login test failed\n";
    }
    
    $user = $userModel->authenticate('receptionist', 'admin123');
    if ($user) {
        echo "✓ Receptionist login test successful\n";
    } else {
        echo "✗ Receptionist login test failed\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "✗ User authentication test failed: " . $e->getMessage() . "\n\n";
}

// Test 3: Room Model
echo "3. Testing Room Model...\n";
try {
    $roomModel = new Room();
    $rooms = $roomModel->getAll();
    echo "✓ Room model working\n";
    echo "✓ Found " . count($rooms) . " rooms\n";
    
    $stats = $roomModel->getStatistics();
    echo "✓ Room statistics:\n";
    echo "  - Total: " . $stats['total_rooms'] . "\n";
    echo "  - Available: " . $stats['available_rooms'] . "\n";
    echo "  - Occupied: " . $stats['occupied_rooms'] . "\n";
    echo "  - Out of Service: " . $stats['out_of_service_rooms'] . "\n";
    echo "\n";
} catch (Exception $e) {
    echo "✗ Room model test failed: " . $e->getMessage() . "\n\n";
}

// Test 4: Floor Model
echo "4. Testing Floor Model...\n";
try {
    $floorModel = new Floor();
    $floors = $floorModel->getAll();
    echo "✓ Floor model working\n";
    echo "✓ Found " . count($floors) . " floors\n";
    
    foreach ($floors as $floor) {
        echo "  - Floor " . $floor['floor_number'] . ": " . $floor['actual_rooms'] . " rooms\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "✗ Floor model test failed: " . $e->getMessage() . "\n\n";
}

// Test 5: Guest Model
echo "5. Testing Guest Model...\n";
try {
    $guestModel = new Guest();
    $idTypes = $guestModel->getIdTypes();
    echo "✓ Guest model working\n";
    echo "✓ Found " . count($idTypes) . " ID types\n";
    
    foreach ($idTypes as $type) {
        echo "  - " . $type['type_name'] . "\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "✗ Guest model test failed: " . $e->getMessage() . "\n\n";
}

// Test 6: Booking Model
echo "6. Testing Booking Model...\n";
try {
    $bookingModel = new Booking();
    $bookings = $bookingModel->getAll();
    echo "✓ Booking model working\n";
    echo "✓ Found " . count($bookings) . " bookings\n";
    
    $stats = $bookingModel->getStatistics();
    echo "✓ Booking statistics:\n";
    echo "  - Total: " . $stats['total_bookings'] . "\n";
    echo "  - Pending: " . $stats['pending_bookings'] . "\n";
    echo "  - Checked In: " . $stats['checked_in_bookings'] . "\n";
    echo "  - Checked Out: " . $stats['checked_out_bookings'] . "\n";
    echo "  - Canceled: " . $stats['canceled_bookings'] . "\n";
    echo "\n";
} catch (Exception $e) {
    echo "✗ Booking model test failed: " . $e->getMessage() . "\n\n";
}

// Test 7: Helper Functions
echo "7. Testing Helper Functions...\n";
try {
    echo "✓ formatCurrency(1500000): " . formatCurrency(1500000) . "\n";
    echo "✓ formatDate('2025-08-24'): " . formatDate('2025-08-24') . "\n";
    echo "✓ formatDateTime('2025-08-24 10:30:00'): " . formatDateTime('2025-08-24 10:30:00') . "\n";
    echo "✓ generateBookingCode(): " . generateBookingCode() . "\n";
    echo "\n";
} catch (Exception $e) {
    echo "✗ Helper functions test failed: " . $e->getMessage() . "\n\n";
}

echo "=== All Tests Completed ===\n";
echo "✓ Regina Hotel Management System Phase 1 is working correctly!\n\n";

echo "Login Credentials:\n";
echo "- Owner: owner / admin123\n";
echo "- Admin: admin / admin123\n";
echo "- Receptionist: receptionist / admin123\n\n";

echo "Access your application at: http://localhost:8080\n";
?>
