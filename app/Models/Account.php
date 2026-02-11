<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'account_number',
        'account_type',
    ];

    protected $casts = [
        'account_type' => 'string',
        'account_number' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($account) {

            $lastId = self::max('id') + 1;

            $account->account_number = 'ACC-' . str_pad($lastId, 6, '0', STR_PAD_LEFT);
        });
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function allocatedBars()
    {
        return $this->hasMany(AllocatedBar::class);
    }
}
