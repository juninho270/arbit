<?php

namespace App\Core;

class Response
{
    public function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function send($content, $status = 200)
    {
        http_response_code($status);
        echo $content;
        exit;
    }

    public function setCorsHeaders()
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
        
        // Allow specific origins in production
        $allowedOrigins = [
            'http://localhost:5173',
            'http://localhost:3000',
            'https://arbit.duckdns.org'
        ];
        
        if (in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
        } else {
            header("Access-Control-Allow-Origin: *");
        }
        
        header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Allow-Credentials: true');
    }

    public function redirect($url, $status = 302)
    {
        http_response_code($status);
        header("Location: $url");
        exit;
    }
}