<div class="space-y-6">
    @if (session()->has('message'))
        <div class="rounded-lg bg-green-50 border border-green-200 p-4">
            <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-semibold text-slate-900">Accounts Dashboard</h1>
                    <p class="text-sm text-slate-500">View and manage all customer accounts.</p>
                </div>
                <button wire:click="openAccountModal" class="inline-flex items-center justify-center rounded-md bg-blue-600 p-2 text-white shadow-sm hover:bg-blue-700" title="New Account">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                </button>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <input type="text" wire:model.live="search" placeholder="Search by name or account number..." class="w-full rounded-md border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:border-blue-500">
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-slate-200">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">Account Number</th>
                            <th class="px-3 py-2 text-left font-semibold">Customer Name</th>
                            <th class="px-3 py-2 text-left font-semibold">Account Type</th>
                            <th class="px-3 py-2 text-left font-semibold">Created</th>
                            <th class="px-3 py-2 text-center font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($accounts as $account)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2 font-mono text-sm text-slate-900">{{ $account->account_number }}</td>
                                <td class="px-3 py-2 font-medium text-slate-900">{{ $account->customer_name }}</td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $account->account_type === 'institutional' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700' }}">
                                        {{ ucfirst($account->account_type) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-slate-600">{{ $account->created_at->format('M d, Y') }}</td>
                                <td class="px-3 py-2 text-center space-x-2">
                                    <button wire:click="viewAccount({{ $account->id }})" class="text-blue-700 hover:text-blue-900" title="View Details">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button wire:click="openDepositModal({{ $account->id }})" class="text-green-700 hover:text-green-900" title="New Deposit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                        </svg>
                                    </button>
                                    <button wire:click="openWithdrawalModal({{ $account->id }})" class="text-red-700 hover:text-red-900" title="New Withdrawal">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-8 text-center text-slate-500">
                                    @if($search)
                                        No accounts found matching "{{ $search }}".
                                    @else
                                        No accounts found. Create your first account to get started.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $accounts->links() }}
            </div>
        </div>
    </div>

    <!-- Modals -->
    <x-modals.account-create :show="$showAccountModal" />
    <x-modals.account-view :show="$showViewModal" :account="$selectedAccount" />
    <x-modals.deposit-create :show="$showDepositModal" :metals="$metals" :storageType="$storage_type" :existingStorageType="$existingStorageType" :account="$depositAccount" />
    <x-modals.withdrawal-create :show="$showWithdrawalModal" :metals="$metals" :account="$withdrawalAccount" :balanceKg="$balanceKg" :availableBars="$availableBars" />
</div>
