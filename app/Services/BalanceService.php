<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Metal;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\MetalPrice;
use App\Models\AllocatedBar;
use Illuminate\Support\Facades\DB;

class BalanceService
{
    /**
     * Resolve account identifier to account ID
     * Accepts either account_id (int) or account_number (string)
     */
    protected function resolveAccountId(int|string $accountIdentifier): int
    {
        if (is_int($accountIdentifier)) {
            return $accountIdentifier;
        }

        $account = Account::where('account_number', $accountIdentifier)->first();

        if (!$account) {
            throw new \Exception("Account not found: {$accountIdentifier}");
        }

        return $account->id;
    }

    /**
     * Get unallocated balance in kg for an account and metal
     * @param int|string $accountIdentifier Account ID or Account Number
     */
    public function unallocatedBalanceKg(int|string $accountIdentifier, int $metalId): float
    {
        $accountId = $this->resolveAccountId($accountIdentifier);

        $deposited = Deposit::where('account_id', $accountId)
            ->where('metal_id', $metalId)
            ->where('storage_type', 'unallocated')
            ->sum('quantity_kg');

        $withdrawn = Withdrawal::where('account_id', $accountId)
            ->where('metal_id', $metalId)
            ->sum('quantity_kg');

        return (float) ($deposited - $withdrawn);
    }

    /**
     * Get allocated bars for an account and metal
     * @param int|string $accountIdentifier Account ID or Account Number
     */
    public function allocatedBars(int|string $accountIdentifier, int $metalId)
    {
        $accountId = $this->resolveAccountId($accountIdentifier);

        return AllocatedBar::where('account_id', $accountId)
            ->where('metal_id', $metalId)
            ->get();
    }

    /**
     * Get total allocated weight in kg for an account and metal
     * @param int|string $accountIdentifier Account ID or Account Number
     */
    public function allocatedWeightKg(int|string $accountIdentifier, int $metalId): float
    {
        $accountId = $this->resolveAccountId($accountIdentifier);

        return AllocatedBar::where('account_id', $accountId)
            ->where('metal_id', $metalId)
            ->sum('weight_kg');
    }

    /**
     * Get unallocated value for an account and metal
     * @param int|string $accountIdentifier Account ID or Account Number
     */
    public function unallocatedValue(int|string $accountIdentifier, int $metalId): float
    {
        $balanceKg = $this->unallocatedBalanceKg($accountIdentifier, $metalId);

        $price = MetalPrice::where('metal_id', $metalId)
            ->latest()
            ->value('price_per_kg');

        return $balanceKg * $price;
    }

    /**
     * Get allocated balance in kg for an account and metal
     * @param int|string $accountIdentifier Account ID or Account Number
     */
    public function allocatedBalanceKg(int|string $accountIdentifier, int $metalId, float $standardBarWeight = 1.0): float
    {
        $accountId = $this->resolveAccountId($accountIdentifier);

        $barCount = AllocatedBar::where('account_id', $accountId)
            ->where('metal_id', $metalId)
            ->count();

        return $barCount * $standardBarWeight;
    }
}
