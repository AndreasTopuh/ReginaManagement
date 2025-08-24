<?php
require_once 'config/config.php';

// Simulate login
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['role'] = 'Admin';

echo "<h2>Booking Creation Debug Test</h2>";

// Test 1: Check if Room model loads
echo "<h3>1. Testing Room Model</h3>";
try {
    $roomModel = new Room();
    echo "✓ Room model loaded successfully<br>";

    // Test 2: Get room types
    $roomTypes = $roomModel->getTypes();
    echo "✓ Room types found: " . count($roomTypes) . "<br>";
    foreach ($roomTypes as $type) {
        echo "- " . $type['type_name'] . " (Rp " . number_format($type['price']) . ")<br>";
    }
} catch (Exception $e) {
    echo "✗ Error with Room model: " . $e->getMessage() . "<br>";
}

// Test 3: Test available rooms with specific dates
echo "<h3>2. Testing Available Rooms</h3>";
try {
    $checkin = '2025-08-25';
    $checkout = '2025-08-26';

    echo "Testing dates: $checkin to $checkout<br>";
    $availableRooms = $roomModel->getAvailableRooms($checkin, $checkout);
    echo "✓ Available rooms found: " . count($availableRooms) . "<br>";

    if (!empty($availableRooms)) {
        foreach ($availableRooms as $room) {
            echo "- Room {$room['room_number']} ({$room['type_name']}) - Rp " . number_format($room['price']) . "/night<br>";
        }
    } else {
        echo "No available rooms found. Let's check why...<br>";

        // Check total rooms
        $allRooms = $roomModel->getAll();
        echo "Total rooms in system: " . count($allRooms) . "<br>";

        foreach ($allRooms as $room) {
            echo "- Room {$room['room_number']} (Status: {$room['status']})<br>";
        }
    }
} catch (Exception $e) {
    echo "✗ Error getting available rooms: " . $e->getMessage() . "<br>";
}

// Test 4: Check Guest model
echo "<h3>3. Testing Guest Model</h3>";
try {
    $guestModel = new Guest();
    $idTypes = $guestModel->getIdTypes();
    echo "✓ ID types found: " . count($idTypes) . "<br>";
    foreach ($idTypes as $type) {
        echo "- " . $type['type_name'] . "<br>";
    }
} catch (Exception $e) {
    echo "✗ Error with Guest model: " . $e->getMessage() . "<br>";
}

// Test 5: Simulate BookingController
echo "<h3>4. Testing BookingController</h3>";
try {
    $bookingController = new BookingController();
    echo "✓ BookingController loaded successfully<br>";
} catch (Exception $e) {
    echo "✗ Error with BookingController: " . $e->getMessage() . "<br>";
}

echo "<br><strong>Debug complete!</strong>";
