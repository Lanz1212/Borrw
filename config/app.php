<?php

return [

    // Nama Aplikasi
    'name' => env('APP_NAME', 'Laravel'),

    // Environment aplikasi (local, production, dsb.)
    'env' => env('APP_ENV', 'production'),

    // Tampilkan pesan error detail (true) atau tidak (false)
    'debug' => (bool) env('APP_DEBUG', false),

    // URL utama aplikasi
    'url' => env('APP_URL', 'http://localhost'),

    // Zona waktu default untuk fungsi date/time PHP
    'timezone' => 'Asia/Jakarta',

    // Bahasa default aplikasi
    'locale' => env('APP_LOCALE', 'en'),

    // Bahasa cadangan jika terjemahan tidak ditemukan
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    // Bahasa lokal untuk library Faker
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    // Algoritma enkripsi yang digunakan
    'cipher' => 'AES-256-CBC',

    // Kunci enkripsi utama aplikasi
    'key' => env('APP_KEY'),

    // Kunci enkripsi lawas (jika ada) untuk dekripsi data lama
    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    // Konfigurasi mode pemeliharaan (maintenance)
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store'  => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
