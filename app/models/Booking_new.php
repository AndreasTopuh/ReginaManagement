<?php
class Booking {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($search = '', $date_from = '', $date_to = '', $status = '') {
        $sql = "SELECT b.*, g.full_name as guest_name, u.name as created_by_name,
                    GROUP_CONCAT(r.room_number ORDER BY r.room_number SEPARATOR ', ') as room_numbers
                FROM bookings b 
                JOIN guests g ON b.guest_id = g.id 
                JOIN users u ON b.created_by = u.id 
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
        
        if (!empty($date_from)) {
            $conditions[] = "DATE(b.checkin_date) >= ?";
            $params[] = $date_from;
        }
        
        if (!empty($date_to)) {
            $conditions[] = "DATE(b.checkout_date) <= ?";
            $params[] = $date_to;
        }
        
        if (!empty($status)) {
            $conditions[] = "b.status = ?";
            $params[] = $status;
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " GROUP BY b.id ORDER BY b.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function findById($id) {
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
    
    public function getBookingRooms($booking_id) {
        $sql = "SELECT br.*, r.room_number, rt.type_name, rt.price as price_per_night,
                       br.price_per_night as booked_price_per_night, f.floor_number
                FROM booking_rooms br 
                JOIN rooms r ON br.room_id = r.id 
                JOIN room_types rt ON r.type_id = rt.id 
                JOIN floors f ON r.floor_id = f.id
                WHERE br.booking_id = ? 
                ORDER BY r.room_number ASC";
        
        return $this->db->fetchAll($sql, [$booking_id]);
    }
    
    public function create($booking_data, $rooms_data) {
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
                
                $sql = "INSERT INTO booking_rooms (booking_id, room_id, price_per_night, nights, subtotal) 
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
    
    public function update($id, $booking_data) {
        try {
            $sql = "UPDATE bookings SET 
                        checkin_date = ?, checkout_date = ?, meal_plan = ?, 
                        special_request = ?, updated_at = NOW()
                    WHERE id = ?";
            
            $params = [
                $booking_data['checkin_date'],
                $booking_data['checkout_date'],
                $booking_data['meal_plan'],
                $booking_data['special_request'],
                $id
            ];
            
            return $this->db->execute($sql, $params);
        } catch (Exception $e) {
            error_log("Booking update error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function updateStatus($id, $status) {
        $sql = "UPDATE bookings SET status = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$status, $id]);
    }
    
    public function checkIn($id) {
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
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Check-in error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function checkOut($id) {
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
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Check-out error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function cancel($id) {
        try {
            $this->db->beginTransaction();
            
            // Update booking status
            $this->updateStatus($id, 'Cancelled');
            
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
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Booking cancellation error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function getStatistics($date_from = null, $date_to = null) {
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
                    SUM(CASE WHEN b.status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled_bookings,
                    COALESCE(SUM(b.grand_total), 0) as total_revenue
                FROM bookings b 
                $where_clause";
        
        return $this->db->fetchOne($sql, $params);
    }
    
    public function getRecentBookings($limit = 5) {
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
}
