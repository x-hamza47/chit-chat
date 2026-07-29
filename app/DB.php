<?php

namespace App;

require_once __DIR__ . '/../config/bootstrap.php';

class DB
{
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;

    protected static $conn;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'];
        $this->port = $_ENV['DB_PORT'] ?? 3306;
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
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";

            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ];

            if (!empty($_ENV['DB_SSL_CA'])) {
                $options[\PDO::MYSQL_ATTR_SSL_CA] = $_ENV['DB_SSL_CA'];
            }

            self::$conn = new \PDO(
                $dsn,
                $this->username,
                $this->password,
                $options
            );
        } catch (\PDOException $e) {
            die("Error connecting to database: " . $e->getMessage());
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
