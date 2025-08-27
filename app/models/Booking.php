<?php
class Booking
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($search = '', $checkin_from = '', $checkout_to = '', $status = '', $sort = '')
    {
        $sql = "SELECT b.*, g.full_name as guest_name, u.name as created_by_name, ro.role_name as created_by_role,
                    GROUP_CONCAT(r.room_number ORDER BY r.room_number SEPARATOR ', ') as room_numbers,
                    GROUP_CONCAT(DISTINCT FLOOR(r.room_number/100) ORDER BY FLOOR(r.room_number/100) SEPARATOR ', ') as floor_numbers
                FROM bookings b 
                JOIN guests g ON b.guest_id = g.id 
                JOIN users u ON b.created_by = u.id 
                JOIN roles ro ON u.role_id = ro.id
                LEFT JOIN booking_rooms br ON b.id = br.booking_id
                LEFT JOIN rooms r ON br.room_id = r.id";

        $conditions = [];
        $params = [];

        if (!empty($search)) {
            $conditions[] = "(g.full_name LIKE ? OR b.booking_code LIKE ?)";
            $search_param = '%' . $search . '%';
            $params[] = $search_param;
            $params[] = $search_param;
        }

        if (!empty($checkin_from)) {
            $conditions[] = "DATE(b.checkin_date) >= ?";
            $params[] = $checkin_from;
        }

        if (!empty($checkout_to)) {
            $conditions[] = "DATE(b.checkout_date) <= ?";
            $params[] = $checkout_to;
        }

        if (!empty($status)) {
            $conditions[] = "b.status = ?";
            $params[] = $status;
        }

        // Handle sorting filters that act as WHERE conditions
        if (!empty($sort)) {
            switch ($sort) {
                case 'created_owner':
                    $conditions[] = "ro.role_name = 'Owner'";
                    break;
                case 'created_admin':
                    $conditions[] = "ro.role_name = 'Admin'";
                    break;
                case 'created_receptionist':
                    $conditions[] = "ro.role_name = 'Receptionist'";
                    break;
                case 'floor_1':
                    $conditions[] = "r.room_number >= 100 AND r.room_number < 200";
                    break;
                case 'floor_2':
                    $conditions[] = "r.room_number >= 200 AND r.room_number < 300";
                    break;
                case 'floor_3':
                    $conditions[] = "r.room_number >= 300 AND r.room_number < 400";
                    break;
                case 'floor_4':
                    $conditions[] = "r.room_number >= 400 AND r.room_number < 500";
                    break;
                case 'floor_5':
                    $conditions[] = "r.room_number >= 500 AND r.room_number < 600";
                    break;
                case 'status_checkedout':
                    $conditions[] = "b.status = 'CheckedOut'";
                    break;
                case 'status_checkedin':
                    $conditions[] = "b.status = 'CheckedIn'";
                    break;
                case 'status_canceled':
                    $conditions[] = "b.status = 'Canceled'";
                    break;
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY b.id";

        // Handle ORDER BY for sorting
        if (!empty($sort)) {
            switch ($sort) {
                case 'newest':
                    $sql .= " ORDER BY b.created_at DESC";
                    break;
                case 'oldest':
                    $sql .= " ORDER BY b.created_at ASC";
                    break;
                case 'price_high':
                    $sql .= " ORDER BY b.grand_total DESC";
                    break;
                case 'price_low':
                    $sql .= " ORDER BY b.grand_total ASC";
                    break;
                case 'created_owner':
                case 'created_admin':
                case 'created_receptionist':
                    $sql .= " ORDER BY u.name ASC, b.created_at DESC";
                    break;
                case 'floor_1':
                case 'floor_2':
                case 'floor_3':
                case 'floor_4':
                case 'floor_5':
                    $sql .= " ORDER BY FLOOR(r.room_number/100) ASC, r.room_number ASC, b.created_at DESC";
                    break;
                case 'status_checkedout':
                case 'status_checkedin':
                case 'status_canceled':
                    $sql .= " ORDER BY b.status ASC, b.created_at DESC";
                    break;
                default:
                    $sql .= " ORDER BY b.created_at DESC";
                    break;
            }
        } else {
            $sql .= " ORDER BY b.created_at DESC";
        }

        return $this->db->fetchAll($sql, $params);
    }

    public function findById($id)
    {
        $sql = "SELECT b.*, g.full_name as guest_name, g.phone as guest_phone, g.email as guest_email,
                    g.id_number as guest_id_number, it.type_name as guest_id_type,
                    u.name as created_by_name
                FROM bookings b 
                JOIN guests g ON b.guest_id = g.id 
                LEFT JOIN id_types it ON g.id_type_id = it.id
                JOIN users u ON b.created_by = u.id 
                WHERE b.id = ?";

        return $this->db->fetchOne($sql, [$id]);
    }

    public function getBookingRooms($booking_id)
    {
        $sql = "SELECT br.*, r.room_number, rt.type_name, rt.price as price_per_night,
                       br.rate_per_night as booked_rate_per_night, f.floor_number
                FROM booking_rooms br 
                JOIN rooms r ON br.room_id = r.id 
                JOIN room_types rt ON r.type_id = rt.id 
                JOIN floors f ON r.floor_id = f.id
                WHERE br.booking_id = ? 
                ORDER BY r.room_number ASC";

        return $this->db->fetchAll($sql, [$booking_id]);
    }

    public function create($booking_data, $rooms_data)
    {
        try {
            // Start transaction
            $this->db->beginTransaction();

            // Generate booking code
            $booking_code = generateBookingCode();

            // Calculate duration
            $checkin = new DateTime($booking_data['checkin_date']);
            $checkout = new DateTime($booking_data['checkout_date']);
            $duration_nights = $checkout->diff($checkin)->days;

            // Insert booking
            $sql = "INSERT INTO bookings (booking_code, guest_id, checkin_date, checkout_date, duration_nights, 
                        meal_plan, special_request, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $params = [
                $booking_code,
                $booking_data['guest_id'],
                $booking_data['checkin_date'],
                $booking_data['checkout_date'],
                $duration_nights,
                $booking_data['meal_plan'],
                $booking_data['special_request'],
                SessionManager::getUserId()
            ];

            $this->db->execute($sql, $params);
            $booking_id = $this->db->lastInsertId();

            // Insert booking rooms
            $total_room_amount = 0;
            foreach ($rooms_data as $room) {
                $subtotal = $room['price_per_night'] * $duration_nights;
                $total_room_amount += $subtotal;

                $sql = "INSERT INTO booking_rooms (booking_id, room_id, rate_per_night, nights, subtotal) 
                        VALUES (?, ?, ?, ?, ?)";

                $params = [$booking_id, $room['room_id'], $room['price_per_night'], $duration_nights, $subtotal];
                $this->db->execute($sql, $params);
            }

            // Calculate totals
            $tax_rate = $booking_data['tax_rate'] ?? 10;
            $service_rate = $booking_data['service_rate'] ?? 5;
            $tax_amount = $total_room_amount * $tax_rate / 100;
            $service_amount = $total_room_amount * $service_rate / 100;
            $grand_total = $total_room_amount + $tax_amount + $service_amount;

            // Update booking totals
            $sql = "UPDATE bookings SET total_room_amount = ?, tax_rate = ?, service_rate = ?, 
                        tax_amount = ?, service_amount = ?, grand_total = ? 
                    WHERE id = ?";

            $params = [
                $total_room_amount,
                $tax_rate,
                $service_rate,
                $tax_amount,
                $service_amount,
                $grand_total,
                $booking_id
            ];

            $this->db->execute($sql, $params);

            // Add booking history entry
            $this->addHistory($booking_id, 'created', 'Booking dibuat dengan kode: ' . $booking_code);

            // Commit transaction
            $this->db->commit();

            return $booking_id;
        } catch (Exception $e) {
            // Rollback transaction
            $this->db->rollback();
            error_log("Booking creation error: " . $e->getMessage());
            throw $e;
        }
    }

    public function update($id, $booking_data)
    {
        try {
            $this->db->beginTransaction();

            // Calculate new duration
            $checkin = new DateTime($booking_data['checkin_date']);
            $checkout = new DateTime($booking_data['checkout_date']);
            $duration_nights = $checkout->diff($checkin)->days;

            // Update booking basic info
            $sql = "UPDATE bookings SET 
                        checkin_date = ?, checkout_date = ?, duration_nights = ?, 
                        meal_plan = ?, special_request = ?, updated_at = NOW()
                    WHERE id = ?";

            $params = [
                $booking_data['checkin_date'],
                $booking_data['checkout_date'],
                $duration_nights,
                $booking_data['meal_plan'],
                $booking_data['special_request'],
                $id
            ];

            $this->db->execute($sql, $params);

            // Recalculate booking rooms with new duration
            $this->recalculateBookingRooms($id, $duration_nights);

            // Recalculate booking totals
            $this->recalculateBookingTotals($id);

            // Add booking history entry
            $this->addHistory($id, 'updated', 'Booking diupdate - Duration: ' . $duration_nights . ' night(s)');

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Booking update error: " . $e->getMessage());
            throw $e;
        }
    }

    private function recalculateBookingRooms($booking_id, $duration_nights)
    {
        // Get all booking rooms
        $sql = "SELECT * FROM booking_rooms WHERE booking_id = ?";
        $booking_rooms = $this->db->fetchAll($sql, [$booking_id]);

        foreach ($booking_rooms as $room) {
            $new_subtotal = $room['rate_per_night'] * $duration_nights;

            $update_sql = "UPDATE booking_rooms SET 
                            nights = ?, subtotal = ?
                          WHERE booking_id = ? AND room_id = ?";

            $this->db->execute($update_sql, [
                $duration_nights,
                $new_subtotal,
                $booking_id,
                $room['room_id']
            ]);
        }
    }

    private function recalculateBookingTotals($booking_id)
    {
        // Get current booking data
        $booking = $this->findById($booking_id);

        // Calculate new total room amount
        $sql = "SELECT SUM(subtotal) as total_room_amount FROM booking_rooms WHERE booking_id = ?";
        $result = $this->db->fetchOne($sql, [$booking_id]);
        $total_room_amount = $result['total_room_amount'] ?? 0;

        // Use existing tax and service rates, or default values
        $tax_rate = $booking['tax_rate'] ?? 10;
        $service_rate = $booking['service_rate'] ?? 5;

        // Calculate tax and service amounts
        $tax_amount = $total_room_amount * $tax_rate / 100;
        $service_amount = $total_room_amount * $service_rate / 100;
        $grand_total = $total_room_amount + $tax_amount + $service_amount;

        // Update booking totals
        $update_sql = "UPDATE bookings SET 
                        total_room_amount = ?, total_service_amount = ?, 
                        tax_amount = ?, service_amount = ?, grand_total = ?
                      WHERE id = ?";

        $this->db->execute($update_sql, [
            $total_room_amount,
            $service_amount, // total_service_amount
            $tax_amount,
            $service_amount, // service_amount
            $grand_total,
            $booking_id
        ]);
    }

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE bookings SET status = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$status, $id]);
    }

    public function checkIn($id)
    {
        try {
            $this->db->beginTransaction();

            // Update booking status
            $this->updateStatus($id, 'CheckedIn');

            // Update room status to Occupied
            $sql = "UPDATE rooms r 
                    JOIN booking_rooms br ON r.id = br.room_id 
                    SET r.status = 'Occupied' 
                    WHERE br.booking_id = ?";

            $this->db->execute($sql, [$id]);

            // Add booking history entry
            $this->addHistory($id, 'checked_in', 'Guest melakukan check-in');

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Check-in error: " . $e->getMessage());
            throw $e;
        }
    }

    public function checkOut($id)
    {
        try {
            $this->db->beginTransaction();

            // Update booking status
            $this->updateStatus($id, 'CheckedOut');

            // Update room status to Available
            $sql = "UPDATE rooms r 
                    JOIN booking_rooms br ON r.id = br.room_id 
                    SET r.status = 'Available' 
                    WHERE br.booking_id = ?";

            $this->db->execute($sql, [$id]);

            // Add booking history entry
            $this->addHistory($id, 'checked_out', 'Guest melakukan check-out');

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Check-out error: " . $e->getMessage());
            throw $e;
        }
    }

    public function cancel($id)
    {
        try {
            $this->db->beginTransaction();

            // Update booking status
            $this->updateStatus($id, 'Canceled');

            // Update room status to Available (if not occupied by other bookings)
            $sql = "UPDATE rooms r 
                    JOIN booking_rooms br ON r.id = br.room_id 
                    SET r.status = 'Available' 
                    WHERE br.booking_id = ? 
                    AND r.id NOT IN (
                        SELECT DISTINCT br2.room_id 
                        FROM booking_rooms br2 
                        JOIN bookings b2 ON br2.booking_id = b2.id 
                        WHERE b2.status IN ('Pending', 'CheckedIn') 
                        AND b2.id != ?
                    )";

            $this->db->execute($sql, [$id, $id]);

            // Add booking history entry
            $this->addHistory($id, 'canceled', 'Booking dibatalkan');

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Booking cancellation error: " . $e->getMessage());
            throw $e;
        }
    }

    public function getStatistics($date_from = null, $date_to = null)
    {
        $where_clause = "";
        $params = [];

        if ($date_from && $date_to) {
            $where_clause = "WHERE DATE(b.created_at) BETWEEN ? AND ?";
            $params = [$date_from, $date_to];
        }

        $sql = "SELECT 
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN b.status = 'Pending' THEN 1 ELSE 0 END) as pending_bookings,
                    SUM(CASE WHEN b.status = 'CheckedIn' THEN 1 ELSE 0 END) as checkedin_bookings,
                    SUM(CASE WHEN b.status = 'CheckedOut' THEN 1 ELSE 0 END) as checkedout_bookings,
                    SUM(CASE WHEN b.status = 'Canceled' THEN 1 ELSE 0 END) as cancelled_bookings,
                    COALESCE(SUM(CASE WHEN b.status != 'Canceled' THEN b.grand_total ELSE 0 END), 0) as total_revenue
                FROM bookings b 
                $where_clause";

        return $this->db->fetchOne($sql, $params);
    }

    public function getRecentBookings($limit = 5)
    {
        $sql = "SELECT b.*, g.full_name as guest_name,
                    GROUP_CONCAT(r.room_number ORDER BY r.room_number SEPARATOR ', ') as room_numbers
                FROM bookings b 
                JOIN guests g ON b.guest_id = g.id 
                LEFT JOIN booking_rooms br ON b.id = br.booking_id
                LEFT JOIN rooms r ON br.room_id = r.id
                GROUP BY b.id 
                ORDER BY b.created_at DESC 
                LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }

    public function getRevenueSummary($date_from, $date_to)
    {
        $sql = "SELECT 
                    COUNT(*) as total_bookings,
                    COALESCE(SUM(CASE WHEN b.status != 'Canceled' THEN b.grand_total ELSE 0 END), 0) as total_revenue,
                    COALESCE(SUM(CASE WHEN b.status != 'Canceled' THEN b.total_room_amount ELSE 0 END), 0) as total_room_amount,
                    COALESCE(SUM(CASE WHEN b.status != 'Canceled' THEN b.tax_amount ELSE 0 END), 0) as total_tax,
                    COALESCE(SUM(CASE WHEN b.status != 'Canceled' THEN b.service_amount ELSE 0 END), 0) as total_service,
                    COALESCE(AVG(CASE WHEN b.status != 'Canceled' THEN b.grand_total ELSE NULL END), 0) as average_booking_value,
                    SUM(CASE WHEN b.status = 'CheckedOut' THEN 1 ELSE 0 END) as completed_bookings,
                    SUM(CASE WHEN b.status = 'CheckedIn' THEN 1 ELSE 0 END) as active_bookings,
                    SUM(CASE WHEN b.status = 'Pending' THEN 1 ELSE 0 END) as pending_bookings,
                    SUM(CASE WHEN b.status = 'Canceled' THEN 1 ELSE 0 END) as cancelled_bookings
                FROM bookings b 
                WHERE DATE(b.created_at) BETWEEN ? AND ?";

        return $this->db->fetchOne($sql, [$date_from, $date_to]);
    }

    public function getRevenueByPeriod($date_from, $date_to, $period = 'daily')
    {
        $date_format = $period === 'monthly' ? '%Y-%m' : '%Y-%m-%d';

        $sql = "SELECT 
                    DATE_FORMAT(b.created_at, '$date_format') as period,
                    COUNT(*) as total_bookings,
                    COALESCE(SUM(b.grand_total), 0) as total_revenue,
                    COALESCE(AVG(b.grand_total), 0) as average_revenue
                FROM bookings b 
                WHERE DATE(b.created_at) BETWEEN ? AND ?
                AND b.status != 'Canceled'
                GROUP BY DATE_FORMAT(b.created_at, '$date_format')
                ORDER BY period ASC";

        return $this->db->fetchAll($sql, [$date_from, $date_to]);
    }

    public function getRevenueByRoomType($date_from, $date_to)
    {
        $sql = "SELECT 
                    rt.type_name,
                    COUNT(DISTINCT b.id) as total_bookings,
                    COALESCE(SUM(br.subtotal), 0) as room_revenue,
                    COALESCE(SUM(b.grand_total), 0) as total_revenue,
                    COALESCE(AVG(br.rate_per_night), 0) as average_rate
                FROM bookings b
                JOIN booking_rooms br ON b.id = br.booking_id
                JOIN rooms r ON br.room_id = r.id
                JOIN room_types rt ON r.type_id = rt.id
                WHERE DATE(b.created_at) BETWEEN ? AND ?
                AND b.status != 'Canceled'
                GROUP BY rt.id, rt.type_name
                ORDER BY total_revenue DESC";

        return $this->db->fetchAll($sql, [$date_from, $date_to]);
    }

    public function getRevenueByMonth($months = 12)
    {
        $sql = "SELECT 
                    DATE_FORMAT(b.created_at, '%Y-%m') as month,
                    MONTHNAME(b.created_at) as month_name,
                    YEAR(b.created_at) as year,
                    COUNT(*) as total_bookings,
                    COALESCE(SUM(b.grand_total), 0) as total_revenue
                FROM bookings b 
                WHERE b.created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
                AND b.status != 'Canceled'
                GROUP BY DATE_FORMAT(b.created_at, '%Y-%m'), MONTHNAME(b.created_at), YEAR(b.created_at)
                ORDER BY month DESC";

        return $this->db->fetchAll($sql, [$months]);
    }

    public function getTopRevenueRooms($date_from, $date_to, $limit = 10)
    {
        $sql = "SELECT 
                    r.room_number,
                    rt.type_name,
                    f.floor_number,
                    COUNT(DISTINCT b.id) as total_bookings,
                    COALESCE(SUM(br.subtotal), 0) as room_revenue,
                    COALESCE(AVG(br.rate_per_night), 0) as average_rate
                FROM bookings b
                JOIN booking_rooms br ON b.id = br.booking_id
                JOIN rooms r ON br.room_id = r.id
                JOIN room_types rt ON r.type_id = rt.id
                JOIN floors f ON r.floor_id = f.id
                WHERE DATE(b.created_at) BETWEEN ? AND ?
                AND b.status != 'Canceled'
                GROUP BY r.id, r.room_number, rt.type_name, f.floor_number
                ORDER BY room_revenue DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$date_from, $date_to, $limit]);
    }

    public function getBookingStatistics($date_from, $date_to)
    {
        $sql = "SELECT 
                    COUNT(*) as total_bookings,
                    AVG(b.duration_nights) as average_stay_duration,
                    SUM(b.duration_nights) as total_nights,
                    COUNT(DISTINCT b.guest_id) as unique_guests,
                    MIN(b.grand_total) as min_booking_value,
                    MAX(b.grand_total) as max_booking_value,
                    AVG(DATEDIFF(b.created_at, b.checkin_date)) as average_booking_lead_time
                FROM bookings b 
                WHERE DATE(b.created_at) BETWEEN ? AND ?
                AND b.status != 'Canceled'";

        return $this->db->fetchOne($sql, [$date_from, $date_to]);
    }

    public function getPaymentMethodStats($date_from, $date_to)
    {
        // For now return empty array since we don't have payment_method column yet
        // This can be implemented when payment methods are added to the system
        return [];
    }

    public function getDetailedRevenue($date_from, $date_to, $status = '', $room_type = '', $sort_by = 'created_at', $sort_order = 'DESC')
    {
        $where_conditions = ["DATE(b.created_at) BETWEEN ? AND ?"];
        $params = [$date_from, $date_to];

        if (!empty($status)) {
            $where_conditions[] = "b.status = ?";
            $params[] = $status;
        }

        if (!empty($room_type)) {
            $where_conditions[] = "rt.type_name = ?";
            $params[] = $room_type;
        }

        $where_clause = implode(' AND ', $where_conditions);

        // Validate sort parameters
        $allowed_sort_fields = ['created_at', 'checkin_date', 'grand_total', 'booking_code', 'guest_name'];
        $sort_by = in_array($sort_by, $allowed_sort_fields) ? $sort_by : 'created_at';
        $sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';

        $sql = "SELECT 
                    b.*,
                    g.full_name as guest_name,
                    GROUP_CONCAT(DISTINCT r.room_number ORDER BY r.room_number SEPARATOR ', ') as room_numbers,
                    GROUP_CONCAT(DISTINCT rt.type_name ORDER BY rt.type_name SEPARATOR ', ') as room_types
                FROM bookings b
                JOIN guests g ON b.guest_id = g.id
                LEFT JOIN booking_rooms br ON b.id = br.booking_id
                LEFT JOIN rooms r ON br.room_id = r.id
                LEFT JOIN room_types rt ON r.type_id = rt.id
                WHERE $where_clause
                GROUP BY b.id
                ORDER BY $sort_by $sort_order";

        return $this->db->fetchAll($sql, $params);
    }

    public function getRevenueTotals($date_from, $date_to, $status = '', $room_type = '')
    {
        $where_conditions = ["DATE(b.created_at) BETWEEN ? AND ?"];
        $params = [$date_from, $date_to];

        if (!empty($status)) {
            $where_conditions[] = "b.status = ?";
            $params[] = $status;
        }

        if (!empty($room_type)) {
            $where_conditions[] = "rt.type_name = ?";
            $params[] = $room_type;
        }

        $where_clause = implode(' AND ', $where_conditions);

        $sql = "SELECT 
                    COUNT(DISTINCT b.id) as total_records,
                    COALESCE(SUM(b.total_room_amount), 0) as total_room_amount,
                    COALESCE(SUM(b.tax_amount), 0) as total_tax,
                    COALESCE(SUM(b.service_amount), 0) as total_service,
                    COALESCE(SUM(b.grand_total), 0) as grand_total
                FROM bookings b
                LEFT JOIN booking_rooms br ON b.id = br.booking_id
                LEFT JOIN rooms r ON br.room_id = r.id
                LEFT JOIN room_types rt ON r.type_id = rt.id
                WHERE $where_clause";

        return $this->db->fetchOne($sql, $params);
    }

    public function getRevenueChartData($period, $date_from, $date_to)
    {
        $revenue_data = $this->getRevenueByPeriod($date_from, $date_to, $period);

        $labels = [];
        $revenues = [];
        $bookings = [];

        foreach ($revenue_data as $data) {
            $labels[] = $data['period'];
            $revenues[] = (float)$data['total_revenue'];
            $bookings[] = (int)$data['total_bookings'];
        }

        return [
            'labels' => $labels,
            'revenue' => $revenues,
            'bookings' => $bookings
        ];
    }

    public function getOccupancyStats($date_from, $date_to)
    {
        $sql = "SELECT 
                    COUNT(DISTINCT br.room_id) as occupied_rooms,
                    (SELECT COUNT(*) FROM rooms) as total_rooms,
                    ROUND(COUNT(DISTINCT br.room_id) * 100.0 / (SELECT COUNT(*) FROM rooms), 2) as occupancy_rate,
                    AVG(DATEDIFF(b.checkout_date, b.checkin_date)) as avg_stay_duration
                FROM bookings b
                JOIN booking_rooms br ON b.id = br.booking_id
                WHERE b.checkin_date BETWEEN ? AND ?
                AND b.status IN ('CheckedIn', 'CheckedOut')";

        $result = $this->db->fetchOne($sql, [$date_from, $date_to]);

        // Ensure we return valid data even if no results
        return $result ?: [
            'occupied_rooms' => 0,
            'total_rooms' => 0,
            'occupancy_rate' => 0,
            'avg_stay_duration' => 0
        ];
    }

    public function getRevenueGrowth($date_from, $date_to)
    {
        // Calculate previous period for comparison
        $period_diff = strtotime($date_to) - strtotime($date_from);
        $prev_date_to = date('Y-m-d', strtotime($date_from) - 1);
        $prev_date_from = date('Y-m-d', strtotime($date_from) - $period_diff - 1);

        $sql = "SELECT 
                    COALESCE(SUM(grand_total), 0) as revenue
                FROM bookings 
                WHERE checkin_date BETWEEN ? AND ?
                AND status != 'Canceled'";

        // Current period revenue
        $current_result = $this->db->fetchOne($sql, [$date_from, $date_to]);
        $current_revenue = $current_result['revenue'] ?? 0;

        // Previous period revenue
        $previous_result = $this->db->fetchOne($sql, [$prev_date_from, $prev_date_to]);
        $previous_revenue = $previous_result['revenue'] ?? 0;

        $growth_rate = 0;
        if ($previous_revenue > 0) {
            $growth_rate = (($current_revenue - $previous_revenue) / $previous_revenue) * 100;
        }

        return [
            'current_revenue' => $current_revenue,
            'previous_revenue' => $previous_revenue,
            'growth_amount' => $current_revenue - $previous_revenue,
            'growth_rate' => round($growth_rate, 2)
        ];
    }

    /**
     * Add booking history entry
     */
    public function addHistory($booking_id, $action_type, $description = null, $action_by = null)
    {
        if ($action_by === null) {
            $action_by = $_SESSION['user_id'] ?? 1; // Default to admin if no session
        }

        $sql = "INSERT INTO booking_history (booking_id, action_type, action_description, action_by) 
                VALUES (?, ?, ?, ?)";

        return $this->db->execute($sql, [$booking_id, $action_type, $description, $action_by]);
    }

    /**
     * Get booking history
     */
    public function getHistory($booking_id)
    {
        $sql = "SELECT bh.*, u.name as action_by_name 
                FROM booking_history bh
                JOIN users u ON bh.action_by = u.id
                WHERE bh.booking_id = ?
                ORDER BY bh.action_at ASC";

        return $this->db->query($sql, [$booking_id])->fetchAll();
    }
}
