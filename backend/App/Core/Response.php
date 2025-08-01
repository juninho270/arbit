<?php

namespace App\Core;

/**
 * HTTP Response Handler
 * Handles HTTP responses and provides easy methods for different response types
 */
class Response
{
    private $headers = [];
    private $statusCode = 200;

    /**
     * Set HTTP status code
     */
    public function status($code)
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Set response header
     */
    public function header($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Set CORS headers
     */
    public function setCorsHeaders()
    {
        $config = include CONFIG_PATH . '/app.php';
        
        $this->header('Access-Control-Allow-Origin', $config['frontend_url']);
        $this->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $this->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $this->header('Access-Control-Allow-Credentials', 'true');
        
        return $this;
    }

    /**
     * Send JSON response
     */
    public function json($data, $statusCode = 200)
    {
        $this->status($statusCode);
        $this->header('Content-Type', 'application/json');
        
        $this->sendHeaders();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Send plain text response
     */
    public function send($content, $statusCode = 200)
    {
        $this->status($statusCode);
        
        $this->sendHeaders();
        echo $content;
        exit;
    }

    /**
     * Redirect to another URL
     */
    public function redirect($url, $statusCode = 302)
    {
        $this->status($statusCode);
        $this->header('Location', $url);
        
        $this->sendHeaders();
        exit;
    }

    /**
     * Render a view
     */
    public function view($viewName, $data = [])
    {
        $viewFile = VIEWS_PATH . '/' . str_replace('.', '/', $viewName) . '.php';
        
        if (!file_exists($viewFile)) {
            throw new \Exception("View {$viewName} not found");
        }
        
        // Extract data to variables
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        include $viewFile;
        
        // Get the content and clean the buffer
        $content = ob_get_clean();
        
        $this->header('Content-Type', 'text/html');
        $this->sendHeaders();
        echo $content;
        exit;
    }

    /**
     * Send all headers
     */
    private function sendHeaders()
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            
            foreach ($this->headers as $key => $value) {
                header("{$key}: {$value}");
            }
        }
    }
}