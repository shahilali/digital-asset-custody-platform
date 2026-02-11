<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Account;
use App\Models\Metal;
use App\Models\Deposit;
use App\Services\DepositService;
use App\Services\WithdrawalService;
use App\Services\BalanceService;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    public $search = '';
    public $showAccountModal = false;
    public $customer_name = '';
    public $account_type = 'retail';

    public $showDepositModal = false;
    public $depositAccountId = null;
    public $depositAccount = null;
    public $metal_id = '';
    public $storage_type = 'unallocated';
    public $quantity_kg = '';
    public $serial_numbers = '';
    public $existingStorageType = null;

    public $showWithdrawalModal = false;
    public $withdrawalAccountId = null;
    public $withdrawalAccount = null;
    public $withdrawal_metal_id = '';
    public $withdrawal_quantity_kg = '';
    public $selectedBarIds = [];
    public $availableBars = [];
    public $balanceKg = 0;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openAccountModal()
    {
        $this->showAccountModal = true;
        $this->customer_name = '';
        $this->account_type = 'retail';
    }

    public function closeAccountModal()
    {
        $this->showAccountModal = false;
        $this->reset(['customer_name', 'account_type']);
    }

    public function saveAccount()
    {
        $this->validate([
            'customer_name' => 'required|string|max:255',
            'account_type' => 'required|in:retail,institutional',
        ]);

        Account::create([
            'customer_name' => $this->customer_name,
            'account_type' => $this->account_type,
        ]);

        session()->flash('message', 'Account created successfully.');
        $this->closeAccountModal();
        $this->resetPage();
    }

    public function openDepositModal($accountId)
    {
        $account = Account::findOrFail($accountId);

        $this->depositAccountId = $accountId;
        $this->depositAccount = $account;
        $this->showDepositModal = true;
        $this->metal_id = '';

        // Set storage type based on account type
        // retail = unallocated, institutional = allocated
        $this->storage_type = $account->account_type === 'institutional' ? 'allocated' : 'unallocated';

        $this->quantity_kg = '';
        $this->serial_numbers = '';
        $this->existingStorageType = null;
    }

    public function updatedMetalId()
    {
        // Check if this account already has deposits for this metal
        if ($this->depositAccountId && $this->metal_id) {
            $existingDeposit = Deposit::with('allocatedBars')
                ->where('account_id', $this->depositAccountId)
                ->where('metal_id', $this->metal_id)
                ->first();

            if ($existingDeposit) {
                // Determine storage type based on allocated bars
                $storageType = $existingDeposit->allocatedBars->isNotEmpty() ? 'allocated' : 'unallocated';
                $this->existingStorageType = $storageType;
                $this->storage_type = $storageType;
            } else {
                $this->existingStorageType = null;
                // Restore storage type based on account type using the already-loaded depositAccount
                if ($this->depositAccount) {
                    $this->storage_type = $this->depositAccount->account_type === 'institutional' ? 'allocated' : 'unallocated';
                }
            }
        }
    }

    public function closeDepositModal()
    {
        $this->showDepositModal = false;
        $this->reset(['depositAccountId', 'metal_id', 'storage_type', 'quantity_kg', 'serial_numbers']);
    }

    public function saveDeposit(DepositService $depositService)
    {
        // Ensure storage_type is set based on account type if it's null
        if (empty($this->storage_type) && $this->depositAccountId) {
            $account = Account::findOrFail($this->depositAccountId);
            $this->storage_type = $account->account_type === 'institutional' ? 'allocated' : 'unallocated';
        }

        $this->validate([
            'depositAccountId' => 'required|integer|exists:accounts,id',
            'metal_id' => 'required|integer|exists:metals,id',
            'storage_type' => 'required|in:allocated,unallocated',
            'quantity_kg' => 'required|numeric|min:0.001',
            'serial_numbers.*' => 'string|distinct',
        ]);

        // Validate storage type matches account type
        $account = Account::findOrFail($this->depositAccountId);
        $expectedStorageType = $account->account_type === 'institutional' ? 'allocated' : 'unallocated';

        if ($this->storage_type !== $expectedStorageType) {
            session()->flash('error', ucfirst($account->account_type) . " accounts can only have {$expectedStorageType} deposits.");
            return;
        }

        // Validate storage type consistency
        if ($this->existingStorageType && $this->storage_type !== $this->existingStorageType) {
            session()->flash('error', "This account already has {$this->existingStorageType} deposits for this metal. All deposits must be of the same storage type.");
            return;
        }

        try {
            // Convert serial numbers string to array if allocated
            $serialNumbers = null;
            if ($this->storage_type === 'allocated' && !empty($this->serial_numbers)) {
                $serialNumbers = array_filter(
                    array_map('trim', explode("\n", $this->serial_numbers)),
                    fn($value) => !empty($value)
                );

                // Validate that number of serial numbers matches quantity in kg
                $serialCount = count($serialNumbers);
                $quantityKg = (int) $this->quantity_kg;

                if ($serialCount !== $quantityKg) {
                    session()->flash('error', "Number of serial numbers ({$serialCount}) must match the quantity in kg ({$quantityKg}).");
                    return;
                }
            }

            $deposit = $depositService->createDeposit(
                $this->depositAccountId,
                $this->metal_id,
                $this->storage_type,
                $this->quantity_kg,
                $serialNumbers
            );

            session()->flash('message', "Deposit {$deposit->deposit_number} created successfully.");
            $this->closeDepositModal();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function openWithdrawalModal($accountId)
    {
        $account = Account::findOrFail($accountId);

        $this->withdrawalAccountId = $accountId;
        $this->withdrawalAccount = $account;
        $this->showWithdrawalModal = true;
        $this->withdrawal_metal_id = '';
        $this->withdrawal_quantity_kg = '';
        $this->selectedBarIds = [];
        $this->availableBars = [];
        $this->balanceKg = 0;
    }

    public function updatedWithdrawalMetalId(BalanceService $balanceService)
    {
        if (!$this->withdrawalAccount || !$this->withdrawal_metal_id) {
            return;
        }

        if ($this->withdrawalAccount->account_type === 'retail') {
            $this->balanceKg = $balanceService->unallocatedBalanceKg(
                $this->withdrawalAccountId,
                $this->withdrawal_metal_id
            );
        } else {
            $this->availableBars = $balanceService->allocatedBars(
                $this->withdrawalAccountId,
                $this->withdrawal_metal_id
            );
        }
    }

    public function closeWithdrawalModal()
    {
        $this->showWithdrawalModal = false;
        $this->reset(['withdrawalAccountId', 'withdrawalAccount', 'withdrawal_metal_id', 'withdrawal_quantity_kg', 'selectedBarIds', 'availableBars', 'balanceKg']);
    }

    public function saveWithdrawal(WithdrawalService $withdrawalService)
    {
        try {
            if ($this->withdrawalAccount->account_type === 'retail') {
                $this->validate([
                    'withdrawalAccountId' => 'required|integer|exists:accounts,id',
                    'withdrawal_metal_id' => 'required|integer|exists:metals,id',
                    'withdrawal_quantity_kg' => 'required|numeric|min:0.001',
                ]);

                $withdrawal = $withdrawalService->createUnallocated(
                    $this->withdrawalAccountId,
                    $this->withdrawal_metal_id,
                    $this->withdrawal_quantity_kg
                );

                session()->flash('message', "Withdrawal {$withdrawal->withdrawal_number} created successfully.");
            } else {
                $this->validate([
                    'withdrawalAccountId' => 'required|integer|exists:accounts,id',
                    'withdrawal_metal_id' => 'required|integer|exists:metals,id',
                    'selectedBarIds' => 'required|array|min:1',
                ]);

                $withdrawals = $withdrawalService->createAllocated(
                    $this->withdrawalAccountId,
                    $this->withdrawal_metal_id,
                    $this->selectedBarIds
                );

                $count = count($withdrawals);
                session()->flash('message', "{$count} withdrawal(s) created successfully.");
            }

            $this->closeWithdrawalModal();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $accounts = Account::query()
            ->when($this->search, function($query) {
                $query->where('customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('account_number', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $metals = Metal::all();

        return view('livewire.dashboard', [
            'accounts' => $accounts,
            'metals' => $metals,
        ]);
    }
}
