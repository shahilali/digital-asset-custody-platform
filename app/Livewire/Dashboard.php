<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Account;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    public $search = '';
    public $showAccountModal = false;
    public $customer_name = '';
    public $account_type = 'retail';

    public $showViewModal = false;
    public $selectedAccount = null;

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

    public function viewAccount($accountId)
    {
        $this->selectedAccount = Account::with(['deposits.metal', 'withdrawals.metal', 'allocatedBars.metal'])
            ->findOrFail($accountId);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->selectedAccount = null;
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

        return view('livewire.dashboard', [
            'accounts' => $accounts,
        ]);
    }
}
