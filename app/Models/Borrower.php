<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrower extends Model
{
    protected $fillable = ['name', 'contact', 'department', 'notes'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
