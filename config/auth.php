<?php

use App\Models\User;

return [

    // Guard dan broker default
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    // Definisi guard autentikasi (contoh: web menggunakan session)
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    // Definisi provider pengguna (contoh: mengambil user dari Eloquent)
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', User::class),
        ],
    ],

    // Konfigurasi fitur reset password
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60, // Waktu kedaluwarsa token (menit)
            'throttle' => 60, // Jeda sebelum meminta token baru (detik)
        ],
    ],

    // Batas waktu konfirmasi password sebelum kedaluwarsa (detik)
    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
