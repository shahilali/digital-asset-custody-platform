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

    <!-- Account Creation Modal -->
    @if($showAccountModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="closeAccountModal"></div>

                <!-- Modal panel -->
                <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-50">
                    <form wire:submit.prevent="saveAccount">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg font-semibold leading-6 text-slate-900 mb-4" id="modal-title">
                                        Create New Account
                                    </h3>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Customer Name</label>
                                            <input type="text" wire:model="customer_name" placeholder="Enter customer name" class="w-full rounded-md border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:border-blue-500">
                                            @error('customer_name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Account Type</label>
                                            <select wire:model="account_type" class="w-full rounded-md border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:border-blue-500">
                                                <option value="retail">Retail</option>
                                                <option value="institutional">Institutional</option>
                                            </select>
                                            @error('account_type') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 sm:w-auto">
                                Create Account
                            </button>
                            <button type="button" wire:click="closeAccountModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 sm:mt-0 sm:w-auto">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- View Account Modal -->
    @if($showViewModal && $selectedAccount)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="closeViewModal"></div>

                <!-- Modal panel -->
                <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full z-50">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-lg font-semibold leading-6 text-slate-900" id="modal-title">
                                    Account Details
                                </h3>
                                <p class="text-sm text-slate-500 mt-1">{{ $selectedAccount->account_number }}</p>
                            </div>
                            <button wire:click="closeViewModal" class="text-slate-400 hover:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-6">
                            <!-- Account Information -->
                            <div class="grid grid-cols-2 gap-4 p-4 bg-slate-50 rounded-lg">
                                <div>
                                    <p class="text-xs font-medium text-slate-500 uppercase">Customer Name</p>
                                    <p class="text-sm font-semibold text-slate-900 mt-1">{{ $selectedAccount->customer_name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-slate-500 uppercase">Account Type</p>
                                    <p class="mt-1">
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $selectedAccount->account_type === 'institutional' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700' }}">
                                            {{ ucfirst($selectedAccount->account_type) }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-slate-500 uppercase">Created Date</p>
                                    <p class="text-sm font-semibold text-slate-900 mt-1">{{ $selectedAccount->created_at->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-slate-500 uppercase">Last Updated</p>
                                    <p class="text-sm font-semibold text-slate-900 mt-1">{{ $selectedAccount->updated_at->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <!-- Transaction Summary -->
                            <div>
                                <h4 class="text-sm font-semibold text-slate-900 mb-3">Transaction Summary</h4>
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                        <p class="text-xs font-medium text-blue-700 uppercase">Total Deposits</p>
                                        <p class="text-2xl font-bold text-blue-900 mt-1">{{ $selectedAccount->deposits->count() }}</p>
                                    </div>
                                    <div class="p-4 bg-orange-50 rounded-lg border border-orange-200">
                                        <p class="text-xs font-medium text-orange-700 uppercase">Total Withdrawals</p>
                                        <p class="text-2xl font-bold text-orange-900 mt-1">{{ $selectedAccount->withdrawals->count() }}</p>
                                    </div>
                                    <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                                        <p class="text-xs font-medium text-purple-700 uppercase">Allocated Bars</p>
                                        <p class="text-2xl font-bold text-purple-900 mt-1">{{ $selectedAccount->allocatedBars->count() }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Deposits -->
                            @if($selectedAccount->deposits->isNotEmpty())
                                <div>
                                    <h4 class="text-sm font-semibold text-slate-900 mb-3">Recent Deposits</h4>
                                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                                        <table class="min-w-full text-xs">
                                            <thead class="bg-slate-50 text-slate-600">
                                                <tr>
                                                    <th class="px-3 py-2 text-left font-semibold">Deposit Number</th>
                                                    <th class="px-3 py-2 text-left font-semibold">Metal</th>
                                                    <th class="px-3 py-2 text-left font-semibold">Type</th>
                                                    <th class="px-3 py-2 text-right font-semibold">Quantity (kg)</th>
                                                    <th class="px-3 py-2 text-left font-semibold">Date</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200">
                                                @foreach($selectedAccount->deposits->take(5) as $deposit)
                                                    <tr class="hover:bg-slate-50">
                                                        <td class="px-3 py-2 font-mono text-slate-900">{{ $deposit->deposit_number }}</td>
                                                        <td class="px-3 py-2 text-slate-900">{{ $deposit->metal->name ?? 'N/A' }}</td>
                                                        <td class="px-3 py-2">
                                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $deposit->storage_type === 'allocated' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                                                {{ ucfirst($deposit->storage_type) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-2 text-right font-mono">{{ number_format($deposit->quantity_kg, 2) }}</td>
                                                        <td class="px-3 py-2 text-slate-600">{{ $deposit->created_at->format('M d, Y') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:justify-end">
                        <button type="button" wire:click="closeViewModal" class="w-full inline-flex justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 sm:w-auto">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
