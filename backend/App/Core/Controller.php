<?php

namespace App\Core;

class Controller
{
    protected function validateRequired($data, $fields)
    {
        $missing = [];
        
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }
        
        if (!empty($missing)) {
            throw new \Exception('Missing required fields: ' . implode(', ', $missing));
        }
    }

    protected function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    protected function render($viewName, $data = [])
    {
        // Extrai os dados para que as variáveis fiquem disponíveis diretamente na view
        extract($data);

        // Inclui o arquivo da view
        $viewPath = VIEWS_PATH . '/' . $viewName . '.php';

        if (!file_exists($viewPath)) {
            throw new \Exception("View file not found: " . $viewPath);
        }

        // Inicia o buffer de saída para capturar o HTML gerado pela view
        ob_start();
        include $viewPath;
        $content = ob_get_clean(); // Captura o conteúdo e limpa o buffer

        echo $content; // Exibe o conteúdo da view
    }

    protected function renderLayout($viewName, $data = [], $layout = 'main')
    {
        // Extrai os dados para que as variáveis fiquem disponíveis diretamente na view
        extract($data);

        // Renderiza a view principal
        $viewPath = VIEWS_PATH . '/' . $viewName . '.php';
        if (!file_exists($viewPath)) {
            throw new \Exception("View file not found: " . $viewPath);
        }

        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        // Renderiza o layout com o conteúdo
        $layoutPath = VIEWS_PATH . '/layouts/' . $layout . '.php';
        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout file not found: " . $layoutPath);
        }

        include $layoutPath;
    }
}