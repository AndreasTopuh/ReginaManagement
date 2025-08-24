<?php
class Floor {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        $sql = "SELECT f.*, 
                    COUNT(r.id) as actual_rooms,
                    SUM(CASE WHEN r.status = 'Available' THEN 1 ELSE 0 END) as available_rooms,
                    SUM(CASE WHEN r.status = 'Occupied' THEN 1 ELSE 0 END) as occupied_rooms,
                    SUM(CASE WHEN r.status = 'OutOfService' THEN 1 ELSE 0 END) as out_of_service_rooms
                FROM floors f 
                LEFT JOIN rooms r ON f.id = r.floor_id 
                GROUP BY f.id 
                ORDER BY f.floor_number ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM floors WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function getRoomsOnFloor($floor_id) {
        $sql = "SELECT r.*, rt.type_name, rt.price 
                FROM rooms r 
                JOIN room_types rt ON r.type_id = rt.id 
                WHERE r.floor_id = ? 
                ORDER BY r.room_number ASC";
        
        return $this->db->fetchAll($sql, [$floor_id]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO floors (floor_number) VALUES (?)";
        $this->db->execute($sql, [$data['floor_number']]);
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE floors SET floor_number = ? WHERE id = ?";
        return $this->db->execute($sql, [$data['floor_number'], $id]);
    }
    
    public function delete($id) {
        // Check if floor has rooms
        $sql = "SELECT COUNT(*) as room_count FROM rooms WHERE floor_id = ?";
        $result = $this->db->fetchOne($sql, [$id]);
        
        if ($result['room_count'] > 0) {
            return false; // Cannot delete floor with rooms
        }
        
        $sql = "DELETE FROM floors WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    public function exists($floor_number, $exclude_id = null) {
        $sql = "SELECT COUNT(*) as count FROM floors WHERE floor_number = ?";
        $params = [$floor_number];
        
        if ($exclude_id) {
            $sql .= " AND id != ?";
            $params[] = $exclude_id;
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['count'] > 0;
    }
}
