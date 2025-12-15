<?php

namespace App\Services;

class Database
{
    private static ?\PDO $mysql = null;
    private static ?\PDO $mariadb = null;

    protected static function connect(string $host, int $port, string $db, string $user, string $pass): \PDO
    {
        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_TIMEOUT => 3,
        ];

        // إعادة محاولة خفيفة في حالة تأخر الإقلاع
        $attempts = 0;
        $maxAttempts = 5;
        $delay = 1;
        do {
            try {
                return new \PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e) {
                $attempts++;
                if ($attempts >= $maxAttempts) {
                    throw $e;
                }
                sleep($delay);
                $delay++;
            }
        } while ($attempts < $maxAttempts);

        // لن يصل هنا
        return new \PDO($dsn, $user, $pass, $options);
    }

    public static function mysql(): \PDO
    {
        if (self::$mysql instanceof \PDO) return self::$mysql;
        $host = env('MYSQL_HOST', 'mysql');
        $port = env('MYSQL_PORT', 3306);
        $db   = env('MYSQL_DB', 'app_db');
        $user = env('MYSQL_USER', 'app_user');
        $pass = env('MYSQL_PASSWORD', 'secret');
        self::$mysql = self::connect($host, (int)$port, $db, $user, $pass);
        return self::$mysql;
    }

    public static function mariadb(): \PDO
    {
        if (self::$mariadb instanceof \PDO) return self::$mariadb;
        $host = env('MARIADB_HOST', 'mariadb');
        $port = env('MARIADB_PORT', 3306);
        $db   = env('MARIADB_DB', 'app_db');
        $user = env('MARIADB_USER', 'app_user');
        $pass = env('MARIADB_PASSWORD', 'secret');
        self::$mariadb = self::connect($host, (int)$port, $db, $user, $pass);
        return self::$mariadb;
    }
}
