<?php

/**
 * Application Configuration
 */

return [
    'name' => 'CryptoArb Pro',
    'version' => '1.0.0',
    'debug' => true, // Set to false in production
    'timezone' => 'America/Sao_Paulo',
    'locale' => 'pt_BR',
    
    // Security
    'jwt_secret' => 'your-jwt-secret-key-change-this-in-production',
    'session_lifetime' => 120, // minutes
    
    // External APIs
    'coingecko_api_url' => 'https://api.coingecko.com/api/v3',
    'moralis_api_key' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJub25jZSI6IjMwNDg5ZjY4LWM1OTYtNDE0ZC1iZGZlLTY5ZjE1MDc4MTFmYyIsIm9yZ0lkIjoiNDYyMzE1IiwidXNlcklkIjoiNDc1NjI0IiwidHlwZUlkIjoiZWQyYTAxNTctNzdmZC00MzQyLTlkNGQtNzQwNTM0MzM5NzkyIiwidHlwZSI6IlBST0pFQ1QiLCJpYXQiOjE3NTM4NDYwMjQsImV4cCI6NDkwOTYwNjAyNH0.XL6D6L3ojKu3virQP5pDdc7nCXwjKMEZRWeT09XChfU',
    'moralis_api_url' => 'https://deep-index.moralis.io/api/v2',
    
    // Frontend URL for CORS
    'frontend_url' => 'http://localhost:5173',
];