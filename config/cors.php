<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel Vibes CORS Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration controls Cross-Origin Resource Sharing (CORS) settings
    | for the Laravel Vibes package. These settings determine which origins,
    | methods, and headers are allowed when clients interact with your Vibes
    | endpoints from different domains.
    |
    | For production environments, you should restrict 'allowed_origins' to
    | only the domains that need access to your AI agent functionality.
    |
    */
    'vibes' => [
        // Which origins can connect to your Vibes endpoints ('*' allows all origins)
        'allowed_origins' => ['*'],
        
        // HTTP methods permitted for cross-origin requests
        'allowed_methods' => ['GET', 'POST', 'OPTIONS'],
        
        // HTTP headers allowed in cross-origin requests
        'allowed_headers' => ['Content-Type', 'X-Requested-With', 'Authorization'],
        
        // How long browsers should cache CORS preflight responses (in seconds)
        'max_age' => 86400,
    ]
];
