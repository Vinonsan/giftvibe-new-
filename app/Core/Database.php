<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $configFile = dirname(__DIR__, 2) . '/config.php';
            $config = is_file($configFile) ? (array) require $configFile : [];
            $database = (array) ($config['database'] ?? []);
            $host = getenv('DB_HOST') ?: (string) ($database['host'] ?? 'localhost');
            $name = getenv('DB_NAME') ?: (string) ($database['name'] ?? 'dbof-giftvibelk');
            $user = getenv('DB_USER') ?: (string) ($database['user'] ?? 'root');
            $password = getenv('DB_PASSWORD') !== false ? (string) getenv('DB_PASSWORD') : (string) ($database['password'] ?? '');

            self::$connection = new PDO(
                "mysql:host={$host};dbname={$name};charset=utf8mb4",
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }

        return self::$connection;
    }
}
