<?php
session_start();
require_once 'config/config.php';
require_once 'app/controllers/UserController.php';

$controller = new UserController();
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'create':
        $controller->create();
        break;
    case 'edit':
        if ($id) {
            $controller->edit($id);
        } else {
            header('Location: ' . BASE_URL . '/users.php');
        }
        break;
    case 'delete':
        if ($id) {
            $controller->delete($id);
        } else {
            header('Location: ' . BASE_URL . '/users.php');
        }
        break;
    case 'toggle-status':
        if ($id) {
            $controller->toggleStatus($id);
        } else {
            header('Location: ' . BASE_URL . '/users.php');
        }
        break;
    default:
        $controller->index();
        break;
}
