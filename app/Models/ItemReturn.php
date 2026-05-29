<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model ItemReturn
 * 
 * Merepresentasikan data pengembalian barang dari suatu transaksi peminjaman.
 */
class ItemReturn extends Model
{
    protected $table = 'returns';

    /**
     * Kolom yang dapat diisi secara massal.
     */
    protected $fillable = [
        'transaction_id', 'transaction_detail_id', 'inventory_id',
        'item_name', 'qty_returned', 'qty_good', 'qty_damaged', 'qty_lost',
        'condition', 'notes', 'processed_by', 'processed_by_name',
    ];

    /**
     * Casting nilai kuantitas menjadi integer.
     */
    protected $casts = [
        'qty_returned' => 'integer',
        'qty_good'     => 'integer',
        'qty_damaged'  => 'integer',
        'qty_lost'     => 'integer',
    ];

    /**
     * Relasi Many-to-One ke tabel transactions.
     * Mendapatkan data transaksi peminjaman asal.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Relasi Many-to-One ke tabel transaction_details.
     * Mendapatkan detail spesifik barang yang dikembalikan.
     */
    public function transactionDetail()
    {
        return $this->belongsTo(TransactionDetail::class, 'transaction_detail_id');
    }

    /**
     * Relasi Many-to-One ke tabel inventory.
     * Mendapatkan data inventaris barang yang dikembalikan.
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Relasi Many-to-One ke tabel users.
     * Mendapatkan admin/petugas yang memproses pengembalian ini.
     */
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
