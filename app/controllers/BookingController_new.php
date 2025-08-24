<?php
class BookingController extends BaseController {
    private $bookingModel;
    private $guestModel;
    private $roomModel;
    
    public function __construct() {
        parent::__construct();
        $this->bookingModel = new Booking();
        $this->guestModel = new Guest();
        $this->roomModel = new Room();
    }
    
    public function index() {
        $this->requireLogin();
        
        $search = $_GET['search'] ?? '';
        $date_from = $_GET['date_from'] ?? '';
        $date_to = $_GET['date_to'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $bookings = $this->bookingModel->getAll($search, $date_from, $date_to, $status);
        
        $this->render('bookings/index', [
            'title' => 'Bookings Management - Regina Hotel',
            'bookings' => $bookings,
            'search' => $search,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'status' => $status
        ]);
    }
    
    public function create() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->store();
        }
        
        return $this->showCreateForm();
    }
    
    public function store() {
        return $this->handleCreateBooking();
    }
    
    public function show($id) {
        $this->requireLogin();
        
        $booking = $this->bookingModel->findById($id);
        if (!$booking) {
            $this->flashMessage('error', 'Booking tidak ditemukan.');
            $this->redirect('/bookings');
        }
        
        $booking_rooms = $this->bookingModel->getBookingRooms($id);
        
        $this->render('bookings/detail', [
            'title' => 'Booking Detail - Regina Hotel',
            'booking' => $booking,
            'booking_rooms' => $booking_rooms
        ]);
    }
    
    public function update($id) {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'update':
                    return $this->handleUpdateBooking($id);
                default:
                    $this->redirect("/bookings/$id");
            }
        }
        
        $this->redirect("/bookings/$id");
    }
    
    public function checkin($id) {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->bookingModel->checkIn($id);
                $this->flashMessage('success', 'Check-in berhasil.');
            } catch (Exception $e) {
                $this->flashMessage('error', 'Gagal check-in: ' . $e->getMessage());
            }
        }
        
        $this->redirect("/bookings/$id");
    }
    
    public function checkout($id) {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->bookingModel->checkOut($id);
                $this->flashMessage('success', 'Check-out berhasil.');
            } catch (Exception $e) {
                $this->flashMessage('error', 'Gagal check-out: ' . $e->getMessage());
            }
        }
        
        $this->redirect("/bookings/$id");
    }
    
    public function cancel($id) {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->bookingModel->cancel($id);
                $this->flashMessage('success', 'Booking berhasil dibatalkan.');
            } catch (Exception $e) {
                $this->flashMessage('error', 'Gagal membatalkan booking: ' . $e->getMessage());
            }
        }
        
        $this->redirect("/bookings/$id");
    }
    
    public function getStatistics() {
        $this->requireLogin();
        
        $date_from = $_GET['date_from'] ?? null;
        $date_to = $_GET['date_to'] ?? null;
        
        $statistics = $this->bookingModel->getStatistics($date_from, $date_to);
        
        $this->json($statistics);
    }
    
    private function handleCreateBooking() {
        try {
            // Guest data
            $guest_data = [
                'full_name' => trim($_POST['guest_name']),
                'id_type_id' => (int) $_POST['id_type_id'],
                'id_number' => trim($_POST['id_number']),
                'phone' => trim($_POST['phone']),
                'email' => trim($_POST['email'])
            ];
            
            // Booking data
            $booking_data = [
                'checkin_date' => $_POST['checkin_date'],
                'checkout_date' => $_POST['checkout_date'],
                'meal_plan' => $_POST['meal_plan'],
                'special_request' => trim($_POST['special_request'])
            ];
            
            // Room selection
            $selected_rooms = $_POST['selected_rooms'] ?? [];
            
            // Validation
            if (empty($guest_data['full_name']) || empty($selected_rooms)) {
                $this->flashMessage('error', 'Nama tamu dan kamar harus diisi.');
                return $this->showCreateForm();
            }
            
            // Create or get guest
            $existing_guest = $this->guestModel->findByIdNumber($guest_data['id_number']);
            if ($existing_guest) {
                $guest_id = $existing_guest['id'];
            } else {
                $guest_id = $this->guestModel->create($guest_data);
            }
            
            $booking_data['guest_id'] = $guest_id;
            
            // Prepare rooms data
            $rooms_data = [];
            foreach ($selected_rooms as $room_id) {
                $room = $this->roomModel->findById($room_id);
                if ($room) {
                    $rooms_data[] = [
                        'room_id' => $room_id,
                        'price_per_night' => $room['price_per_night']
                    ];
                }
            }
            
            // Create booking
            $booking_id = $this->bookingModel->create($booking_data, $rooms_data);
            
            $this->flashMessage('success', 'Booking berhasil dibuat.');
            $this->redirect("/bookings/$booking_id");
            
        } catch (Exception $e) {
            error_log("Booking creation error: " . $e->getMessage());
            $this->flashMessage('error', 'Gagal membuat booking: ' . $e->getMessage());
            return $this->showCreateForm();
        }
    }
    
    private function handleUpdateBooking($id) {
        try {
            $booking_data = [
                'checkin_date' => $_POST['checkin_date'],
                'checkout_date' => $_POST['checkout_date'],
                'meal_plan' => $_POST['meal_plan'],
                'special_request' => trim($_POST['special_request'])
            ];
            
            $this->bookingModel->update($id, $booking_data);
            $this->flashMessage('success', 'Booking berhasil diupdate.');
            
        } catch (Exception $e) {
            error_log("Booking update error: " . $e->getMessage());
            $this->flashMessage('error', 'Gagal mengupdate booking: ' . $e->getMessage());
        }
        
        $this->redirect("/bookings/$id");
    }
    
    private function showCreateForm() {
        $rooms = $this->roomModel->getAll('Available');
        
        $this->render('bookings/create', [
            'title' => 'Create New Booking - Regina Hotel',
            'rooms' => $rooms
        ]);
    }
}
