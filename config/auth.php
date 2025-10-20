<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'usuarios',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'usuarios',
        ],

        'personas' => [
            'driver' => 'session',
            'provider' => 'personas',
        ],

        'usuarios' => [
            'driver' => 'session',
            'provider' => 'usuarios',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    | ✅ CAMBIO CRÍTICO: El provider 'usuarios' ahora usa 'custom_eloquent'
    */

    'providers' => [
        // Provider principal (usuarios) - Usa nuestro CustomUserProvider
        'usuarios' => [
            'driver' => 'custom_eloquent',  // ✅ CAMBIO AQUÍ
            'model' => App\Models\Usuario::class,
        ],

        // Provider para personas
        'personas' => [
            'driver' => 'eloquent',
            'model' => App\Models\Persona::class,
        ],

        // Provider original (por si acaso)
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        'usuarios' => [
            'provider' => 'usuarios',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'personas' => [
            'provider' => 'personas',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
