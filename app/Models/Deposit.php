<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'deposit_number',
        'account_id',
        'metal_id',
        'storage_type',
        'quantity_kg',
    ];

    protected $casts = [
        'quantity_kg' => 'decimal:3',
        'storage_type' => 'string',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function metal()
    {
        return $this->belongsTo(Metal::class);
    }

    public function allocatedBars()
    {
        return $this->hasMany(AllocatedBar::class);
    }
}
