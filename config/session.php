<?php

use Illuminate\Support\Str;

return [

    // Driver penyimpanan sesi default
    'driver' => env('SESSION_DRIVER', 'database'),

    // Batas waktu sesi sebelum kedaluwarsa (menit)
    'lifetime' => (int) env('SESSION_LIFETIME', 120),

    // Hapus sesi saat browser ditutup
    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),

    // Enkripsi data sesi
    'encrypt' => env('SESSION_ENCRYPT', false),

    // Lokasi file sesi (jika menggunakan driver 'file')
    'files' => storage_path('framework/sessions'),

    // Koneksi database untuk menyimpan sesi
    'connection' => env('SESSION_CONNECTION'),

    // Nama tabel database untuk sesi
    'table' => env('SESSION_TABLE', 'sessions'),

    // Store cache yang digunakan (jika driver sesi berbasis cache)
    'store' => env('SESSION_STORE'),

    // Peluang pembersihan sesi lama secara otomatis (2 dari 100 request)
    'lottery' => [2, 100],

    // Nama cookie untuk sesi
    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug((string) env('APP_NAME', 'laravel')).'-session'
    ),

    // Path cookie sesi
    'path' => env('SESSION_PATH', '/'),

    // Domain cookie sesi
    'domain' => env('SESSION_DOMAIN'),

    // Batasi pengiriman cookie hanya untuk koneksi HTTPS
    'secure' => env('SESSION_SECURE_COOKIE'),

    // Cegah akses cookie sesi melalui JavaScript
    'http_only' => env('SESSION_HTTP_ONLY', true),

    // Kebijakan Same-Site (mengamankan dari serangan CSRF)
    'same_site' => env('SESSION_SAME_SITE', 'lax'),

    // Mengikat cookie ke top-level site
    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

];
