<?php
class Room
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($status = null)
    {
        $sql = "SELECT r.*, rt.type_name, rt.price,
                       f.floor_number
                FROM rooms r 
                JOIN room_types rt ON r.type_id = rt.id 
                JOIN floors f ON r.floor_id = f.id";

        $params = [];
        if ($status) {
            $sql .= " WHERE r.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY r.room_number ASC";

        return $this->db->fetchAll($sql, $params);
    }

    public function findById($id)
    {
        $sql = "SELECT r.*, rt.type_name, rt.price,
                       f.floor_number
                FROM rooms r 
                JOIN room_types rt ON r.type_id = rt.id 
                JOIN floors f ON r.floor_id = f.id 
                WHERE r.id = ?";

        return $this->db->fetchOne($sql, [$id]);
    }

    public function getAvailableRooms($checkin_date, $checkout_date)
    {
        $sql = "SELECT r.*, rt.type_name, rt.price,
                       f.floor_number
                FROM rooms r 
                JOIN room_types rt ON r.type_id = rt.id 
                JOIN floors f ON r.floor_id = f.id 
                WHERE r.status IN ('Available', 'Clean') 
                AND r.id NOT IN (
                    SELECT DISTINCT br.room_id 
                    FROM booking_rooms br 
                    JOIN bookings b ON br.booking_id = b.id 
                    WHERE b.status NOT IN ('CheckedOut', 'Canceled') 
                    AND NOT (
                        -- No overlap conditions: new checkout <= existing checkin OR new checkin >= existing checkout
                        DATE(?) <= DATE(b.checkin_date) OR DATE(?) >= DATE(b.checkout_date)
                    )
                )
                ORDER BY rt.price ASC, r.room_number ASC";

        $params = [
            $checkout_date, // new checkout date
            $checkin_date   // new checkin date
        ];

        return $this->db->fetchAll($sql, $params);
    }
    public function getAllAvailableRooms()
    {
        $sql = "SELECT r.*, rt.type_name, rt.price,
                       f.floor_number
                FROM rooms r 
                JOIN room_types rt ON r.type_id = rt.id 
                JOIN floors f ON r.floor_id = f.id 
                WHERE r.status IN ('Available', 'available', 'Clean') 
                ORDER BY rt.price ASC, r.room_number ASC";

        return $this->db->fetchAll($sql);
    }

    public function create($data)
    {
        $sql = "INSERT INTO rooms (room_number, type_id, floor_id, description, features) 
                VALUES (?, ?, ?, ?, ?)";

        $params = [
            $data['room_number'],
            $data['type_id'],
            $data['floor_id'],
            $data['description'],
            $data['features']
        ];

        $result = $this->db->execute($sql, $params);

        // Update floor total_rooms
        $this->updateFloorTotalRooms($data['floor_id']);

        return $result;
    }

    public function update($id, $data)
    {
        $old_room = $this->findById($id);

        $sql = "UPDATE rooms SET room_number = ?, type_id = ?, floor_id = ?, description = ?, features = ?, status = ? 
                WHERE id = ?";

        $params = [
            $data['room_number'],
            $data['type_id'],
            $data['floor_id'],
            $data['description'],
            $data['features'],
            $data['status'] ?? 'Available',
            $id
        ];

        $result = $this->db->execute($sql, $params);

        // Update floor total_rooms for both old and new floor
        $this->updateFloorTotalRooms($old_room['floor_id']);
        if ($old_room['floor_id'] != $data['floor_id']) {
            $this->updateFloorTotalRooms($data['floor_id']);
        }

        return $result;
    }

    public function delete($id)
    {
        $room = $this->findById($id);

        $sql = "DELETE FROM rooms WHERE id = ?";
        $result = $this->db->execute($sql, [$id]);

        // Update floor total_rooms
        $this->updateFloorTotalRooms($room['floor_id']);

        return $result;
    }

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE rooms SET status = ? WHERE id = ?";
        return $this->db->execute($sql, [$status, $id]);
    }

    public function getTypes()
    {
        $sql = "SELECT * FROM room_types ORDER BY price ASC";
        return $this->db->fetchAll($sql);
    }

    public function getFloors()
    {
        $sql = "SELECT * FROM floors ORDER BY floor_number ASC";
        return $this->db->fetchAll($sql);
    }

    public function getStatistics()
    {
        $sql = "SELECT 
                    COUNT(*) as total_rooms,
                    SUM(CASE WHEN status = 'Available' THEN 1 ELSE 0 END) as available_rooms,
                    SUM(CASE WHEN status = 'Occupied' THEN 1 ELSE 0 END) as occupied_rooms,
                    SUM(CASE WHEN status = 'OutOfService' THEN 1 ELSE 0 END) as out_of_service_rooms
                FROM rooms";

        return $this->db->fetchOne($sql);
    }

    public function getRoomTypesWithDetails($status = null)
    {
        $sql = "SELECT 
                    rt.*,
                    ri.image_filename,
                    ri.image_title,
                    COUNT(r.id) as total_rooms,
                    SUM(CASE WHEN r.status = 'Available' THEN 1 ELSE 0 END) as available_rooms,
                    SUM(CASE WHEN r.status = 'Occupied' THEN 1 ELSE 0 END) as occupied_rooms,
                    SUM(CASE WHEN r.status = 'OutOfService' THEN 1 ELSE 0 END) as out_of_service_rooms
                FROM room_types rt
                LEFT JOIN room_images ri ON rt.id = ri.room_type_id AND ri.is_primary = 1
                LEFT JOIN rooms r ON rt.id = r.type_id";

        $params = [];
        if ($status) {
            $sql .= " AND r.status = ?";
            $params[] = $status;
        }

        $sql .= " GROUP BY rt.id, ri.image_filename, ri.image_title
                  ORDER BY rt.price ASC";

        return $this->db->fetchAll($sql, $params);
    }

    public function getRoomTypeDetails($typeId)
    {
        $sql = "SELECT 
                    rt.*,
                    ri.image_filename,
                    ri.image_title,
                    COUNT(r.id) as total_rooms,
                    SUM(CASE WHEN r.status = 'Available' THEN 1 ELSE 0 END) as available_rooms,
                    SUM(CASE WHEN r.status = 'Occupied' THEN 1 ELSE 0 END) as occupied_rooms,
                    SUM(CASE WHEN r.status = 'OutOfService' THEN 1 ELSE 0 END) as out_of_service_rooms
                FROM room_types rt
                LEFT JOIN room_images ri ON rt.id = ri.room_type_id AND ri.is_primary = 1
                LEFT JOIN rooms r ON rt.id = r.type_id
                WHERE rt.id = ?
                GROUP BY rt.id, ri.image_filename, ri.image_title";

        return $this->db->fetchOne($sql, [$typeId]);
    }

    public function getRoomsByType($typeId)
    {
        $sql = "SELECT r.*, rt.type_name, rt.price, f.floor_number
                FROM rooms r 
                JOIN room_types rt ON r.type_id = rt.id 
                JOIN floors f ON r.floor_id = f.id
                WHERE r.type_id = ?
                ORDER BY r.room_number ASC";

        return $this->db->fetchAll($sql, [$typeId]);
    }

    private function updateFloorTotalRooms($floor_id)
    {
        $sql = "UPDATE floors SET total_rooms = (
                    SELECT COUNT(*) FROM rooms WHERE floor_id = ?
                ) WHERE id = ?";

        $this->db->execute($sql, [$floor_id, $floor_id]);
    }
}
