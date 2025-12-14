<?php
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/BaseService.php';

class UserService extends BaseService {
    
    public function __construct() {
        $this->dao = new UserDao();
    }

    protected function validateData($data, $isUpdate = false) {
        if (!$isUpdate) {
            if (empty($data['username'])) {
                throw new Exception("Username is required");
            }
            if (empty($data['email'])) {
                throw new Exception("Email is required");
            }
            if (empty($data['password'])) {
                throw new Exception("Password is required");
            }
        }
        
        if (isset($data['username']) && strlen($data['username']) > 100) {
            throw new Exception("Username must not exceed 100 characters");
        }
        
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        if (isset($data['password']) && strlen($data['password']) < 6) {
            throw new Exception("Password must be at least 6 characters");
        }
        
        if (isset($data['role']) && !in_array($data['role'], ['admin', 'user'])) {
            throw new Exception("Invalid role. Must be 'admin' or 'user'");
        }
    }

    protected function getIdColumn() {
        return 'user_id';
    }

    public function getUserByEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        try {
            return $this->dao->getUserByEmail($email);
        } catch (Exception $e) {
            throw new Exception("Error fetching user by email: " . $e->getMessage());
        }
    }

    public function getUserByUsername($username) {
        try {
            return $this->dao->getUserByUsername($username);
        } catch (Exception $e) {
            throw new Exception("Error fetching user by username: " . $e->getMessage());
        }
    }

    public function register($data) {
        $this->validateData($data);
        
        if ($this->dao->getUserByEmail($data['email'])) {
            throw new Exception("Email already exists");
        }
        
        if ($this->dao->getUserByUsername($data['username'])) {
            throw new Exception("Username already exists");
        }
        
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['registration_date'] = date('Y-m-d');
        $data['role'] = $data['role'] ?? 'user';
        
        return $this->dao->add($data);
    }

    public function login($username, $password) {
        $user = $this->dao->getUserByUsername($username);
        
        if (!$user) {
            throw new Exception("Invalid credentials");
        }
        
        if (!password_verify($password, $user['password'])) {
            throw new Exception("Invalid credentials");
        }
        
        return $user;
    }
}
