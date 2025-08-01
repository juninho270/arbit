<?php

namespace App\Core;

use App\Models\User;

class Auth
{
    private static $user = null;

    public static function attempt($email, $password)
    {
        $user = User::findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            self::$user = $user;
            $_SESSION['user_id'] = $user['id'];
            return true;
        }
        
        return false;
    }

    public static function check()
    {
        if (self::$user) {
            return true;
        }
        
        // Check session
        if (isset($_SESSION['user_id'])) {
            self::$user = User::find($_SESSION['user_id']);
            return self::$user !== null;
        }
        
        // Check Bearer token
        $request = new Request();
        $token = $request->getBearerToken();
        
        if ($token) {
            // In a real app, you'd validate the JWT token here
            // For now, we'll use a simple token format: user_id
            if (is_numeric($token)) {
                self::$user = User::find($token);
                return self::$user !== null;
            }
        }
        
        return false;
    }

    public static function user()
    {
        if (!self::check()) {
            return null;
        }
        
        return self::$user;
    }

    public static function id()
    {
        $user = self::user();
        return $user ? $user['id'] : null;
    }

    public static function logout()
    {
        self::$user = null;
        unset($_SESSION['user_id']);
        session_destroy();
    }

    public static function generateToken($userId)
    {
        // In a real app, you'd generate a proper JWT token
        // For now, we'll use a simple format
        return base64_encode($userId . ':' . time());
    }
}