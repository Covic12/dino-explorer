<?php
require_once __DIR__ . '/../dao/EraDao.php';

class EraService
{
    private $dao;

    public function __construct()
    {
        $this->dao = new EraDao();
    }

    public function getAll()
    {
        return $this->dao->getAll();
    }

    public function getById($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new InvalidArgumentException("Invalid era id");
        }

        $era = $this->dao->getById($id);
        if (!$era) {
            throw new RuntimeException("Era not found");
        }

        return $era;
    }

    public function create($data)
    {
        // basic validation rules
        $required = ['name', 'period', 'description'];
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
            'period'      => $data['period'],
            'description' => $data['description'],
        ]);
    }

    public function update($id, $data)
    {
        $this->getById($id); // throws if not found

        $allowed = ['name', 'period', 'description'];
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

    public function getByPeriod($period)
    {
        if (empty($period)) {
            throw new InvalidArgumentException("period is required");
        }

        return $this->dao->getErasByPeriod($period);
    }
}
