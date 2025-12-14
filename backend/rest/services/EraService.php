<?php
require_once __DIR__ . '/../dao/EraDao.php';
require_once __DIR__ . '/BaseService.php';

class EraService extends BaseService {
    
    public function __construct() {
        $this->dao = new EraDao();
    }

    protected function validateData($data, $isUpdate = false) {
        if (!$isUpdate && empty($data['title'])) {
            throw new Exception("Era title is required");
        }
        
        if (isset($data['title']) && strlen($data['title']) > 100) {
            throw new Exception("Era title must not exceed 100 characters");
        }
        
        if (isset($data['era']) && strlen($data['era']) > 100) {
            throw new Exception("Era must not exceed 100 characters");
        }
        
        if (isset($data['period']) && strlen($data['period']) > 100) {
            throw new Exception("Period must not exceed 100 characters");
        }
    }

    protected function getIdColumn() {
        return 'era_id';
    }

    public function getErasByPeriod($period) {
        try {
            return $this->dao->getErasByPeriod($period);
        } catch (Exception $e) {
            throw new Exception("Error fetching eras by period: " . $e->getMessage());
        }
    }
}
