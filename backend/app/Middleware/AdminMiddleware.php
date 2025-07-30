<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;

/**
 * Admin Middleware
 * Ensures user has admin role before accessing admin routes
 */
class AdminMiddleware
{
    public function handle(Request $request, Response $response)
    {
        if (!Auth::isAdmin()) {
            $response->json([
                'error' => true,
                'message' => 'Admin access required'
            ], 403);
            return false;
        }
        
        return true;
    }
}