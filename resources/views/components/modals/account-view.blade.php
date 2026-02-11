@props(['show' => false, 'account' => null])

@if($show && $account)
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
                            <p class="text-sm text-slate-500 mt-1">{{ $account->account_number }}</p>
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
                                <p class="text-sm font-semibold text-slate-900 mt-1">{{ $account->customer_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-500 uppercase">Account Type</p>
                                <p class="mt-1">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $account->account_type === 'institutional' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700' }}">
                                        {{ ucfirst($account->account_type) }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-500 uppercase">Created Date</p>
                                <p class="text-sm font-semibold text-slate-900 mt-1">{{ $account->created_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-slate-500 uppercase">Last Updated</p>
                                <p class="text-sm font-semibold text-slate-900 mt-1">{{ $account->updated_at->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <!-- Transaction Summary -->
                        <div>
                            <h4 class="text-sm font-semibold text-slate-900 mb-3">Transaction Summary</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                    <p class="text-xs font-medium text-blue-700 uppercase">Total Deposits</p>
                                    <p class="text-2xl font-bold text-blue-900 mt-1">{{ $account->deposits->count() }}</p>
                                </div>
                                <div class="p-4 bg-orange-50 rounded-lg border border-orange-200">
                                    <p class="text-xs font-medium text-orange-700 uppercase">Total Withdrawals</p>
                                    <p class="text-2xl font-bold text-orange-900 mt-1">{{ $account->withdrawals->count() }}</p>
                                </div>
                                <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                                    <p class="text-xs font-medium text-purple-700 uppercase">Allocated Bars</p>
                                    <p class="text-2xl font-bold text-purple-900 mt-1">{{ $account->allocatedBars->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Deposits -->
                        @if($account->deposits->isNotEmpty())
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
                                            @foreach($account->deposits->take(5) as $deposit)
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
