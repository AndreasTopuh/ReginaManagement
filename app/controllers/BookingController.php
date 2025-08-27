<?php
class BookingController extends BaseController
{
    private $bookingModel;
    private $guestModel;
    private $roomModel;

    public function __construct()
    {
        parent::__construct();
        $this->bookingModel = new Booking();
        $this->guestModel = new Guest();
        $this->roomModel = new Room();
    }

    public function index()
    {
        $this->requireLogin();

        $search = $_GET['search'] ?? '';
        $checkin_from = $_GET['checkin_from'] ?? '';
        $checkout_to = $_GET['checkout_to'] ?? '';
        $status = $_GET['status'] ?? '';
        $sort = $_GET['sort'] ?? '';

        $bookings = $this->bookingModel->getAll($search, $checkin_from, $checkout_to, $status, $sort);

        $this->render('bookings/index', [
            'title' => 'Bookings Management - Regina Hotel',
            'bookings' => $bookings,
            'search' => $search,
            'checkin_from' => $checkin_from,
            'checkout_to' => $checkout_to,
            'status' => $status,
            'sort' => $sort
        ]);
    }

    public function create()
    {
        $this->requireLogin();

        return $this->showCreateForm();
    }

    public function store()
    {
        $this->requireLogin();
        return $this->handleCreateBooking();
    }

    public function show($id)
    {
        $this->requireLogin();

        // Handle POST actions (check-in, check-out, cancel)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            switch ($action) {
                case 'checkin':
                    try {
                        $this->bookingModel->checkIn($id);
                        $this->flashMessage('success', 'Check-in berhasil.');
                    } catch (Exception $e) {
                        $this->flashMessage('error', 'Gagal check-in: ' . $e->getMessage());
                    }
                    $this->redirect("/bookings/$id");
                    return;

                case 'checkout':
                    try {
                        $this->bookingModel->checkOut($id);
                        $this->flashMessage('success', 'Check-out berhasil.');
                    } catch (Exception $e) {
                        $this->flashMessage('error', 'Gagal check-out: ' . $e->getMessage());
                    }
                    $this->redirect("/bookings/$id");
                    return;

                case 'cancel':
                    try {
                        $this->bookingModel->cancel($id);
                        $this->flashMessage('success', 'Booking berhasil dibatalkan.');
                    } catch (Exception $e) {
                        $this->flashMessage('error', 'Gagal membatalkan booking: ' . $e->getMessage());
                    }
                    $this->redirect("/bookings/$id");
                    return;

                case 'update':
                    return $this->handleUpdateBooking($id);
            }
        }

        try {
            $booking = $this->bookingModel->findById($id);
            if (!$booking) {
                $this->flashMessage('error', 'Booking tidak ditemukan.');
                $this->redirect('/bookings');
                return;
            }

            $booking_rooms = $this->bookingModel->getBookingRooms($id);
            $booking_history = $this->bookingModel->getHistory($id);

            $this->render('bookings/detail', [
                'title' => 'Booking Detail - Regina Hotel',
                'booking' => $booking,
                'booking_rooms' => $booking_rooms,
                'booking_history' => $booking_history
            ]);
        } catch (Exception $e) {
            if (APP_DEBUG) {
                echo "<h3>Error in BookingController@show:</h3>";
                echo "<p>Error: " . $e->getMessage() . "</p>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
            } else {
                $this->flashMessage('error', 'Terjadi kesalahan saat memuat booking.');
                $this->redirect('/bookings');
            }
        }
    }

    public function update($id)
    {
        $this->requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            switch ($action) {
                case 'update':
                    return $this->handleUpdateBooking($id);
                case 'checkin':
                    try {
                        $this->bookingModel->checkIn($id);
                        $this->flashMessage('success', 'Check-in berhasil.');
                    } catch (Exception $e) {
                        $this->flashMessage('error', 'Gagal check-in: ' . $e->getMessage());
                    }
                    break;
                case 'checkout':
                    try {
                        $this->bookingModel->checkOut($id);
                        $this->flashMessage('success', 'Check-out berhasil.');
                    } catch (Exception $e) {
                        $this->flashMessage('error', 'Gagal check-out: ' . $e->getMessage());
                    }
                    break;
                case 'cancel':
                    try {
                        $this->bookingModel->cancel($id);
                        $this->flashMessage('success', 'Booking berhasil dibatalkan.');
                    } catch (Exception $e) {
                        $this->flashMessage('error', 'Gagal membatalkan booking: ' . $e->getMessage());
                    }
                    break;
                default:
                    $this->redirect("/bookings/$id");
            }
        }

        $this->redirect("/bookings/$id");
    }

    public function checkin($id)
    {
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

    public function checkout($id)
    {
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

    public function cancel($id)
    {
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

    public function getStatistics()
    {
        $this->requireLogin();

        $date_from = $_GET['date_from'] ?? null;
        $date_to = $_GET['date_to'] ?? null;

        $statistics = $this->bookingModel->getStatistics($date_from, $date_to);

        $this->json($statistics);
    }

    private function handleCreateBooking()
    {
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

            // Double booking validation
            $available_rooms = $this->roomModel->getAvailableRooms($booking_data['checkin_date'], $booking_data['checkout_date']);
            $available_room_ids = array_column($available_rooms, 'id');

            foreach ($selected_rooms as $room_id) {
                if (!in_array($room_id, $available_room_ids)) {
                    $room = $this->roomModel->findById($room_id);
                    $room_number = $room ? $room['room_number'] : $room_id;

                    // Log double booking attempt
                    error_log("DOUBLE BOOKING ATTEMPT: User {$_SESSION['user_id']} tried to book room {$room_number} (ID: {$room_id}) for {$booking_data['checkin_date']} to {$booking_data['checkout_date']} but room is not available");

                    $this->flashMessage('error', "Kamar {$room_number} tidak tersedia untuk tanggal yang dipilih. Mungkin sudah dibooking oleh tamu lain.");
                    return $this->showCreateForm();
                }
            }

            // Create or get guest
            $guest_id = null;
            if (!empty($guest_data['id_number'])) {
                $existing_guest = $this->guestModel->findByIdNumber($guest_data['id_number']);
                if ($existing_guest) {
                    $guest_id = $existing_guest['id'];
                }
            }

            if (!$guest_id) {
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
                        'price_per_night' => $room['price'] // Use 'price' field from room_types table
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

    private function handleUpdateBooking($id)
    {
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

    private function showCreateForm()
    {
        $checkin_date = $_POST['checkin_date'] ?? $_GET['checkin_date'] ?? date('Y-m-d');
        $checkout_date = $_POST['checkout_date'] ?? $_GET['checkout_date'] ?? date('Y-m-d', strtotime('+1 day'));

        // Always use getAvailableRooms to check for conflicts
        $available_rooms = $this->roomModel->getAvailableRooms($checkin_date, $checkout_date);

        // Get ID types for guest form
        $id_types = $this->guestModel->getIdTypes();

        $this->render('bookings/create', [
            'title' => 'Create New Booking - Regina Hotel',
            'available_rooms' => $available_rooms,
            'id_types' => $id_types
        ]);
    }

    public function checkAvailability()
    {
        $this->requireLogin();

        $checkin_date = $_POST['checkin_date'] ?? '';
        $checkout_date = $_POST['checkout_date'] ?? '';

        if (empty($checkin_date) || empty($checkout_date)) {
            $this->json(['error' => 'Please select check-in and check-out dates']);
            return;
        }

        $available_rooms = $this->roomModel->getAvailableRooms($checkin_date, $checkout_date);

        $this->json([
            'success' => true,
            'rooms' => $available_rooms,
            'count' => count($available_rooms)
        ]);
    }
}
