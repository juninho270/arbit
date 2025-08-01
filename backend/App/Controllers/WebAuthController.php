<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;

class WebAuthController extends Controller
{
    public function showLogin($request, $response)
    {
        // Se já estiver logado, redirecionar
        if (Auth::check()) {
            $user = Auth::user();
            if (User::isAdmin($user)) {
                $response->redirect('/admin');
            } else {
                $response->redirect('/dashboard');
            }
            return;
        }

        // Renderizar página de login
        $this->render('login');
    }

    public function login($request, $response)
    {
        try {
            $email = $request->getPost('email');
            $password = $request->getPost('password');
            
            if (!$email || !$password) {
                $this->render('login', [
                    'error' => 'Email e senha são obrigatórios',
                    'email' => $email
                ]);
                return;
            }
            
            $email = $this->sanitize($email);
            
            if (Auth::attempt($email, $password)) {
                $user = Auth::user();
                
                // Atualizar último login
                User::update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
                
                // Redirecionar baseado no role
                if (User::isAdmin($user)) {
                    $response->redirect('/admin');
                } else {
                    $response->redirect('/dashboard');
                }
            } else {
                $this->render('login', [
                    'error' => 'Credenciais inválidas',
                    'email' => $email
                ]);
            }
        } catch (\Exception $e) {
            $this->render('login', [
                'error' => 'Erro interno do servidor',
                'email' => $email ?? ''
            ]);
        }
    }

    public function logout($request, $response)
    {
        Auth::logout();
        $response->redirect('/login');
    }
}