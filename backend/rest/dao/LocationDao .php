<?php
require_once 'BaseDao.php';

class LocationDao extends BaseDao {
    protected $table_name;

    public function __construct()
    {
        $this->table_name = "location";
        parent::__construct($this->table_name);
    }

    public function getLocationsByContinent($continent)
    {
        $stmt = $this->connection->prepare("SELECT * FROM " . $this->table_name . " WHERE continent = :continent");
        $stmt->bindParam(':continent', $continent);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
