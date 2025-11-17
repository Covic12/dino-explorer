<?php
require_once __DIR__ . '/../dao/LocationDao.php';

class LocationService
{
    private $dao;

    public function __construct()
    {
        $this->dao = new LocationDao();
    }

    public function getAll()
    {
        return $this->dao->getAll();
    }

    public function getById($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new InvalidArgumentException("Invalid location id");
        }

        $location = $this->dao->getById($id);
        if (!$location) {
            throw new RuntimeException("Location not found");
        }

        return $location;
    }

    public function create($data)
    {
        // validation rules
        $required = ['name', 'continent', 'description'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("$field is required");
            }
        }

        if (strlen($data['name']) > 100) {
            throw new InvalidArgumentException("Name too long (max 100 chars)");
        }

        return $this->dao->add([
            'name'        => $data['name'],
            'continent'   => $data['continent'],
            'description' => $data['description'],
        ]);
    }

    public function update($id, $data)
    {
        // ensure it exists
        $this->getById($id);

        $allowed = ['name', 'continent', 'description'];
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
        $this->getById($id); // will throw if not found
        $this->dao->delete($id);
        return true;
    }

    public function getByContinent($continent)
    {
        if (empty($continent)) {
            throw new InvalidArgumentException("continent is required");
        }

        return $this->dao->getLocationsByContinent($continent);
    }
}
