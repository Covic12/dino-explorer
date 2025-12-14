<?php
require_once __DIR__ . '/../dao/DinosaursDao.php';
require_once __DIR__ . '/BaseService.php';

class DinosaurService extends BaseService {
    
    public function __construct() {
        $this->dao = new DinosaursDao();
    }

    protected function validateData($data, $isUpdate = false) {
        if (!$isUpdate && empty($data['name'])) {
            throw new Exception("Dinosaur name is required");
        }
        
        if (isset($data['name']) && strlen($data['name']) > 100) {
            throw new Exception("Dinosaur name must not exceed 100 characters");
        }
        
        if (isset($data['diet']) && !in_array($data['diet'], ['Herbivore', 'Carnivore', 'Omnivore', ''])) {
            throw new Exception("Invalid diet type");
        }
        
        if (isset($data['era_id']) && !empty($data['era_id']) && !is_numeric($data['era_id'])) {
            throw new Exception("Invalid era_id");
        }
        
        if (isset($data['location_id']) && !empty($data['location_id']) && !is_numeric($data['location_id'])) {
            throw new Exception("Invalid location_id");
        }
    }

    protected function getIdColumn() {
        return 'dinosaur_id';
    }

    public function getByLocation($location) {
        try {
            return $this->dao->getByLocation($location);
        } catch (Exception $e) {
            throw new Exception("Error fetching dinosaurs by location: " . $e->getMessage());
        }
    }
}
