<?php
require_once __DIR__ . '/../dao/UserDao.php';

class UserService
{
    private $dao;

    public function __construct()
    {
        $this->dao = new UserDao();
    }

    public function getAll()
    {
        return $this->dao->getAll();
    }

    public function getById($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new InvalidArgumentException("Invalid user id");
        }

        $user = $this->dao->getById($id);
        if (!$user) {
            throw new RuntimeException("User not found");
        }

        return $user;
    }

    public function create($data)
    {
        // Adjust field names to your schema if needed
        $required = ['name', 'email', 'password', 'role'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("$field is required");
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format");
        }

        if (strlen($data['password']) < 6) {
            throw new InvalidArgumentException("Password must be at least 6 characters");
        }

        if (strlen($data['name']) > 100) {
            throw new InvalidArgumentException("Name too long (max 100 chars)");
        }

        // In a real app you’d hash the password; for the assignment you can keep plain text or add hashing here
        return $this->dao->add([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
            'role'     => $data['role'],
        ]);
    }

    public function update($id, $data)
    {
        $this->getById($id); // ensure user exists

        $allowed = ['name', 'email', 'password', 'role'];
        $entity  = [];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                if ($field === 'email' && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    throw new InvalidArgumentException("Invalid email format");
                }
                if ($field === 'password' && strlen($data[$field]) < 6) {
                    throw new InvalidArgumentException("Password must be at least 6 characters");
                }
                if ($field === 'name' && strlen($data[$field]) > 100) {
                    throw new InvalidArgumentException("Name too long (max 100 chars)");
                }
                $entity[$field] = $data[$field];
            }
        }

        if (empty($entity)) {
            throw new InvalidArgumentException("No valid fields to update");
        }

        return $this->dao->update($entity, $id);
    }

    public function delete($id)
    {
        $this->getById($id); // will throw if not found
        $this->dao->delete($id);
        return true;
    }

    public function getByEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format");
        }

        $user = $this->dao->getUserByEmail($email);
        if (!$user) {
            throw new RuntimeException("User not found");
        }

        return $user;
    }
}
