<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = [
        'code', 'name', 'category', 'type',
        'total_qty', 'available_qty', 'min_stock',
        'condition', 'notes',
    ];

    protected $casts = [
        'total_qty'     => 'integer',
        'available_qty' => 'integer',
        'min_stock'     => 'integer',
    ];

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function damagedItems()
    {
        return $this->hasMany(DamagedItem::class);
    }

    public function isLowStock(): bool
    {
        return $this->min_stock > 0 && $this->available_qty <= $this->min_stock;
    }
}
