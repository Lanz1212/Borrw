<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamagedItem extends Model
{
    protected $fillable = [
        'inventory_id', 'item_name', 'qty', 'description', 'condition_notes',
        'transaction_id', 'reported_by', 'reported_by_name',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
