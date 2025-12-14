<?php
require_once __DIR__ . '/../dao/ResearcherDao.php';
require_once __DIR__ . '/BaseService.php';

class ResearcherService extends BaseService {
    
    public function __construct() {
        $this->dao = new ResearcherDao();
    }

    protected function validateData($data, $isUpdate = false) {
        if (!$isUpdate && empty($data['name'])) {
            throw new Exception("Researcher name is required");
        }
        
        if (isset($data['name']) && strlen($data['name']) > 100) {
            throw new Exception("Researcher name must not exceed 100 characters");
        }
    }

    protected function getIdColumn() {
        return 'researcher_id';
    }
}
