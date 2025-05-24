<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:5173'], // Specify the frontend origin
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
