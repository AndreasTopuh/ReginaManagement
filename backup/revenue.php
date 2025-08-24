<?php
session_start();
require_once 'config/config.php';
require_once 'app/controllers/RevenueController.php';

$controller = new RevenueController();
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'detailed':
        $controller->detailed();
        break;
    case 'export':
        $controller->export();
        break;
    case 'chart-data':
        $controller->chart_data();
        break;
    default:
        $controller->index();
        break;
}
