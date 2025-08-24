<?php
require_once 'config/config.php';

$rooms = new RoomController();

if (isset($_GET['action']) && $_GET['action'] === 'add') {
    $rooms->add();
} elseif (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $rooms->edit($_GET['id']);
} else {
    $rooms->index();
}
?>
