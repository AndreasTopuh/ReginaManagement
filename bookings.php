<?php
require_once 'config/config.php';

$bookings = new BookingController();

if (isset($_GET['action']) && $_GET['action'] === 'create') {
    $bookings->create();
} else {
    $bookings->index();
}
?>
