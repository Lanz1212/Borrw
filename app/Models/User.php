<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model User
 * 
 * Merepresentasikan data pengguna sistem dan autentikasi.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'active',
    ];

    /**
     * Kolom yang disembunyikan dari array atau representasi JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Menentukan casting tipe data untuk kolom tertentu.
     */
    protected function casts(): array
    {
        return [
            'password'  => 'hashed',
            'active'    => 'boolean',
        ];
    }

    /**
     * Memeriksa apakah pengguna memiliki hak akses sebagai admin.
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Relasi One-to-Many ke tabel transactions.
     * Mengambil semua transaksi yang dibuat oleh pengguna ini.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'created_by');
    }
}
