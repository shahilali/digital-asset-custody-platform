<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\AllocatedBar;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Exception;

class DepositService
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
            throw new Exception("Account not found: {$accountIdentifier}");
        }

        return $account->id;
    }

    /**
     * Create a deposit
     *
     * @param int|string $accountIdentifier Account ID or Account Number
     * @param int $metalId
     * @param string $storageType 'unallocated' or 'allocated'
     * @param float $quantityKg
     * @param array|null $serialNumbers required if allocated
     * @return Deposit
     */
    public function createDeposit(int|string $accountIdentifier, int $metalId, string $storageType, float $quantityKg, array $serialNumbers = null): Deposit
    {
        $accountId = $this->resolveAccountId($accountIdentifier);

        return DB::transaction(function () use ($accountId, $metalId, $storageType, $quantityKg, $serialNumbers) {

            // Validate storage type
            if (!in_array($storageType, ['unallocated', 'allocated'])) {
                throw new Exception("Invalid storage type: $storageType");
            }

            // For allocated, serial numbers are required
            if ($storageType === 'allocated' && empty($serialNumbers)) {
                throw new Exception("Allocated storage requires serial numbers");
            }

            // Create deposit record
            $deposit = Deposit::create([
                'deposit_number' => 'DEP-' . uniqid(),
                'account_id' => $accountId,
                'metal_id' => $metalId,
                'storage_type' => $storageType,
                'quantity_kg' => $quantityKg,
            ]);

            // Handle allocated bars
            if ($storageType === 'allocated') {
                foreach ($serialNumbers as $serial) {
                    // Check for duplicate serial
                    if (AllocatedBar::where('serial_number', $serial)->exists()) {
                        throw new Exception("Serial number already exists: $serial");
                    }

                    AllocatedBar::create([
                        'serial_number' => $serial,
                        'account_id' => $accountId,
                        'metal_id' => $metalId,
                        'deposit_id' => $deposit->id,
                        // weight is optional or standard
                    ]);
                }
            }

            return $deposit;
        });
    }
}
