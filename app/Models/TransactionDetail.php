<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id', 'inventory_id', 'item_name', 'item_code',
        'item_type', 'qty', 'status', 'qty_returned', 'return_date',
    ];

    protected $casts = [
        'qty'          => 'integer',
        'qty_returned' => 'integer',
        'return_date'  => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function returns()
    {
        return $this->hasMany(ItemReturn::class, 'transaction_detail_id');
    }
}
