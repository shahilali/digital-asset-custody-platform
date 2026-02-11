<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Account;
use App\Models\Metal;
use App\Services\BalanceService;

class AccountView extends Component
{
    public $accountId;
    public $account;

    public function mount($id)
    {
        $this->accountId = $id;
        $this->account = Account::with(['deposits.metal', 'withdrawals.metal', 'allocatedBars.metal'])
            ->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.account-view');
    }
}
