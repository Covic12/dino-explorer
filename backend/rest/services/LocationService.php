<?php
require_once __DIR__ . '/../dao/LocationDao .php';
require_once __DIR__ . '/BaseService.php';

class LocationService extends BaseService {
    
    public function __construct() {
        $this->dao = new LocationDao();
    }

    protected function validateData($data, $isUpdate = false) {
        if (!$isUpdate && empty($data['title'])) {
            throw new Exception("Location title is required");
        }
        
        if (isset($data['title']) && strlen($data['title']) > 100) {
            throw new Exception("Location title must not exceed 100 characters");
        }
        
        if (isset($data['continent']) && strlen($data['continent']) > 100) {
            throw new Exception("Continent must not exceed 100 characters");
        }
        
        if (isset($data['country']) && strlen($data['country']) > 100) {
            throw new Exception("Country must not exceed 100 characters");
        }
        
        if (isset($data['location_url']) && !empty($data['location_url']) && !filter_var($data['location_url'], FILTER_VALIDATE_URL)) {
            throw new Exception("Invalid URL format");
        }
    }

    protected function getIdColumn() {
        return 'location_id';
    }
}
