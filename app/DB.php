<?php

namespace App;

require_once __DIR__ . '/../config/bootstrap.php';

class DB {
    
    private $host;
    private $db_name;
    private $username;
    private $password;
    protected static $conn;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'];
        $this->db_name = $_ENV['DB_NAME'];
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];
        if (!self::$conn) {
            $this->connect();
        }
    }

    private function connect()
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name}";
            self::$conn = new \PDO($dsn, $this->username, $this->password);
            self::$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        } catch (\PDOException $e) {
           die("Error connecting to database: " . $e->getMessage() );
        }
    }

    public static function getConnection()
    {
        if (!self::$conn) {
            new self(); 
        }
        return self::$conn;
    }


}