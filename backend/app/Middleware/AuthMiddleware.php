<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;

/**
 * Authentication Middleware
 * Ensures user is authenticated before accessing protected routes
 */
class AuthMiddleware
{
    public function handle(Request $request, Response $response)
    {
        if (!Auth::check()) {
            $response->json([
                'error' => true,
                'message' => 'Authentication required'
            ], 401);
            return false;
        }
        
        return true;
    }
}