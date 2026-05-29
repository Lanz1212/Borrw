<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model DamagedItem
 * 
 * Merepresentasikan data barang yang rusak atau hilang saat proses pengembalian atau inspeksi.
 */
class DamagedItem extends Model
{
    /**
     * Kolom yang dapat diisi secara massal.
     */
    protected $fillable = [
        'inventory_id', 'item_name', 'qty', 'description', 'condition_notes',
        'transaction_id', 'reported_by', 'reported_by_name',
    ];

    /**
     * Casting tipe data kolom agar sesuai.
     */
    protected $casts = [
        'qty' => 'integer',
    ];

    /**
     * Relasi Many-to-One ke tabel inventory.
     * Mendapatkan data barang utama yang mengalami kerusakan.
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Relasi Many-to-One ke tabel transactions.
     * Mendapatkan data transaksi yang terkait dengan kerusakan barang ini (jika ada).
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Relasi Many-to-One ke tabel users.
     * Mendapatkan data pengguna yang melaporkan kerusakan.
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
