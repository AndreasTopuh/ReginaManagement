<?php
class RoomController extends BaseController {
    private $roomModel;
    private $floorModel;
    
    public function __construct() {
        parent::__construct();
        $this->roomModel = new Room();
        $this->floorModel = new Floor();
    }
    
    public function index() {
        $this->requireLogin();
        
        $status = $_GET['status'] ?? '';
        $rooms = $this->roomModel->getAll($status);
        
        $this->render('rooms/index', [
            'title' => 'Rooms Management - Regina Hotel',
            'rooms' => $rooms,
            'status' => $status
        ]);
    }
    
    public function create() {
        $this->requireRole(['Owner', 'Admin']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->store();
        }
        
        return $this->showCreateForm();
    }
    
    public function store() {
        $this->requireRole(['Owner', 'Admin']);
        
        $data = [
            'room_number' => trim($_POST['room_number']),
            'type_id' => (int) $_POST['type_id'],
            'floor_id' => (int) $_POST['floor_id'],
            'description' => trim($_POST['description']),
            'features' => trim($_POST['features'])
        ];
        
        // Validation
        if (empty($data['room_number'])) {
            $this->flashMessage('error', 'Nomor kamar harus diisi.');
            return $this->showCreateForm();
        }
        
        try {
            $this->roomModel->create($data);
            $this->flashMessage('success', 'Kamar berhasil ditambahkan.');
            $this->redirect('/rooms');
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $this->flashMessage('error', 'Nomor kamar sudah digunakan.');
            } else {
                $this->flashMessage('error', 'Gagal menambahkan kamar: ' . $e->getMessage());
            }
            return $this->showCreateForm();
        }
    }
    
    public function edit($id) {
        $this->requireRole(['Owner', 'Admin']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->update($id);
        }
        
        return $this->showEditForm($id);
    }
    
    public function update($id) {
        $this->requireRole(['Owner', 'Admin']);
        
        $data = [
            'room_number' => trim($_POST['room_number']),
            'type_id' => (int) $_POST['type_id'],
            'floor_id' => (int) $_POST['floor_id'],
            'description' => trim($_POST['description']),
            'features' => trim($_POST['features'])
        ];
        
        try {
            $this->roomModel->update($id, $data);
            $this->flashMessage('success', 'Kamar berhasil diupdate.');
            $this->redirect('/rooms');
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $this->flashMessage('error', 'Nomor kamar sudah digunakan.');
            } else {
                $this->flashMessage('error', 'Gagal mengupdate kamar: ' . $e->getMessage());
            }
            return $this->showEditForm($id);
        }
    }
    
    public function getAvailableRooms() {
        $this->requireLogin();
        
        $checkin_date = $_GET['checkin_date'] ?? '';
        $checkout_date = $_GET['checkout_date'] ?? '';
        
        if (empty($checkin_date) || empty($checkout_date)) {
            $this->json(['error' => 'Check-in and check-out dates are required'], 400);
        }
        
        try {
            $rooms = $this->roomModel->getAvailableRooms($checkin_date, $checkout_date);
            $this->json($rooms);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }
    
    private function showCreateForm() {
        $floors = $this->floorModel->getAll();
        $room_types = $this->getRoomTypes();
        
        $this->render('rooms/add', [
            'title' => 'Add New Room - Regina Hotel',
            'floors' => $floors,
            'room_types' => $room_types
        ]);
    }
    
    private function showEditForm($id) {
        $room = $this->roomModel->findById($id);
        if (!$room) {
            $this->flashMessage('error', 'Kamar tidak ditemukan.');
            $this->redirect('/rooms');
        }
        
        $floors = $this->floorModel->getAll();
        $room_types = $this->getRoomTypes();
        
        $this->render('rooms/edit', [
            'title' => 'Edit Room - Regina Hotel',
            'room' => $room,
            'floors' => $floors,
            'room_types' => $room_types
        ]);
    }
    
    private function getRoomTypes() {
        // Get room types from database
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM room_types ORDER BY type_name");
    }
}
