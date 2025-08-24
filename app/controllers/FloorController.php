<?php
class FloorController {
    private $floorModel;
    
    public function __construct() {
        $this->floorModel = new Floor();
    }
    
    public function index() {
        requirePermission(['Owner', 'Admin']);
        
        $floors = $this->floorModel->getAll();
        include APP_PATH . '/views/floors/index.php';
    }
    
    public function add() {
        requirePermission(['Owner', 'Admin']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $floor_number = (int) $_POST['floor_number'];
            
            if ($floor_number <= 0) {
                setFlashMessage('error', 'Nomor lantai harus lebih dari 0.');
                return $this->showAddForm();
            }
            
            if ($this->floorModel->exists($floor_number)) {
                setFlashMessage('error', 'Lantai dengan nomor tersebut sudah ada.');
                return $this->showAddForm();
            }
            
            try {
                $this->floorModel->create(['floor_number' => $floor_number]);
                setFlashMessage('success', 'Lantai berhasil ditambahkan.');
                redirect('/floors.php');
            } catch (Exception $e) {
                setFlashMessage('error', 'Gagal menambahkan lantai: ' . $e->getMessage());
                return $this->showAddForm();
            }
        }
        
        return $this->showAddForm();
    }
    
    public function edit($id) {
        requirePermission(['Owner', 'Admin']);
        
        $floor = $this->floorModel->findById($id);
        if (!$floor) {
            setFlashMessage('error', 'Lantai tidak ditemukan.');
            redirect('/floors.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $floor_number = (int) $_POST['floor_number'];
            
            if ($floor_number <= 0) {
                setFlashMessage('error', 'Nomor lantai harus lebih dari 0.');
                return $this->showEditForm($floor);
            }
            
            if ($this->floorModel->exists($floor_number, $id)) {
                setFlashMessage('error', 'Lantai dengan nomor tersebut sudah ada.');
                return $this->showEditForm($floor);
            }
            
            try {
                $this->floorModel->update($id, ['floor_number' => $floor_number]);
                setFlashMessage('success', 'Lantai berhasil diperbarui.');
                redirect('/floors.php');
            } catch (Exception $e) {
                setFlashMessage('error', 'Gagal memperbarui lantai: ' . $e->getMessage());
                return $this->showEditForm($floor);
            }
        }
        
        return $this->showEditForm($floor);
    }
    
    public function detail($id) {
        requirePermission(['Owner', 'Admin']);
        
        $floor = $this->floorModel->findById($id);
        if (!$floor) {
            setFlashMessage('error', 'Lantai tidak ditemukan.');
            redirect('/floors.php');
        }
        
        $rooms = $this->floorModel->getRoomsOnFloor($id);
        include APP_PATH . '/views/floors/detail.php';
    }
    
    public function delete($id) {
        requirePermission(['Owner', 'Admin']);
        
        $floor = $this->floorModel->findById($id);
        if (!$floor) {
            setFlashMessage('error', 'Lantai tidak ditemukan.');
            redirect('/floors.php');
        }
        
        if ($this->floorModel->delete($id)) {
            setFlashMessage('success', 'Lantai berhasil dihapus.');
        } else {
            setFlashMessage('error', 'Tidak dapat menghapus lantai yang masih memiliki kamar.');
        }
        
        redirect('/floors.php');
    }
    
    private function showAddForm() {
        include APP_PATH . '/views/floors/add.php';
    }
    
    private function showEditForm($floor) {
        include APP_PATH . '/views/floors/edit.php';
    }
}
