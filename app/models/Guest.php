<?php
class Guest {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        $sql = "SELECT g.*, it.type_name as id_type_name 
                FROM guests g 
                LEFT JOIN id_types it ON g.id_type_id = it.id 
                ORDER BY g.full_name ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    public function findById($id) {
        $sql = "SELECT g.*, it.type_name as id_type_name 
                FROM guests g 
                LEFT JOIN id_types it ON g.id_type_id = it.id 
                WHERE g.id = ?";
        
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function findByIdNumber($id_number) {
        $sql = "SELECT g.*, it.type_name as id_type_name 
                FROM guests g 
                LEFT JOIN id_types it ON g.id_type_id = it.id 
                WHERE g.id_number = ?";
        
        return $this->db->fetchOne($sql, [$id_number]);
    }
    
    public function create($data) {
        $sql = "INSERT INTO guests (full_name, id_type_id, id_number, phone, email) 
                VALUES (?, ?, ?, ?, ?)";
        
        $params = [
            $data['full_name'],
            $data['id_type_id'],
            $data['id_number'],
            $data['phone'],
            $data['email']
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE guests SET full_name = ?, id_type_id = ?, id_number = ?, phone = ?, email = ? 
                WHERE id = ?";
        
        $params = [
            $data['full_name'],
            $data['id_type_id'],
            $data['id_number'],
            $data['phone'],
            $data['email'],
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    public function getIdTypes() {
        $sql = "SELECT * FROM id_types ORDER BY type_name ASC";
        return $this->db->fetchAll($sql);
    }
    
    public function search($keyword) {
        $sql = "SELECT g.*, it.type_name as id_type_name 
                FROM guests g 
                LEFT JOIN id_types it ON g.id_type_id = it.id 
                WHERE g.full_name LIKE ? OR g.phone LIKE ? OR g.email LIKE ? OR g.id_number LIKE ?
                ORDER BY g.full_name ASC";
        
        $search = '%' . $keyword . '%';
        return $this->db->fetchAll($sql, [$search, $search, $search, $search]);
    }
}
