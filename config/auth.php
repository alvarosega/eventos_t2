<?php

return [
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'externo' => [
            'driver' => 'session',
            'provider' => 'externos',
        ],

        'empleado' => [
            'driver' => 'session',
            'provider' => 'empleados',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'externos' => [
            'driver' => 'eloquent',
            'model' => App\Models\Externo::class,
        ],

        'empleados' => [
            'driver' => 'eloquent',
            'model' => App\Models\Empleado::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],

        'externos' => [
            'provider' => 'externos',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],

        'empleados' => [
            'provider' => 'empleados',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];