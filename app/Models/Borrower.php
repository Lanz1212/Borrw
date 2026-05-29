<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Borrower
 * 
 * Merepresentasikan data peminjam barang.
 */
class Borrower extends Model
{
    /**
     * Kolom yang dapat diisi secara massal.
     */
    protected $fillable = ['name', 'contact', 'department', 'notes'];

    /**
     * Relasi One-to-Many ke tabel transactions.
     * Mendapatkan daftar transaksi peminjaman yang dilakukan oleh peminjam ini.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
