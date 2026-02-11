<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocatedBar extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number',
        'metal_id',
        'account_id',
        'deposit_id',
    ];

    public function metal()
    {
        return $this->belongsTo(Metal::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function deposit()
    {
        return $this->belongsTo(Deposit::class);
    }
}
