<?php
class RoomController {
    private $roomModel;
    private $floorModel;
    
    public function __construct() {
        $this->roomModel = new Room();
        $this->floorModel = new Floor();
    }
    
    public function index() {
        requireLogin();
        
        $status = $_GET['status'] ?? '';
        $rooms = $this->roomModel->getAll($status);
        
        include APP_PATH . '/views/rooms/index.php';
    }
    
    public function add() {
        requirePermission(['Owner', 'Admin']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'room_number' => trim($_POST['room_number']),
                'type_id' => (int) $_POST['type_id'],
                'floor_id' => (int) $_POST['floor_id'],
                'description' => trim($_POST['description']),
                'features' => trim($_POST['features'])
            ];
            
            // Validation
            if (empty($data['room_number'])) {
                setFlashMessage('error', 'Nomor kamar harus diisi.');
                return $this->showAddForm();
            }
            
            try {
                $this->roomModel->create($data);
                setFlashMessage('success', 'Kamar berhasil ditambahkan.');
                redirect('/rooms.php');
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    setFlashMessage('error', 'Nomor kamar sudah digunakan.');
                } else {
                    setFlashMessage('error', 'Gagal menambahkan kamar: ' . $e->getMessage());
                }
                return $this->showAddForm();
            }
        }
        
        return $this->showAddForm();
    }
    
    public function edit($id) {
        requirePermission(['Owner', 'Admin']);
        
        $room = $this->roomModel->findById($id);
        if (!$room) {
            setFlashMessage('error', 'Kamar tidak ditemukan.');
            redirect('/rooms.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'room_number' => trim($_POST['room_number']),
                'type_id' => (int) $_POST['type_id'],
                'floor_id' => (int) $_POST['floor_id'],
                'description' => trim($_POST['description']),
                'features' => trim($_POST['features'])
            ];
            
            // Validation
            if (empty($data['room_number'])) {
                setFlashMessage('error', 'Nomor kamar harus diisi.');
                return $this->showEditForm($room);
            }
            
            try {
                $this->roomModel->update($id, $data);
                setFlashMessage('success', 'Kamar berhasil diperbarui.');
                redirect('/rooms.php');
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    setFlashMessage('error', 'Nomor kamar sudah digunakan.');
                } else {
                    setFlashMessage('error', 'Gagal memperbarui kamar: ' . $e->getMessage());
                }
                return $this->showEditForm($room);
            }
        }
        
        return $this->showEditForm($room);
    }
    
    private function showAddForm() {
        $room_types = $this->roomModel->getTypes();
        $floors = $this->roomModel->getFloors();
        include APP_PATH . '/views/rooms/add.php';
    }
    
    private function showEditForm($room) {
        $room_types = $this->roomModel->getTypes();
        $floors = $this->roomModel->getFloors();
        include APP_PATH . '/views/rooms/edit.php';
    }
}
