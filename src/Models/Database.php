<?php
namespace Models;

use App\Core\Database as CoreDatabase;

class Database
{
    private static ?self $instance = null;
    private \PDO $conn;

    private function __construct()
    {
        $this->conn = CoreDatabase::pdo();
    }

    public static function connect(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConn(): \PDO
    {
        return $this->conn;
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function insert(string $sql, array $params = []): string
    {
        $this->execute($sql, $params);
        return $this->conn->lastInsertId();
    }
}
