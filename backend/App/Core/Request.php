<?php

namespace App\Core;

class Request
{
    private $params = [];

    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getPath()
    {
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // Remove query string
        if (($pos = strpos($requestUri, '?')) !== false) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        
        // Get the script directory (e.g., /backend/public)
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        
        // Remove the script directory from the request URI to get clean path
        if ($scriptDir !== '/' && strpos($requestUri, $scriptDir) === 0) {
            $path = substr($requestUri, strlen($scriptDir));
        } else {
            $path = $requestUri;
        }
        
        // Ensure path starts with /
        if (empty($path) || $path[0] !== '/') {
            $path = '/' . $path;
        }
        
        return $path;
    }

    public function getBody()
    {
        return file_get_contents('php://input');
    }

    public function getJson()
    {
        $body = $this->getBody();
        return json_decode($body, true);
    }

    public function getHeader($name)
    {
        $name = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $_SERVER[$name] ?? null;
    }

    public function getBearerToken()
    {
        $header = $this->getHeader('Authorization');
        if ($header && strpos($header, 'Bearer ') === 0) {
            return substr($header, 7);
        }
        return null;
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function getParam($name, $default = null)
    {
        return $this->params[$name] ?? $default;
    }

    public function getQuery($name, $default = null)
    {
        return $_GET[$name] ?? $default;
    }

    public function getPost($name, $default = null)
    {
        return $_POST[$name] ?? $default;
    }

    public function isPost()
    {
        return $this->getMethod() === 'POST';
    }

    public function isGet()
    {
        return $this->getMethod() === 'GET';
    }

    public function isAjax()
    {
        return $this->getHeader('X-Requested-With') === 'XMLHttpRequest';
    }

    public function wantsJson()
    {
        $accept = $this->getHeader('Accept');
        return $accept && strpos($accept, 'application/json') !== false;
    }
}