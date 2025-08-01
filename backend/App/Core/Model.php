<?php

namespace App\Core;

class Model
{
    protected static $connection;

    public static function getConnection()
    {
        if (!self::$connection) {
            $config = require CONFIG_PATH . '/database.php';
            $dbConfig = $config['connections']['mysql'];
            
            $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
            
            try {
                self::$connection = new \PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
            } catch (\PDOException $e) {
                throw new \Exception('Database connection failed: ' . $e->getMessage());
            }
        }
        
        return self::$connection;
    }

    protected static function query($sql, $params = [])
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    protected static function fetchAll($sql, $params = [])
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }

    protected static function fetchOne($sql, $params = [])
    {
        $stmt = self::query($sql, $params);
        return $stmt->fetch();
    }

    protected static function execute($sql, $params = [])
    {
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }

    protected static function lastInsertId()
    {
        return self::getConnection()->lastInsertId();
    }
}