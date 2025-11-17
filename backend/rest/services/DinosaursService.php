<?php
require_once __DIR__ . '/../dao/DinosaursDao.php';

class DinosaursService
{
    private $dao;

    public function __construct()
    {
        $this->dao = new DinosaursDao();
    }

    public function getAll()
    {
        return $this->dao->getAll();
    }

    public function getById($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new InvalidArgumentException("Invalid dinosaur id");
        }

        $dino = $this->dao->getById($id);
        if (!$dino) {
            throw new RuntimeException("Dinosaur not found");
        }

        return $dino;
    }

    public function create($data)
    {
        // Example validation rules
        $required = ['name', 'period', 'location'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("$field is required");
            }
        }

        // Additional constraints (example)
        if (strlen($data['name']) > 100) {
            throw new InvalidArgumentException("Name too long (max 100 chars)");
        }

        return $this->dao->add([
            'name'     => $data['name'],
            'period'   => $data['period'],
            'location' => $data['location'],
        ]);
    }

    public function update($id, $data)
    {
        $this->getById($id); // will throw if not found

        // you can decide which fields are allowed
        $allowed = ['name', 'period', 'location'];
        $entity  = [];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
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
        $this->getById($id); // ensure it exists
        $this->dao->delete($id);
        return true;
    }

    public function getByLocation($location)
    {
        if (empty($location)) {
            throw new InvalidArgumentException("location is required");
        }

        return $this->dao->getByLocation($location);
    }
}
