<?php
class Database {
    private static $instance = null;
    private $pdo;

    private $host     = 'localhost';
    private $dbname   = 'license_tracker';
    private $username = 'root';
    private $password = '';
    private $charset  = 'utf8mb4';

    private function __construct() {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
    }

    // Prevent cloning and unserialization
    private function __clone() {}
    public function __wakeup() { throw new Exception("Cannot unserialize singleton."); }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->pdo;
    }
}
