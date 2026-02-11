<?php

namespace App\Services;

use App\Models\Withdrawal;
use App\Models\AllocatedBar;
use App\Models\Account;
use App\Services\BalanceService;
use Illuminate\Support\Str;

class WithdrawalService
{
    protected $balanceService;

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

    public function __construct(BalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    /**
     * Create unallocated withdrawal
     * @param int|string $accountIdentifier Account ID or Account Number
     */
    public function createUnallocated(int|string $accountIdentifier, int $metalId, float $quantityKg): Withdrawal
    {
        $accountId = $this->resolveAccountId($accountIdentifier);

        $balance = $this->balanceService->unallocatedBalanceKg($accountId, $metalId);

        if ($quantityKg > $balance) {
            throw new \Exception('Insufficient balance.');
        }

        return Withdrawal::create([
            'withdrawal_number' => 'WD-' . Str::uuid(),
            'account_id' => $accountId,
            'metal_id' => $metalId,
            'quantity_kg' => $quantityKg,
        ]);
    }

    /**
     * Create allocated withdrawal
     * @param int|string $accountIdentifier Account ID or Account Number
     */
    public function createAllocated(int|string $accountIdentifier, int $metalId, array $barIds, float $standardBarWeight = 1.0): array
    {
        $accountId = $this->resolveAccountId($accountIdentifier);

        if (empty($barIds)) {
            throw new \Exception('No bars selected.');
        }

        $withdrawals = [];

        foreach ($barIds as $barId) {
            $bar = AllocatedBar::find($barId);

            if (!$bar || $bar->account_id != $accountId || $bar->metal_id != $metalId) {
                throw new \Exception('Invalid bar selected.');
            }

            $withdrawals[] = Withdrawal::create([
                'withdrawal_number' => 'WD-' . Str::uuid(),
                'account_id' => $accountId,
                'metal_id' => $metalId,
                'quantity_kg' => $standardBarWeight,
            ]);

            $bar->delete();
        }

        return $withdrawals;
    }
}
