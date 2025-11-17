<?php
require_once __DIR__ . '/../dao/ResearcherDao.php';

class ResearcherService
{
    private $dao;

    public function __construct()
    {
        $this->dao = new ResearcherDao();
    }

    public function getAll()
    {
        return $this->dao->getAll();
    }

    public function getById($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new InvalidArgumentException("Invalid researcher id");
        }

        $researcher = $this->dao->getById($id);
        if (!$researcher) {
            throw new RuntimeException("Researcher not found");
        }

        return $researcher;
    }

    public function create($data)
    {
        // validation rules (adjust fields to your real schema)
        $required = ['name', 'field', 'bio'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("$field is required");
            }
        }

        if (strlen($data['name']) > 100) {
            throw new InvalidArgumentException("Name too long (max 100 chars)");
        }

        return $this->dao->add([
            'name'  => $data['name'],
            'field' => $data['field'],
            'bio'   => $data['bio'],
        ]);
    }

    public function update($id, $data)
    {
        // ensure exists
        $this->getById($id);

        $allowed = ['name', 'field', 'bio'];
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

    public function searchByName($name)
    {
        if (empty($name)) {
            throw new InvalidArgumentException("name is required");
        }

        return $this->dao->searchResearchersByName($name);
    }
}
