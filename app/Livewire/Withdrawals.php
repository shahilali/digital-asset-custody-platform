<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Withdrawal;

class Withdrawals extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $withdrawals = Withdrawal::with(['account', 'metal'])
            ->when($this->search, function($query) {
                $query->whereHas('account', function($q) {
                    $q->where('customer_name', 'like', '%' . $this->search . '%')
                      ->orWhere('account_number', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.withdrawals', [
            'withdrawals' => $withdrawals,
        ]);
    }
}
