<?php
require_once 'BaseDao.php';

class EraDao extends BaseDao {
    protected $table_name;

    public function __construct()
    {
        $this->table_name = "eras";
        parent::__construct($this->table_name);
    }

    public function getErasByPeriod($period)
    {
        $stmt = $this->connection->prepare("SELECT * FROM " . $this->table_name . " WHERE period = :period");
        $stmt->bindParam(':period', $period);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
