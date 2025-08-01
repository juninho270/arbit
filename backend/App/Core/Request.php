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
        $path = $_SERVER['REQUEST_URI'];
        
        // Remove query string
        if (($pos = strpos($path, '?')) !== false) {
            $path = substr($path, 0, $pos);
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
}