<?php
class Connect
{
    private $dbConnection = null;

    public function __construct()
    {
        $host = "localhost";
        $port = "3306";
        $db   = "my_formaggy";
        $user = "formaggy";
        $pass = "5aUuWTj456aj";

        try {
            $this->dbConnection = new PDO(
                "mysql:host=$host;port=$port;charset=utf8mb4;dbname=$db",
                $user,
                $pass
            );
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->dbConnection;
    }
}
