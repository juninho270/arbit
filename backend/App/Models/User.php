<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    public static function findByEmail($email)
    {
        return self::fetchOne('SELECT * FROM users WHERE email = ?', [$email]);
    }

    public static function find($id)
    {
        return self::fetchOne('SELECT * FROM users WHERE id = ?', [$id]);
    }

    public static function all()
    {
        return self::fetchAll('SELECT * FROM users ORDER BY created_at DESC');
    }

    public static function create($data)
    {
        $sql = 'INSERT INTO users (name, email, password, balance, bot_balance, role, status, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())';
        
        $params = [
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['balance'] ?? 0,
            $data['bot_balance'] ?? 0,
            $data['role'] ?? 'user',
            $data['status'] ?? 'active'
        ];
        
        self::execute($sql, $params);
        return self::find(self::lastInsertId());
    }

    public static function update($id, $data)
    {
        $fields = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, ['name', 'email', 'balance', 'bot_balance', 'role', 'status'])) {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
        }
        
        if (empty($fields)) {
            return self::find($id);
        }
        
        $params[] = $id;
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ', updated_at = NOW() WHERE id = ?';
        
        self::execute($sql, $params);
        return self::find($id);
    }

    public static function delete($id)
    {
        return self::execute('DELETE FROM users WHERE id = ?', [$id]);
    }

    public static function isAdmin($user)
    {
        return isset($user['role']) && $user['role'] === 'admin';
    }

    public static function getByStatus($status)
    {
        return self::fetchAll('SELECT * FROM users WHERE status = ? ORDER BY created_at DESC', [$status]);
    }

    public static function countAll()
    {
        $result = self::fetchOne('SELECT COUNT(*) as count FROM users');
        return $result['count'] ?? 0;
    }
}