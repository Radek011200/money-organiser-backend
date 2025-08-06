<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'register', 'logout', 'auth/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173',
        'http://172.25.21.191:5173',
        'http://192.168.1.17:5173',
        'http://127.0.0.1:5173'
    ], // Specify the frontend origin
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Enable credentials support
];
//'allowed_origins' => [
//    'http://localhost:5173',
//    'https://your-production-domain.com',
//     Add other origins as needed
//],
