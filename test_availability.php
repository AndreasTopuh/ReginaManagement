<?php
require_once 'config/config.php';
require_once 'app/models/Room.php';

$room = new Room();

// Test untuk kamar yang sudah dibooking 101 dari 25-27 Agustus
echo "<h3>Test Room Availability</h3>";

// Test dengan debugging SQL query
echo "<h4>Debug: Test SQL Query manually</h4>";
$db = Database::getInstance();

$test_checkin = '2024-08-26';
$test_checkout = '2024-08-27';

$debug_sql = "SELECT DISTINCT br.room_id, r.room_number, b.checkin_date, b.checkout_date, b.status
              FROM booking_rooms br 
              JOIN bookings b ON br.booking_id = b.id 
              JOIN rooms r ON br.room_id = r.id
              WHERE b.status NOT IN ('CheckedOut', 'Cancelled') 
              AND (
                  (DATE(b.checkin_date) < DATE('$test_checkin') AND DATE(b.checkout_date) > DATE('$test_checkin')) OR
                  (DATE(b.checkin_date) >= DATE('$test_checkin') AND DATE(b.checkin_date) < DATE('$test_checkout')) OR
                  (DATE(b.checkout_date) > DATE('$test_checkin') AND DATE(b.checkout_date) <= DATE('$test_checkout')) OR
                  (DATE(b.checkin_date) <= DATE('$test_checkin') AND DATE(b.checkout_date) >= DATE('$test_checkout'))
              )";

$conflicts = $db->fetchAll($debug_sql);
echo "Conflicting bookings for Aug 26-27:<br>";
if ($conflicts) {
    foreach ($conflicts as $c) {
        echo "- Room {$c['room_number']} ({$c['checkin_date']} to {$c['checkout_date']}) - Status: {$c['status']}<br>";
    }
} else {
    echo "No conflicts found<br>";
}

echo "<h4>1. Available rooms for Aug 25-27 (should NOT include room 101):</h4>";
$available = $room->getAvailableRooms('2024-08-25', '2024-08-27');
if ($available) {
    foreach ($available as $r) {
        echo "Room {$r['room_number']} - {$r['type_name']} - Rp " . number_format($r['price']) . "<br>";
    }
} else {
    echo "No rooms available<br>";
}

echo "<h4>2. Available rooms for Aug 26-27 (overlap test - should NOT include room 101):</h4>";
$available2 = $room->getAvailableRooms('2024-08-26', '2024-08-27');
if ($available2) {
    foreach ($available2 as $r) {
        echo "Room {$r['room_number']} - {$r['type_name']} - Rp " . number_format($r['price']) . "<br>";
    }
} else {
    echo "No rooms available<br>";
}

echo "<h4>3. Available rooms for Aug 28-30 (should include room 101):</h4>";
$available3 = $room->getAvailableRooms('2024-08-28', '2024-08-30');
if ($available3) {
    foreach ($available3 as $r) {
        echo "Room {$r['room_number']} - {$r['type_name']} - Rp " . number_format($r['price']) . "<br>";
    }
} else {
    echo "No rooms available<br>";
}

echo "<h4>4. Current bookings for verification:</h4>";
$bookings_sql = "SELECT b.id, b.booking_code, br.room_id, r.room_number, b.checkin_date, b.checkout_date, b.status 
                 FROM bookings b 
                 JOIN booking_rooms br ON b.id = br.booking_id 
                 JOIN rooms r ON br.room_id = r.id 
                 WHERE b.status NOT IN ('CheckedOut', 'Cancelled')
                 ORDER BY b.checkin_date";
$bookings = $db->fetchAll($bookings_sql);
if ($bookings) {
    foreach ($bookings as $booking) {
        echo "Booking {$booking['booking_code']} - Room {$booking['room_number']} - {$booking['checkin_date']} to {$booking['checkout_date']} - Status: {$booking['status']}<br>";
    }
} else {
    echo "No active bookings<br>";
}
