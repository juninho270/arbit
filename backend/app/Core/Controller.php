<?php

namespace App\Core;

/**
 * Base Controller Class
 * All controllers should extend this class
 */
abstract class Controller
{
    protected $request;
    protected $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Validate request data
     */
    protected function validate($rules)
    {
        $errors = [];
        $data = $this->request->all();

        foreach ($rules as $field => $rule) {
            $ruleList = explode('|', $rule);
            
            foreach ($ruleList as $singleRule) {
                $error = $this->validateField($field, $data[$field] ?? null, $singleRule);
                if ($error) {
                    $errors[$field][] = $error;
                }
            }
        }

        if (!empty($errors)) {
            $this->response->json([
                'error' => true,
                'message' => 'Validation failed',
                'errors' => $errors
            ], 422);
        }

        return $data;
    }

    /**
     * Validate a single field
     */
    private function validateField($field, $value, $rule)
    {
        if ($rule === 'required' && empty($value)) {
            return "The {$field} field is required.";
        }

        if ($rule === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return "The {$field} must be a valid email address.";
        }

        if (strpos($rule, 'min:') === 0) {
            $min = (int) substr($rule, 4);
            if (!empty($value) && strlen($value) < $min) {
                return "The {$field} must be at least {$min} characters.";
            }
        }

        if (strpos($rule, 'max:') === 0) {
            $max = (int) substr($rule, 4);
            if (!empty($value) && strlen($value) > $max) {
                return "The {$field} may not be greater than {$max} characters.";
            }
        }

        if ($rule === 'numeric' && !empty($value) && !is_numeric($value)) {
            return "The {$field} must be a number.";
        }

        return null;
    }

    /**
     * Get authenticated user
     */
    protected function user()
    {
        return Auth::user();
    }

    /**
     * Check if user is authenticated
     */
    protected function isAuthenticated()
    {
        return Auth::check();
    }

    /**
     * Require authentication
     */
    protected function requireAuth()
    {
        if (!$this->isAuthenticated()) {
            $this->response->json([
                'error' => true,
                'message' => 'Authentication required'
            ], 401);
        }
    }

    /**
     * Require admin role
     */
    protected function requireAdmin()
    {
        $this->requireAuth();
        
        $user = $this->user();
        if (!$user || $user['role'] !== 'admin') {
            $this->response->json([
                'error' => true,
                'message' => 'Admin access required'
            ], 403);
        }
    }
}