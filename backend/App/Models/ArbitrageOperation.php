<?php

namespace App\Models;

use App\Core\Model;

class ArbitrageOperation extends Model
{
    public static function getRecentByUser($userId, $limit = 10)
    {
        $sql = 'SELECT * FROM arbitrage_operations 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?';
        
        return self::fetchAll($sql, [$userId, $limit]);
    }

    public static function countByUser($userId)
    {
        $sql = 'SELECT COUNT(*) as count FROM arbitrage_operations WHERE user_id = ?';
        $result = self::fetchOne($sql, [$userId]);
        return $result['count'] ?? 0;
    }

    public static function getMonthlyProfitByUser($userId)
    {
        $sql = 'SELECT COALESCE(SUM(profit), 0) as total_profit 
                FROM arbitrage_operations 
                WHERE user_id = ? 
                AND status = "completed"
                AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
        
        $result = self::fetchOne($sql, [$userId]);
        return $result['total_profit'] ?? 0;
    }

    public static function create($data)
    {
        $sql = 'INSERT INTO arbitrage_operations (
                    user_id, type, cryptocurrency, amount, buy_price, sell_price, 
                    profit, profit_percentage, status, transaction_hash, chain, 
                    execution_time, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())';
        
        $params = [
            $data['user_id'],
            $data['type'],
            $data['cryptocurrency'],
            $data['amount'],
            $data['buy_price'],
            $data['sell_price'],
            $data['profit'],
            $data['profit_percentage'],
            $data['status'],
            $data['transaction_hash'] ?? null,
            $data['chain'] ?? null,
            $data['execution_time']
        ];
        
        self::execute($sql, $params);
        return self::find(self::lastInsertId());
    }

    public static function find($id)
    {
        return self::fetchOne('SELECT * FROM arbitrage_operations WHERE id = ?', [$id]);
    }

    public static function update($id, $data)
    {
        $fields = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, ['status', 'transaction_hash', 'chain', 'profit', 'profit_percentage', 'completed_at', 'error_message'])) {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
        }
        
        if (empty($fields)) {
            return self::find($id);
        }
        
        $params[] = $id;
        $sql = 'UPDATE arbitrage_operations SET ' . implode(', ', $fields) . ', updated_at = NOW() WHERE id = ?';
        
        self::execute($sql, $params);
        return self::find($id);
    }
}