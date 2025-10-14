<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        // El guard por defecto será 'web'
        'guard' => 'web',
        // El proveedor de contraseñas por defecto será 'usuarios'
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
            // CRÍTICO: Usamos 'usuarios' como proveedor principal.
            'provider' => 'usuarios',
        ],

        // Puedes dejar los guards específicos si los usas en alguna parte,
        // pero el 'web' es el que se usa en el login principal.
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
    */

    'providers' => [
        // Provider principal (usuarios) - Se usará en el guard 'web'
        'usuarios' => [
            'driver' => 'eloquent',
            'model' => App\Models\Usuario::class, // Modelo de usuario autenticable
        ],

        // Provider para personas (existente)
        'personas' => [
            'driver' => 'eloquent',
            'model' => App\Models\Persona::class, // Modelo de persona (NO autenticable por sí mismo)
        ],

        // Puedes eliminar el provider 'multi' y 'correos' si no los usas directamente en guards.
        // Mantenemos el provider original por si acaso (aunque no lo uses).
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
        // Configuración para usuarios (debe usar el provider 'usuarios')
        'usuarios' => [
            'provider' => 'usuarios',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        // Configuración para personas (debe usar el provider 'personas')
        'personas' => [
            'provider' => 'personas',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        // Mantén la configuración original por si acaso
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
