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

    'tourleader' => [
        'driver' => 'sanctum',
        'provider' => 'tourleaders',
    ],

    // 🔥 TAMBAHKAN INI
    'muthawif' => [
        'driver' => 'sanctum',
        'provider' => 'muthawifs',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],

    'tourleaders' => [
        'driver' => 'eloquent',
        'model' => App\Models\TourLeader::class,
    ],

    'muthawifs' => [
        'driver' => 'eloquent',
        'model' => App\Models\Muthawif::class,
    ],
],

];
