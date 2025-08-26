<?php
require_once 'config/config.php';
require_once 'config/database.php';

$db = Database::getInstance();
$room = $db->fetchOne('SELECT room_number, status FROM rooms WHERE room_number = ?', ['102']);

if ($room) {
    echo "Kamar: " . $room['room_number'] . " - Status: " . $room['status'] . "\n";
} else {
    echo "Kamar 102 tidak ditemukan\n";
}
