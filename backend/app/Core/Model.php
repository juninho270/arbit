<?php

namespace App\Core;

/**
 * Base Model Class
 * Provides basic database operations using PDO
 */
abstract class Model
{
    protected static $connection;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];

    /**
     * Get database connection
     */
    protected static function getConnection()
    {
        if (!self::$connection) {
            $config = include CONFIG_PATH . '/database.php';
            $dbConfig = $config['connections'][$config['default']];
            
            $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
            
            self::$connection = new \PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
        }
        
        return self::$connection;
    }

    /**
     * Find a record by ID
     */
    public static function find($id)
    {
        $instance = new static();
        $pdo = self::getConnection();
        
        $stmt = $pdo->prepare("SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = ?");
        $stmt->execute([$id]);
        
        $result = $stmt->fetch();
        return $result ? $instance->hideFields($result) : null;
    }

    /**
     * Find all records
     */
    public static function all()
    {
        $instance = new static();
        $pdo = self::getConnection();
        
        $stmt = $pdo->query("SELECT * FROM {$instance->table}");
        $results = $stmt->fetchAll();
        
        return array_map([$instance, 'hideFields'], $results);
    }

    /**
     * Find records with WHERE clause
     */
    public static function where($column, $operator, $value = null)
    {
        $instance = new static();
        $pdo = self::getConnection();
        
        // If only 2 parameters, assume '=' operator
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $stmt = $pdo->prepare("SELECT * FROM {$instance->table} WHERE {$column} {$operator} ?");
        $stmt->execute([$value]);
        
        $results = $stmt->fetchAll();
        return array_map([$instance, 'hideFields'], $results);
    }

    /**
     * Find first record with WHERE clause
     */
    public static function whereFirst($column, $operator, $value = null)
    {
        $results = static::where($column, $operator, $value);
        return !empty($results) ? $results[0] : null;
    }

    /**
     * Create a new record
     */
    public static function create($data)
    {
        $instance = new static();
        $pdo = self::getConnection();
        
        // Filter only fillable fields
        $filteredData = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $instance->fillable)) {
                $filteredData[$key] = $value;
            }
        }
        
        $columns = implode(', ', array_keys($filteredData));
        $placeholders = ':' . implode(', :', array_keys($filteredData));
        
        $sql = "INSERT INTO {$instance->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $pdo->prepare($sql);
        
        foreach ($filteredData as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        
        $stmt->execute();
        
        // Return the created record
        return static::find($pdo->lastInsertId());
    }

    /**
     * Update a record
     */
    public static function update($id, $data)
    {
        $instance = new static();
        $pdo = self::getConnection();
        
        // Filter only fillable fields
        $filteredData = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $instance->fillable)) {
                $filteredData[$key] = $value;
            }
        }
        
        $setParts = [];
        foreach ($filteredData as $key => $value) {
            $setParts[] = "{$key} = :{$key}";
        }
        
        $sql = "UPDATE {$instance->table} SET " . implode(', ', $setParts) . " WHERE {$instance->primaryKey} = :id";
        $stmt = $pdo->prepare($sql);
        
        foreach ($filteredData as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->bindValue(':id', $id);
        
        $stmt->execute();
        
        // Return the updated record
        return static::find($id);
    }

    /**
     * Delete a record
     */
    public static function delete($id)
    {
        $instance = new static();
        $pdo = self::getConnection();
        
        $stmt = $pdo->prepare("DELETE FROM {$instance->table} WHERE {$instance->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Execute raw SQL query
     */
    public static function query($sql, $params = [])
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    /**
     * Hide specified fields from result
     */
    protected function hideFields($data)
    {
        if (empty($this->hidden)) {
            return $data;
        }
        
        foreach ($this->hidden as $field) {
            unset($data[$field]);
        }
        
        return $data;
    }

    /**
     * Hash password
     */
    protected static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify password
     */
    protected static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
}