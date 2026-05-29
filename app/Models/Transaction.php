<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Transaction
 * 
 * Merepresentasikan data transaksi peminjaman barang utama.
 */
class Transaction extends Model
{
    /**
     * Kolom yang dapat diisi secara massal.
     */
    protected $fillable = [
        'transaction_code', 'borrower_id', 'borrower_name',
        'loan_date', 'return_date', 'status',
        'notes', 'created_by', 'created_by_name', 'signature',
    ];

    /**
     * Casting format tanggal menjadi instance Carbon (datetime).
     */
    protected $casts = [
        'loan_date'   => 'datetime',
        'return_date' => 'datetime',
    ];

    /**
     * Relasi Many-to-One ke tabel borrowers.
     * Mendapatkan data peminjam yang melakukan transaksi ini.
     */
    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }

    /**
     * Relasi Many-to-One ke tabel users (alias creator).
     * Mendapatkan data user/admin yang membuat transaksi ini.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi One-to-Many ke tabel transaction_details.
     * Mendapatkan daftar barang yang dipinjam dalam transaksi ini.
     */
    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    /**
     * Relasi One-to-Many ke tabel item_returns.
     * Mendapatkan data pengembalian barang untuk transaksi ini.
     */
    public function returns()
    {
        return $this->hasMany(ItemReturn::class);
    }

    /**
     * Relasi One-to-Many ke tabel damaged_items.
     * Mendapatkan data barang rusak yang terkait dengan transaksi ini.
     */
    public function damagedItems()
    {
        return $this->hasMany(DamagedItem::class);
    }
}
