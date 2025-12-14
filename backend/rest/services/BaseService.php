<?php

abstract class BaseService {
    protected $dao;

    public function getAll() {
        try {
            return $this->dao->getAll();
        } catch (Exception $e) {
            throw new Exception("Error fetching records: " . $e->getMessage());
        }
    }

    public function getById($id) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid ID provided");
        }
        
        try {
            $result = $this->dao->getById($id);
            if (!$result) {
                throw new Exception("Record not found");
            }
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error fetching record: " . $e->getMessage());
        }
    }

    public function add($data) {
        $this->validateData($data);
        
        try {
            return $this->dao->add($data);
        } catch (Exception $e) {
            throw new Exception("Error adding record: " . $e->getMessage());
        }
    }

    public function update($id, $data) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid ID provided");
        }
        
        $this->validateData($data, true);
        
        try {
            return $this->dao->update($data, $id, $this->getIdColumn());
        } catch (Exception $e) {
            throw new Exception("Error updating record: " . $e->getMessage());
        }
    }

    public function delete($id) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid ID provided");
        }
        
        try {
            return $this->dao->delete($id);
        } catch (Exception $e) {
            throw new Exception("Error deleting record: " . $e->getMessage());
        }
    }

    abstract protected function validateData($data, $isUpdate = false);
    
    abstract protected function getIdColumn();
}
