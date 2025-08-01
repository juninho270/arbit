<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login($request, $response)
    {
        try {
            $data = $request->getJson();
            
            if (!$data) {
                $response->json(['error' => 'Invalid JSON'], 400);
                return;
            }
            
            $this->validateRequired($data, ['email', 'password']);
            
            $email = $this->sanitize($data['email']);
            $password = $data['password'];
            
            if (Auth::attempt($email, $password)) {
                $user = Auth::user();
                $token = Auth::generateToken($user['id']);
                
                // Remove password from response
                unset($user['password']);
                
                $response->json([
                    'success' => true,
                    'user' => $user,
                    'token' => $token
                ]);
            } else {
                $response->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (\Exception $e) {
            $response->json(['error' => $e->getMessage()], 400);
        }
    }

    public function register($request, $response)
    {
        try {
            $data = $request->getJson();
            
            $this->validateRequired($data, ['name', 'email', 'password']);
            
            $userData = [
                'name' => $this->sanitize($data['name']),
                'email' => $this->sanitize($data['email']),
                'password' => $data['password']
            ];
            
            $user = User::create($userData);
            $token = Auth::generateToken($user['id']);
            
            // Remove password from response
            unset($user['password']);
            
            $response->json([
                'success' => true,
                'user' => $user,
                'token' => $token
            ]);
        } catch (\Exception $e) {
            $response->json(['error' => $e->getMessage()], 400);
        }
    }

    public function logout($request, $response)
    {
        Auth::logout();
        $response->json(['success' => true]);
    }

    public function me($request, $response)
    {
        if (!Auth::check()) {
            $response->json(['error' => 'Unauthorized'], 401);
            return;
        }
        
        $user = Auth::user();
        unset($user['password']);
        
        $response->json($user);
    }

    public function loginAsUser($request, $response)
    {
        if (!Auth::check() || !User::isAdmin(Auth::user())) {
            $response->json(['error' => 'Forbidden'], 403);
            return;
        }
        
        try {
            $data = $request->getJson();
            $this->validateRequired($data, ['user_id']);
            
            $targetUser = User::find($data['user_id']);
            if (!$targetUser) {
                $response->json(['error' => 'User not found'], 404);
                return;
            }
            
            $token = Auth::generateToken($targetUser['id']);
            unset($targetUser['password']);
            
            $response->json([
                'success' => true,
                'user' => $targetUser,
                'token' => $token
            ]);
        } catch (\Exception $e) {
            $response->json(['error' => $e->getMessage()], 400);
        }
    }
}