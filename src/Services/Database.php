<?php

namespace App\Services;

class Database
{
    private static $mysql = null;
    private static $mariadb = null;

    public static function mysql()
    {
        if (self::$mysql) return self::$mysql;
        $host = env('MYSQL_HOST', 'mysql');
        $db = env('MYSQL_DB', 'app_db');
        $user = env('MYSQL_USER', 'app_user');
        $pass = env('MYSQL_PASSWORD', 'secret');
        $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";
        self::$mysql = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
        return self::$mysql;
    }

    public static function mariadb()
    {
        if (self::$mariadb) return self::$mariadb;
        $host = env('MARIADB_HOST', 'mariadb');
        $db = env('MARIADB_DB', 'app_db');
        $user = env('MARIADB_USER', 'app_user');
        $pass = env('MARIADB_PASSWORD', 'secret');
        $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4"; // same PDO driver
        self::$mariadb = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
        return self::$mariadb;
    }
}
