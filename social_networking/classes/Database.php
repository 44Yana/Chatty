<?php
class Database {
    private $host = 'localhost';
    private $dbname = 'social_db';
    private $username = 'root';
    private $password = '';
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Грешка при свързване: " . $e->getMessage());
        }
    }


    public function getConnection() {
        return $this->pdo;
    }

//Sql zaqvka
    public function prepare($sql) {
        return $this->pdo->prepare($sql);
    }
    public function query($sql) {
        return $this->pdo->query($sql);
    }
}
?>
