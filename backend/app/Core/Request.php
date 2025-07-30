<?php

namespace App\Core;

/**
 * HTTP Request Handler
 * Handles incoming HTTP requests and provides easy access to request data
 */
class Request
{
    private $method;
    private $path;
    private $query;
    private $body;
    private $headers;
    private $files;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->query = $_GET;
        $this->headers = $this->getAllHeaders();
        $this->files = $_FILES;
        
        // Parse request body
        $this->parseBody();
    }

    /**
     * Get HTTP method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get request path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get query parameters
     */
    public function query($key = null, $default = null)
    {
        if ($key === null) {
            return $this->query;
        }
        
        return $this->query[$key] ?? $default;
    }

    /**
     * Get request body data
     */
    public function input($key = null, $default = null)
    {
        if ($key === null) {
            return $this->body;
        }
        
        return $this->body[$key] ?? $default;
    }

    /**
     * Get all input data (query + body)
     */
    public function all()
    {
        return array_merge($this->query, $this->body);
    }

    /**
     * Get specific input fields
     */
    public function only($keys)
    {
        $result = [];
        $all = $this->all();
        
        foreach ($keys as $key) {
            if (isset($all[$key])) {
                $result[$key] = $all[$key];
            }
        }
        
        return $result;
    }

    /**
     * Check if input has a key
     */
    public function has($key)
    {
        $all = $this->all();
        return isset($all[$key]);
    }

    /**
     * Get header value
     */
    public function header($key, $default = null)
    {
        $key = strtolower($key);
        return $this->headers[$key] ?? $default;
    }

    /**
     * Get uploaded file
     */
    public function file($key)
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Check if request is JSON
     */
    public function isJson()
    {
        return strpos($this->header('content-type', ''), 'application/json') !== false;
    }

    /**
     * Check if request is AJAX
     */
    public function isAjax()
    {
        return $this->header('x-requested-with') === 'XMLHttpRequest';
    }

    /**
     * Get bearer token from Authorization header
     */
    public function bearerToken()
    {
        $header = $this->header('authorization');
        if ($header && strpos($header, 'Bearer ') === 0) {
            return substr($header, 7);
        }
        return null;
    }

    /**
     * Parse request body based on content type
     */
    private function parseBody()
    {
        $this->body = [];
        
        if ($this->method === 'GET') {
            return;
        }
        
        if ($this->isJson()) {
            $json = file_get_contents('php://input');
            $this->body = json_decode($json, true) ?: [];
        } else {
            $this->body = $_POST;
        }
    }

    /**
     * Get all HTTP headers
     */
    private function getAllHeaders()
    {
        $headers = [];
        
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        } else {
            // Fallback for servers that don't have getallheaders()
            foreach ($_SERVER as $key => $value) {
                if (strpos($key, 'HTTP_') === 0) {
                    $header = str_replace('_', '-', substr($key, 5));
                    $headers[$header] = $value;
                }
            }
        }
        
        // Convert keys to lowercase
        return array_change_key_case($headers, CASE_LOWER);
    }
}