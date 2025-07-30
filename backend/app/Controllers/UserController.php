<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

/**
 * User Controller
 * Handles user management operations
 */
class UserController extends Controller
{
    /**
     * Display a listing of users (Admin only)
     */
    public function index()
    {
        $this->requireAdmin();

        $users = User::query("SELECT * FROM users ORDER BY created_at DESC");

        $this->response->json($users);
    }

    /**
     * Store a newly created user (Admin only)
     */
    public function store()
    {
        $this->requireAdmin();

        $data = $this->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'balance' => 'required|numeric',
            'bot_balance' => 'required|numeric',
            'role' => 'required',
            'status' => 'required',
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

        $user = User::create($data);

        $this->response->json($user, 201);
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        $currentUser = $this->user();
        
        // Users can only see their own profile, admins can see any
        if ($currentUser['role'] !== 'admin' && $currentUser['id'] != $id) {
            $this->response->json([
                'error' => true,
                'message' => 'Acesso negado'
            ], 403);
        }

        $user = User::find($id);
        if (!$user) {
            $this->response->json([
                'error' => true,
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        $this->response->json($user);
    }

    /**
     * Update the specified user
     */
    public function update($id)
    {
        $currentUser = $this->user();
        
        // Users can only update their own profile, admins can update any
        if ($currentUser['role'] !== 'admin' && $currentUser['id'] != $id) {
            $this->response->json([
                'error' => true,
                'message' => 'Acesso negado'
            ], 403);
        }

        $user = User::find($id);
        if (!$user) {
            $this->response->json([
                'error' => true,
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        $allowedFields = ['name', 'email', 'balance', 'bot_balance'];
        
        // Only admins can update role and status
        if ($currentUser['role'] === 'admin') {
            $allowedFields = array_merge($allowedFields, ['role', 'status']);
        }

        $data = [];
        foreach ($allowedFields as $field) {
            if ($this->request->has($field)) {
                $data[$field] = $this->request->input($field);
            }
        }

        if (empty($data)) {
            $this->response->json([
                'error' => true,
                'message' => 'Nenhum dado para atualizar'
            ], 400);
        }

        // Validate email uniqueness if updating email
        if (isset($data['email'])) {
            $existingUser = User::whereFirst('email', $data['email']);
            if ($existingUser && $existingUser['id'] != $id) {
                $this->response->json([
                    'error' => true,
                    'message' => 'Este email já está em uso.'
                ], 422);
            }
        }

        $updatedUser = User::update($id, $data);

        $this->response->json($updatedUser);
    }

    /**
     * Remove the specified user (Admin only)
     */
    public function destroy($id)
    {
        $this->requireAdmin();
        
        $currentUser = $this->user();
        
        // Prevent admin from deleting themselves
        if ($currentUser['id'] == $id) {
            $this->response->json([
                'error' => true,
                'message' => 'Você não pode excluir sua própria conta'
            ], 400);
        }

        $user = User::find($id);
        if (!$user) {
            $this->response->json([
                'error' => true,
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        User::delete($id);

        $this->response->json([
            'message' => 'Usuário excluído com sucesso'
        ]);
    }

    /**
     * Update user balance
     */
    public function updateBalance($id)
    {
        $currentUser = $this->user();
        
        // Users can only update their own balance, admins can update any
        if ($currentUser['role'] !== 'admin' && $currentUser['id'] != $id) {
            $this->response->json([
                'error' => true,
                'message' => 'Acesso negado'
            ], 403);
        }

        $user = User::find($id);
        if (!$user) {
            $this->response->json([
                'error' => true,
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        $data = [];
        if ($this->request->has('balance')) {
            $data['balance'] = $this->request->input('balance');
        }
        if ($this->request->has('bot_balance')) {
            $data['bot_balance'] = $this->request->input('bot_balance');
        }

        if (empty($data)) {
            $this->response->json([
                'error' => true,
                'message' => 'Nenhum saldo para atualizar'
            ], 400);
        }

        $updatedUser = User::update($id, $data);

        $this->response->json($updatedUser);
    }
}