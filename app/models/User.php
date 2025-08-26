<?php
class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function authenticate($username, $password)
    {
        $sql = "SELECT u.*, r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.username = ? AND u.status = 1";

        $user = $this->db->fetchOne($sql, [$username]);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    public function findById($id)
    {
        $sql = "SELECT u.*, r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.id = ?";

        return $this->db->fetchOne($sql, [$id]);
    }

    public function findByUsername($username)
    {
        $sql = "SELECT u.*, r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.username = ?";

        return $this->db->fetchOne($sql, [$username]);
    }

    // Alias for consistency with UserController
    public function getUserById($id)
    {
        return $this->findById($id);
    }

    public function getAll()
    {
        $sql = "SELECT u.*, r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                ORDER BY u.name ASC";

        return $this->db->fetchAll($sql);
    }

    // Alias for consistency with UserController  
    public function getAllUsers()
    {
        return $this->getAll();
    }

    public function getUserByUsername($username)
    {
        $sql = "SELECT u.*, r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.username = ?";

        return $this->db->fetchOne($sql, [$username]);
    }

    public function getUserByEmail($email)
    {
        $sql = "SELECT u.*, r.role_name 
                FROM users u 
                JOIN roles r ON u.role_id = r.id 
                WHERE u.email = ?";

        return $this->db->fetchOne($sql, [$email]);
    }

    public function createUser($data)
    {
        try {
            $this->db->beginTransaction();

            // Get role_id based on role name
            $role_id = $this->getRoleIdByName($data['role']);
            if (!$role_id) {
                throw new Exception("Invalid role specified.");
            }

            $sql = "INSERT INTO users (name, username, email, phone, password, role_id, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, 1, NOW())";

            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

            $params = [
                $data['full_name'],
                $data['username'],
                $data['email'],
                $data['phone'],
                $password_hash,
                $role_id
            ];

            $result = $this->db->execute($sql, $params);

            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function updateUser($data)
    {
        try {
            $this->db->beginTransaction();

            // Get role_id based on role name
            $role_id = $this->getRoleIdByName($data['role']);
            if (!$role_id) {
                throw new Exception("Invalid role specified.");
            }

            // Build SQL based on whether password is being updated
            if (isset($data['password']) && !empty($data['password'])) {
                $sql = "UPDATE users SET name = ?, username = ?, email = ?, phone = ?, 
                        password = ?, role_id = ?, status = ?, updated_at = NOW() 
                        WHERE id = ?";

                $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

                $params = [
                    $data['full_name'],
                    $data['username'],
                    $data['email'],
                    $data['phone'],
                    $password_hash,
                    $role_id,
                    $data['status'],
                    $data['id']
                ];
            } else {
                $sql = "UPDATE users SET name = ?, username = ?, email = ?, phone = ?, 
                        role_id = ?, status = ?, updated_at = NOW() 
                        WHERE id = ?";

                $params = [
                    $data['full_name'],
                    $data['username'],
                    $data['email'],
                    $data['phone'],
                    $role_id,
                    $data['status'],
                    $data['id']
                ];
            }

            $result = $this->db->execute($sql, $params);

            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function deleteUser($user_id)
    {
        $sql = "DELETE FROM users WHERE id = ?";
        return $this->db->execute($sql, [$user_id]);
    }

    public function toggleUserStatus($user_id)
    {
        $sql = "UPDATE users SET status = CASE WHEN status = 1 THEN 0 ELSE 1 END, 
                updated_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$user_id]);
    }

    private function getRoleIdByName($role_name)
    {
        $sql = "SELECT id FROM roles WHERE role_name = ?";
        $result = $this->db->fetchOne($sql, [$role_name]);
        return $result ? $result['id'] : null;
    }

    public function getRoles()
    {
        $sql = "SELECT * FROM roles ORDER BY id ASC";
        return $this->db->fetchAll($sql);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE users SET ";
        $fields = [];
        $params = [];

        if (isset($data['name'])) {
            $fields[] = "name = ?";
            $params[] = $data['name'];
        }

        if (isset($data['username'])) {
            $fields[] = "username = ?";
            $params[] = $data['username'];
        }

        if (isset($data['password'])) {
            $fields[] = "password = ?";
            $params[] = $data['password'];
        }

        if (isset($data['role_id'])) {
            $fields[] = "role_id = ?";
            $params[] = $data['role_id'];
        }

        $sql .= implode(", ", $fields);
        $sql .= " WHERE id = ?";
        $params[] = $id;

        return $this->db->execute($sql, $params);
    }

    public function updateStatus($id, $status)
    {
        $sql = "UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$status, $id]);
    }

    // Additional utility methods for better consistency
    public function findByEmail($email)
    {
        return $this->getUserByEmail($email);
    }

    public function exists($field, $value, $exclude_id = null)
    {
        $sql = "SELECT id FROM users WHERE $field = ?";
        $params = [$value];

        if ($exclude_id) {
            $sql .= " AND id != ?";
            $params[] = $exclude_id;
        }

        $result = $this->db->fetchOne($sql, $params);
        return !empty($result);
    }
}
