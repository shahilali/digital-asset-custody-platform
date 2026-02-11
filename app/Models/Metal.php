<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'symbol',
    ];

    public function price()
    {
        return $this->hasOne(MetalPrice::class);
    }

    public function metalPrices()
    {
        return $this->hasMany(MetalPrice::class);
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
