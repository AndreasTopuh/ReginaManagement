<?php
require_once 'config/config.php';

$floors = new FloorController();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'add':
            $floors->add();
            break;
        case 'edit':
            if (isset($_GET['id'])) {
                $floors->edit($_GET['id']);
            }
            break;
        case 'detail':
            if (isset($_GET['id'])) {
                $floors->detail($_GET['id']);
            }
            break;
        case 'delete':
            if (isset($_GET['id'])) {
                $floors->delete($_GET['id']);
            }
            break;
        default:
            $floors->index();
    }
} else {
    $floors->index();
}
?>
