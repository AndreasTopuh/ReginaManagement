<?php
class RoomController extends BaseController
{
    private $roomModel;
    private $floorModel;

    public function __construct()
    {
        parent::__construct();
        $this->roomModel = new Room();
        $this->floorModel = new Floor();
    }

    public function index()
    {
        $this->requireLogin();

        // Handle action parameter
        $action = $_GET['action'] ?? '';
        $id = $_GET['id'] ?? '';

        // If action is edit and ID is provided, redirect to edit method
        if ($action === 'edit' && $id) {
            $this->edit($id);
            return;
        }

        // If action is add, redirect to create method
        if ($action === 'add') {
            $this->create();
            return;
        }

        $status = $_GET['status'] ?? '';
        $rooms = $this->roomModel->getAll($status);

        $this->render('rooms/index', [
            'title' => 'Rooms Management - Regina Hotel',
            'rooms' => $rooms,
            'status' => $status
        ]);
    }

    public function show($id)
    {
        $this->requireLogin();

        $room = $this->roomModel->findById($id);
        if (!$room) {
            $this->flashMessage('error', 'Kamar tidak ditemukan.');
            $this->redirect('/rooms');
            return;
        }

        $this->render('rooms/detail', [
            'title' => 'Room Details - Regina Hotel',
            'room' => $room
        ]);
    }

    public function create()
    {
        $this->requireRole(['Owner', 'Admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->store();
        }

        return $this->showCreateForm();
    }

    public function store()
    {
        $this->requireRole(['Owner', 'Admin']);

        // Debug: Log that store method is called
        error_log("RoomController::store() called");
        error_log("POST data: " . print_r($_POST, true));

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

        if (empty($data['type_id']) || $data['type_id'] == 0) {
            $this->flashMessage('error', 'Tipe kamar harus dipilih.');
            return $this->showCreateForm();
        }

        if (empty($data['floor_id']) || $data['floor_id'] == 0) {
            $this->flashMessage('error', 'Lantai harus dipilih.');
            return $this->showCreateForm();
        }

        try {
            $result = $this->roomModel->create($data);
            error_log("Room created successfully: " . print_r($result, true));
            $this->flashMessage('success', 'Kamar berhasil ditambahkan.');
            $this->redirect('/rooms');
        } catch (Exception $e) {
            error_log("Error creating room: " . $e->getMessage());
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $this->flashMessage('error', 'Nomor kamar sudah digunakan.');
            } else {
                $this->flashMessage('error', 'Gagal menambahkan kamar: ' . $e->getMessage());
            }
            return $this->showCreateForm();
        }
    }

    public function edit($id)
    {
        $this->requireRole(['Owner', 'Admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->update($id);
        }

        return $this->showEditForm($id);
    }

    public function update($id)
    {
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

    public function delete($id)
    {
        $this->requireRole(['Owner', 'Admin']);

        try {
            // Check if room exists
            $room = $this->roomModel->findById($id);
            if (!$room) {
                $this->flashMessage('error', 'Kamar tidak ditemukan.');
                $this->redirect('/rooms');
            }

            // Check if room has active bookings
            $db = Database::getInstance();
            $activeBookings = $db->fetchOne(
                "SELECT COUNT(*) as count FROM booking_rooms br 
                 JOIN bookings b ON br.booking_id = b.id 
                 WHERE br.room_id = ? AND b.status IN ('Pending', 'CheckedIn')",
                [$id]
            );

            if ($activeBookings['count'] > 0) {
                $this->flashMessage('error', 'Tidak dapat menghapus kamar yang memiliki booking aktif.');
                $this->redirect('/rooms');
            }

            // Delete room
            $this->roomModel->delete($id);
            $this->flashMessage('success', 'Kamar berhasil dihapus.');
        } catch (Exception $e) {
            $this->flashMessage('error', 'Gagal menghapus kamar: ' . $e->getMessage());
        }

        $this->redirect('/rooms');
    }

    public function getAvailableRooms()
    {
        $this->requireLogin();

        $checkin_date = $_GET['checkin_date'] ?? '';
        $checkout_date = $_GET['checkout_date'] ?? '';

        if (empty($checkin_date) || empty($checkout_date)) {
            $this->json(['error' => 'Check-in and check-out dates are required'], 400);
            return;
        }

        try {
            $rooms = $this->roomModel->getAvailableRooms($checkin_date, $checkout_date);
            $this->json($rooms);
        } catch (Exception $e) {
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    private function showCreateForm()
    {
        $floors = $this->floorModel->getAll();
        $room_types = $this->getRoomTypes();

        $this->render('rooms/add', [
            'title' => 'Add New Room - Regina Hotel',
            'floors' => $floors,
            'room_types' => $room_types
        ]);
    }

    private function showEditForm($id)
    {
        $room = $this->roomModel->findById($id);
        if (!$room) {
            $this->flashMessage('error', 'Kamar tidak ditemukan.');
            $this->redirect('/rooms');
            return;
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

    private function getRoomTypes()
    {
        // Get room types from database
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM room_types ORDER BY type_name");
    }
}
