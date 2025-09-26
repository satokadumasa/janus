<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'supports_credentials' => true,
    'allowed_origins' => ['*'],
    // 'allowed_origins' => [
    //     'http://dev3-kurapital.pcrm.work/',
    //     'http://dev3-partner.pcrm.work/',
    // ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'allowed_methods' => ['*'],
    'exposed_headers' => [],
    // 'paths' => ['*'],
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],
    'max_age' => 86400604800,
];
