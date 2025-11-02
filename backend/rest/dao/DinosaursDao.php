<?php
require_once 'BaseDao.php';

class DinosaursDao extends BaseDao {
    protected $table_name;

    public function __construct()
    {
        $this->table_name = "dinosaurs";
        parent::__construct($this->table_name);
    }

    public function getBylocation($location) {
        $stmt = $this->connection->prepare("SELECT * FROM dinosaurs WHERE location = :location");
        $stmt->bindParam(':location', $location);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>