<?php

namespace App\Core;

/**
 * Authentication Class
 * Handles user authentication and session management
 */
class Auth
{
    private static $user = null;

    /**
     * Attempt to authenticate user
     */
    public static function attempt($email, $password)
    {
        $userModel = new \App\Models\User();
        $user = $userModel::whereFirst('email', $email);
        
        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $userModel::update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
            
            // Store user in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = $user;
            
            self::$user = $user;
            return true;
        }
        
        return false;
    }

    /**
     * Login user by ID (for admin impersonation)
     */
    public static function loginById($userId)
    {
        $userModel = new \App\Models\User();
        $user = $userModel::find($userId);
        
        if ($user) {
            // Update last login
            $userModel::update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
            
            // Store user in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = $user;
            
            self::$user = $user;
            return true;
        }
        
        return false;
    }

    /**
     * Get authenticated user
     */
    public static function user()
    {
        if (self::$user) {
            return self::$user;
        }
        
        if (isset($_SESSION['user'])) {
            self::$user = $_SESSION['user'];
            return self::$user;
        }
        
        // Try to authenticate via bearer token (for API requests)
        $request = new Request();
        $token = $request->bearerToken();
        
        if ($token) {
            // In a real implementation, you would verify the JWT token here
            // For now, we'll use a simple token-based auth
            $userModel = new \App\Models\User();
            $user = $userModel::whereFirst('remember_token', $token);
            
            if ($user) {
                self::$user = $user;
                return self::$user;
            }
        }
        
        return null;
    }

    /**
     * Check if user is authenticated
     */
    public static function check()
    {
        return self::user() !== null;
    }

    /**
     * Logout user
     */
    public static function logout()
    {
        self::$user = null;
        unset($_SESSION['user_id']);
        unset($_SESSION['user']);
        session_destroy();
    }

    /**
     * Generate API token for user
     */
    public static function generateToken($userId)
    {
        $token = bin2hex(random_bytes(32));
        
        // Store token in database
        $userModel = new \App\Models\User();
        $userModel::update($userId, ['remember_token' => $token]);
        
        return $token;
    }

    /**
     * Check if user is admin
     */
    public static function isAdmin()
    {
        $user = self::user();
        return $user && $user['role'] === 'admin';
    }
}