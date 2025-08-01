<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;

/**
 * Authentication Controller
 * Handles user login, registration, logout and authentication
 */
class AuthController extends Controller
{
    /**
     * Login user and create session/token
     */
    public function login()
    {
        $data = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($data['email'], $data['password'])) {
            $user = Auth::user();
            
            // Generate API token for API requests
            $token = Auth::generateToken($user['id']);
            
            $this->response->json([
                'user' => $user,
                'token' => $token,
            ]);
        } else {
            $this->response->json([
                'error' => true,
                'message' => 'As credenciais fornecidas estão incorretas.'
            ], 401);
        }
    }

    /**
     * Register a new user
     */
    public function register()
    {
        $data = $this->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        // Check if email already exists
        $existingUser = User::whereFirst('email', $data['email']);
        if ($existingUser) {
            $this->response->json([
                'error' => true,
                'message' => 'Este email já está em uso.'
            ], 422);
        }

        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['balance'] = 1000; // Starting balance
        $data['bot_balance'] = 0;
        $data['role'] = 'user';
        $data['status'] = 'active';

        $user = User::create($data);
        
        // Auto login after registration
        Auth::loginById($user['id']);
        $token = Auth::generateToken($user['id']);

        $this->response->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Logout user
     */
    public function logout()
    {
        $this->requireAuth();
        
        Auth::logout();
        
        $this->response->json([
            'message' => 'Logout realizado com sucesso'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me()
    {
        $this->requireAuth();
        
        $this->response->json($this->user());
    }

    /**
     * Admin login as another user
     */
    public function loginAsUser()
    {
        $this->requireAdmin();
        
        $data = $this->validate([
            'user_id' => 'required|numeric',
        ]);

        $targetUser = User::find($data['user_id']);
        if (!$targetUser) {
            $this->response->json([
                'error' => true,
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        // Login as target user
        Auth::loginById($targetUser['id']);
        $token = Auth::generateToken($targetUser['id']);

        // Update last login
        User::update($targetUser['id'], ['last_login' => date('Y-m-d H:i:s')]);

        $this->response->json([
            'user' => $targetUser,
            'token' => $token,
            'impersonated' => true,
        ]);
    }
}