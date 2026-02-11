<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'withdrawal_number',
        'account_id',
        'metal_id',
        'quantity_kg',
    ];

    protected $casts = [
        'quantity_kg' => 'decimal:3',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function metal()
    {
        return $this->belongsTo(Metal::class);
    }
}
