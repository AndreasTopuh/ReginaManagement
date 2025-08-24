<?php
class BookingController
{
    private $bookingModel;
    private $guestModel;
    private $roomModel;

    public function __construct()
    {
        $this->bookingModel = new Booking();
        $this->guestModel = new Guest();
        $this->roomModel = new Room();
    }

    public function index()
    {
        requireLogin();

        $search = $_GET['search'] ?? '';
        $date_from = $_GET['date_from'] ?? '';
        $date_to = $_GET['date_to'] ?? '';
        $status = $_GET['status'] ?? '';

        $bookings = $this->bookingModel->getAll($search, $date_from, $date_to, $status);

        include APP_PATH . '/views/bookings/index.php';
    }

    public function create()
    {
        requireLogin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleCreateBooking();
        }

        return $this->showCreateForm();
    }

    public function view($id)
    {
        requireLogin();

        $booking = $this->bookingModel->findById($id);
        if (!$booking) {
            setFlashMessage('error', 'Booking tidak ditemukan.');
            redirect('/bookings.php');
            return;
        }

        try {
            $booking_rooms = $this->bookingModel->getBookingRooms($id);
        } catch (Exception $e) {
            setFlashMessage('error', 'Error mengambil data booking: ' . $e->getMessage());
            redirect('/bookings.php');
            return;
        }

        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Prevent duplicate submissions
            if (
                isset($_SESSION['last_booking_action']) &&
                $_SESSION['last_booking_action']['id'] == $id &&
                $_SESSION['last_booking_action']['time'] > (time() - 5)
            ) {
                redirect("/bookings_detail.php?id=$id");
                return;
            }

            $action = $_POST['action'] ?? '';

            try {
                switch ($action) {
                    case 'update':
                        return $this->handleUpdateBooking($id);

                    case 'checkin':
                        $this->bookingModel->checkIn($id);
                        setFlashMessage('success', 'Check-in berhasil.');
                        $_SESSION['last_booking_action'] = ['id' => $id, 'time' => time()];
                        redirect("/bookings_detail.php?id=$id");
                        return;

                    case 'checkout':
                        $this->bookingModel->checkOut($id);
                        setFlashMessage('success', 'Check-out berhasil.');
                        $_SESSION['last_booking_action'] = ['id' => $id, 'time' => time()];
                        redirect("/bookings_detail.php?id=$id");
                        return;

                    case 'cancel':
                        $this->bookingModel->cancel($id);
                        setFlashMessage('success', 'Booking berhasil dibatalkan.');
                        $_SESSION['last_booking_action'] = ['id' => $id, 'time' => time()];
                        redirect("/bookings_detail.php?id=$id");
                        return;
                }
            } catch (Exception $e) {
                setFlashMessage('error', 'Error melakukan aksi: ' . $e->getMessage());
                redirect("/bookings_detail.php?id=$id");
                return;
            }
        }

        include APP_PATH . '/views/bookings/detail.php';
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
            if (empty($guest_data['full_name'])) {
                throw new Exception('Nama tamu harus diisi.');
            }

            if (empty($selected_rooms)) {
                throw new Exception('Pilih minimal satu kamar.');
            }

            if ($booking_data['checkin_date'] >= $booking_data['checkout_date']) {
                throw new Exception('Tanggal checkout harus setelah checkin.');
            }

            // Check if guest exists
            $existing_guest = null;
            if (!empty($guest_data['id_number'])) {
                $existing_guest = $this->guestModel->findByIdNumber($guest_data['id_number']);
            }

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
                        'price_per_night' => $room['price']
                    ];
                }
            }

            $booking_id = $this->bookingModel->create($booking_data, $rooms_data);

            setFlashMessage('success', 'Booking berhasil dibuat.');
            redirect("/bookings_detail.php?id=$booking_id");
        } catch (Exception $e) {
            error_log("Booking creation error: " . $e->getMessage());
            setFlashMessage('error', 'Gagal membuat booking: ' . $e->getMessage());
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

            if ($booking_data['checkin_date'] >= $booking_data['checkout_date']) {
                throw new Exception('Tanggal checkout harus setelah checkin.');
            }

            $this->bookingModel->update($id, $booking_data);
            setFlashMessage('success', 'Booking berhasil diperbarui.');
        } catch (Exception $e) {
            setFlashMessage('error', 'Gagal memperbarui booking: ' . $e->getMessage());
        }

        redirect("/bookings_detail.php?id=$id");
    }

    private function showCreateForm()
    {
        $room_types = $this->roomModel->getTypes();
        $id_types = $this->guestModel->getIdTypes();

        // Get available rooms if dates are selected
        $available_rooms = [];
        $checkin_date = $_GET['checkin_date'] ?? $_POST['checkin_date'] ?? '';
        $checkout_date = $_GET['checkout_date'] ?? $_POST['checkout_date'] ?? '';

        if (!empty($checkin_date) && !empty($checkout_date)) {
            $available_rooms = $this->roomModel->getAvailableRooms($checkin_date, $checkout_date);
        } else {
            // Show all available rooms initially (without date filtering)
            $available_rooms = $this->roomModel->getAllAvailableRooms();
        }

        include APP_PATH . '/views/bookings/create.php';
    }
}
