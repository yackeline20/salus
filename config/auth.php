<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'personas',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'multi', // Proveedor personalizado que maneja múltiples modelos
        ],
        
        // Guard específico para personas
        'personas' => [
            'driver' => 'session',
            'provider' => 'personas',
        ],

        // Guard específico para usuarios
        'usuarios' => [
            'driver' => 'session',
            'provider' => 'usuarios',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        // Provider multi-modelo para el guard web
        'multi' => [
            'driver' => 'eloquent',
            'model' => App\Models\Usuario::class, // Modelo principal
        ],

        // Provider para usuarios (tabla usuarios)
        'usuarios' => [
            'driver' => 'eloquent',
            'model' => App\Models\Usuario::class,
        ],

        // Provider para personas (existente)
        'personas' => [
            'driver' => 'eloquent',
            'model' => App\Models\Persona::class,
        ],

        // Mantén el provider original por compatibilidad
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        // Provider para correos
        'correos' => [
            'driver' => 'eloquent',
            'model' => App\Models\Correo::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        // Configuración para usuarios
        'usuarios' => [
            'provider' => 'usuarios',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        // Configuración para personas
        'personas' => [
            'provider' => 'personas',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        // Mantén la configuración original
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        // Configuración para correos
        'correos' => [
            'provider' => 'correos',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];