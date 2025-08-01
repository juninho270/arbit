<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function index($request, $response)
    {
        if (!Auth::check() || !User::isAdmin(Auth::user())) {
            $response->json(['error' => 'Forbidden'], 403);
            return;
        }
        
        $users = User::all();
        
        // Remove passwords from response
        foreach ($users as &$user) {
            unset($user['password']);
        }
        
        $response->json($users);
    }

    public function store($request, $response)
    {
        if (!Auth::check() || !User::isAdmin(Auth::user())) {
            $response->json(['error' => 'Forbidden'], 403);
            return;
        }
        
        try {
            $data = $request->getJson();
            $this->validateRequired($data, ['name', 'email']);
            
            $userData = [
                'name' => $this->sanitize($data['name']),
                'email' => $this->sanitize($data['email']),
                'password' => $data['password'] ?? 'password',
                'balance' => $data['balance'] ?? 0,
                'bot_balance' => $data['bot_balance'] ?? 0,
                'role' => $data['role'] ?? 'user',
                'status' => $data['status'] ?? 'active'
            ];
            
            $user = User::create($userData);
            unset($user['password']);
            
            $response->json($user);
        } catch (\Exception $e) {
            $response->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show($request, $response)
    {
        if (!Auth::check()) {
            $response->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        $id = $request->getParam('id');
        $currentUser = Auth::user();
        
        // Users can only view their own profile, admins can view any
        if ($currentUser['id'] != $id && !User::isAdmin($currentUser)) {
            $response->json(['error' => 'Forbidden'], 403);
            return;
        }
        
        $user = User::find($id);
        if (!$user) {
            $response->json(['error' => 'User not found'], 404);
            return;
        }
        
        unset($user['password']);
        $response->json($user);
    }

    public function update($request, $response)
    {
        if (!Auth::check()) {
            $response->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        $id = $request->getParam('id');
        $currentUser = Auth::user();
        
        // Users can only update their own profile, admins can update any
        if ($currentUser['id'] != $id && !User::isAdmin($currentUser)) {
            $response->json(['error' => 'Forbidden'], 403);
            return;
        }
        
        try {
            $data = $request->getJson();
            $user = User::update($id, $data);
            
            if (!$user) {
                $response->json(['error' => 'User not found'], 404);
                return;
            }
            
            unset($user['password']);
            $response->json($user);
        } catch (\Exception $e) {
            $response->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy($request, $response)
    {
        if (!Auth::check() || !User::isAdmin(Auth::user())) {
            $response->json(['error' => 'Forbidden'], 403);
            return;
        }
        
        $id = $request->getParam('id');
        $deleted = User::delete($id);
        
        if ($deleted) {
            $response->json(['success' => true]);
        } else {
            $response->json(['error' => 'User not found'], 404);
        }
    }

    public function updateBalance($request, $response)
    {
        if (!Auth::check()) {
            $response->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        $id = $request->getParam('id');
        $currentUser = Auth::user();
        
        // Users can only update their own balance, admins can update any
        if ($currentUser['id'] != $id && !User::isAdmin($currentUser)) {
            $response->json(['error' => 'Forbidden'], 403);
            return;
        }
        
        try {
            $data = $request->getJson();
            
            $updateData = [];
            if (isset($data['balance'])) {
                $updateData['balance'] = $data['balance'];
            }
            if (isset($data['bot_balance'])) {
                $updateData['bot_balance'] = $data['bot_balance'];
            }
            
            $user = User::update($id, $updateData);
            
            if (!$user) {
                $response->json(['error' => 'User not found'], 404);
                return;
            }
            
            unset($user['password']);
            $response->json($user);
        } catch (\Exception $e) {
            $response->json(['error' => $e->getMessage()], 400);
        }
    }
}