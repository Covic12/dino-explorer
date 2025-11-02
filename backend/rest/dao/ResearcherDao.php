<?php
require_once 'BaseDao.php';

class ResearcherDao extends BaseDao {
    protected $table_name;

    public function __construct()
    {
        $this->table_name = "researchers";
        parent::__construct($this->table_name);
    }

    public function searchResearchersByName($name)
    {
        $stmt = $this->connection->prepare("SELECT * FROM " . $this->table_name . " WHERE name LIKE :name");
        $searchTerm = '%' . $name . '%';
        $stmt->bindParam(':name', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
