<?php

/**
 * Web Routes
 * Configuração simplificada para Single Page Application (React)
 */

// Health check endpoint
$router->get('/health', function($request, $response) {
    $response->json([
        'status' => 'healthy',
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => '1.0.0'
    ]);
});

// Catch-all route para servir o React App
// IMPORTANTE: Esta rota deve ser a ÚLTIMA, pois captura tudo que não foi processado antes
$router->get('/{path:.*}', function($request, $response) {
    $path = $request->getPath();
    
    // Se for uma rota de API, não processar aqui (deixar para api.php)
    if (strpos($path, '/api/') === 0) {
        $response->json(['error' => 'API route not found'], 404);
        return;
    }
    
    // Para todas as outras rotas, servir o React App
    $indexPath = PUBLIC_PATH . '/index.html';
    
    if (file_exists($indexPath)) {
        // Definir headers apropriados para HTML
        header('Content-Type: text/html; charset=utf-8');
        echo file_get_contents($indexPath);
    } else {
        $response->send('React app not found. Please run "npm run build" and copy files to backend/public/', 404);
    }
});