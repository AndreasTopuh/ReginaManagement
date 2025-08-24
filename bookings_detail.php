<?php
require_once 'config/config.php';

if (!isset($_GET['id'])) {
    redirect('/bookings.php');
}

$bookings = new BookingController();
$bookings->view($_GET['id']);
?>
