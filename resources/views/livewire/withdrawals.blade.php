<div class="min-h-screen bg-slate-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">All Withdrawals</h1>
            <p class="text-sm text-slate-500 mt-1">View and search all withdrawal transactions</p>
        </div>

         <!-- Summary Stats -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <p class="text-xs font-medium text-slate-500 uppercase">Total Withdrawals</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $withdrawals->total() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <p class="text-xs font-medium text-slate-500 uppercase">Total Quantity</p>
                <p class="text-2xl font-bold text-red-900 mt-1">
                    {{ number_format($withdrawals->sum('quantity_kg'), 3) }} kg
                </p>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4 mb-6 mt-6">
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by customer name or account number..."
                    class="w-full rounded-md border-slate-300 bg-white px-4 py-2 pl-10 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:border-blue-500"
                >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-2.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <!-- Withdrawals Table -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Withdrawal Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Account</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Customer Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Metal</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Quantity (kg)</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($withdrawals as $withdrawal)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm font-mono text-slate-900">{{ $withdrawal->withdrawal_number }}</td>
                                <td class="px-4 py-3 text-sm font-mono text-slate-600">
                                    <a href="{{ route('accounts.view', $withdrawal->account_id) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ $withdrawal->account->account_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-900">{{ $withdrawal->account->customer_name }}</td>
                                <td class="px-4 py-3 text-sm text-slate-900">{{ $withdrawal->metal->name ?? 'N/A' }} ({{ $withdrawal->metal->symbol ?? '' }})</td>
                                <td class="px-4 py-3 text-sm text-right font-mono text-red-700">{{ number_format($withdrawal->quantity_kg, 3) }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $withdrawal->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                    @if($search)
                                        No withdrawals found matching "{{ $search }}"
                                    @else
                                        No withdrawals found
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($withdrawals->hasPages())
                <div class="px-4 py-3 border-t border-slate-200">
                    {{ $withdrawals->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
