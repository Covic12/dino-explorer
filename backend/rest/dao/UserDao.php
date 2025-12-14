<?php
require_once 'BaseDao.php';

class UserDao extends BaseDao {
    protected $table_name;

    public function __construct()
    {
        $this->table_name = "user";
        parent::__construct($this->table_name);
    }

    public function getUserByEmail($email)
    {
        $stmt = $this->connection->prepare("SELECT * FROM " . $this->table_name . " WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(); 
    }

    public function getUserByUsername($username)
    {
        $stmt = $this->connection->prepare("SELECT * FROM " . $this->table_name . " WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>
