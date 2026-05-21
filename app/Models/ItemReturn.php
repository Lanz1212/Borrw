<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemReturn extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'transaction_id', 'transaction_detail_id', 'inventory_id',
        'item_name', 'qty_returned', 'qty_good', 'qty_damaged', 'qty_lost',
        'condition', 'notes', 'processed_by', 'processed_by_name',
    ];

    protected $casts = [
        'qty_returned' => 'integer',
        'qty_good'     => 'integer',
        'qty_damaged'  => 'integer',
        'qty_lost'     => 'integer',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function transactionDetail()
    {
        return $this->belongsTo(TransactionDetail::class, 'transaction_detail_id');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
