<?php

return [
    'middlewares' => [
        'auth:sanctum',
        'api.bypass', // Tambahkan middleware custom kita
    ],
    'authentication' => [
        'enabled' => true,
        'guard' => 'sanctum',
    ],  
    // Disable permission checking jika menggunakan Shield
    'permissions' => [
        'enabled' => false,
    ],
    'navigation' => [
        'token' => [
            'cluster' => null,
            'group' => 'User',
            'sort' => -1,
            'icon' => 'heroicon-o-key',
            'should_register_navigation' => false,
        ],
    ],
    'models' => [
        'token' => [
            'enable_policy' => true,
        ],
    ],
    'route' => [
        'panel_prefix' => false,
        'use_resource_middlewares' => false,
    ],
    'tenancy' => [
        'enabled' => false,
        'awareness' => false,
    ],
    'login-rules' => [
        'email' => 'required|email',
        'password' => 'required',
    ],
    'login-middleware' => [
        // Add any additional middleware you want to apply to the login route
    ],
    'logout-middleware' => [
        'auth:sanctum',
        // Add any additional middleware you want to apply to the logout route
    ],
    'use-spatie-permission-middleware' => false,
];
