<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_code', 'borrower_id', 'borrower_name',
        'loan_date', 'return_date', 'status',
        'notes', 'created_by', 'created_by_name', 'signature',
    ];

    protected $casts = [
        'loan_date'   => 'datetime',
        'return_date' => 'datetime',
    ];

    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function returns()
    {
        return $this->hasMany(ItemReturn::class);
    }

    public function damagedItems()
    {
        return $this->hasMany(DamagedItem::class);
    }
}
