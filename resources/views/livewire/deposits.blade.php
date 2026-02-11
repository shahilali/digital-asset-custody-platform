<div class="min-h-screen bg-slate-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">All Deposits</h1>
            <p class="text-sm text-slate-500 mt-1">View and search all deposit transactions</p>
        </div>

         <!-- Summary Stats -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <p class="text-xs font-medium text-slate-500 uppercase">Total Deposits</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $deposits->total() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <p class="text-xs font-medium text-slate-500 uppercase">Allocated Deposits</p>
                <p class="text-2xl font-bold text-blue-900 mt-1">
                    {{ $deposits->where('storage_type', 'allocated')->count() }}
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
                <p class="text-xs font-medium text-slate-500 uppercase">Unallocated Deposits</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">
                    {{ $deposits->where('storage_type', 'unallocated')->count() }}
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

        <!-- Deposits Table -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Deposit Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Account</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Customer Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Metal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Storage Type</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Quantity (kg)</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Bars</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($deposits as $deposit)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm font-mono text-slate-900">{{ $deposit->deposit_number }}</td>
                                <td class="px-4 py-3 text-sm font-mono text-slate-600">
                                    <a href="{{ route('accounts.view', $deposit->account_id) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ $deposit->account->account_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-900">{{ $deposit->account->customer_name }}</td>
                                <td class="px-4 py-3 text-sm text-slate-900">{{ $deposit->metal->name ?? 'N/A' }} ({{ $deposit->metal->symbol ?? '' }})</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $deposit->storage_type === 'allocated' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($deposit->storage_type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-mono text-slate-900">{{ number_format($deposit->quantity_kg, 3) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-mono text-slate-600">
                                    @if($deposit->storage_type === 'allocated')
                                        {{ $deposit->allocatedBars->count() }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $deposit->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-slate-500">
                                    @if($search)
                                        No deposits found matching "{{ $search }}"
                                    @else
                                        No deposits found
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($deposits->hasPages())
                <div class="px-4 py-3 border-t border-slate-200">
                    {{ $deposits->links() }}
                </div>
            @endif
        </div>


    </div>
</div>
