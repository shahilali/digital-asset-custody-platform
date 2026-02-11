<div class="min-h-screen bg-slate-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Account Details</h1>
                    <p class="text-sm text-slate-500 mt-1">{{ $account->account_number }}</p>
                </div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-md shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Account Information -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Account Information</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
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
        </div>

        <!-- Transaction Summary -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Transaction Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-xs font-medium text-blue-700 uppercase">Total Deposits</p>
                    <p class="text-2xl font-bold text-blue-900 mt-1">{{ $account->deposits->count() }}</p>
                </div>
                <div class="p-4 bg-orange-50 rounded-lg border border-orange-200">
                    <p class="text-xs font-medium text-orange-700 uppercase">Total Withdrawals</p>
                    <p class="text-2xl font-bold text-orange-900 mt-1">{{ $account->withdrawals->count() }}</p>
                </div>
                @if($account->account_type === 'institutional')
                    <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                        <p class="text-xs font-medium text-purple-700 uppercase">Allocated Bars</p>
                        <p class="text-2xl font-bold text-purple-900 mt-1">{{ $account->allocatedBars->count() }}</p>
                    </div>
                @else
                    <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                        <p class="text-xs font-medium text-green-700 uppercase">Balance</p>
                        <p class="text-2xl font-bold text-green-900 mt-1">
                            @php
                                $balanceService = app(\App\Services\BalanceService::class);
                                $totalBalance = 0;
                                foreach(\App\Models\Metal::all() as $metal) {
                                    $totalBalance += $balanceService->unallocatedBalanceKg($account->id, $metal->id);
                                }
                            @endphp
                            {{ number_format($totalBalance, 3) }} kg
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Metal Balances -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Metal Balances</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Metal</th>
                            @if($account->account_type === 'institutional')
                                <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Bars</th>
                            @endif
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Balance (kg)</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Value (USD)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @php
                            $balanceService = app(\App\Services\BalanceService::class);
                        @endphp
                        @foreach(\App\Models\Metal::all() as $metal)
                            @php
                                if ($account->account_type === 'retail') {
                                    $balance = $balanceService->unallocatedBalanceKg($account->id, $metal->id);
                                    $value = $balanceService->unallocatedValue($account->id, $metal->id);
                                    $barCount = 0;
                                } else {
                                    $balance = $balanceService->allocatedBalanceKg($account->id, $metal->id);
                                    $barCount = $balanceService->allocatedBars($account->id, $metal->id)->count();
                                    $latestPrice = \App\Models\MetalPrice::where('metal_id', $metal->id)->latest()->value('price_per_kg') ?? 0;
                                    $value = $balance * $latestPrice;
                                }
                            @endphp
                            @if($balance > 0)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-sm text-slate-900">{{ $metal->name }} ({{ $metal->symbol }})</td>
                                    @if($account->account_type === 'institutional')
                                        <td class="px-4 py-3 text-sm text-right font-mono text-slate-900">{{ $barCount }}</td>
                                    @endif
                                    <td class="px-4 py-3 text-sm text-right font-mono text-slate-900">{{ number_format($balance, 3) }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-green-700">${{ number_format($value, 2) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Deposits -->
        @if($account->deposits->isNotEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Recent Deposits</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Deposit Number</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Metal</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Quantity (kg)</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach($account->deposits->take(10) as $deposit)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-sm font-mono text-slate-900">{{ $deposit->deposit_number }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-900">{{ $deposit->metal->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $deposit->storage_type === 'allocated' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ ucfirst($deposit->storage_type) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-mono">{{ number_format($deposit->quantity_kg, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $deposit->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Recent Withdrawals -->
        @if($account->withdrawals->isNotEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Recent Withdrawals</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Withdrawal Number</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Metal</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Quantity (kg)</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach($account->withdrawals->take(10) as $withdrawal)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-sm font-mono text-slate-900">{{ $withdrawal->withdrawal_number }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-900">{{ $withdrawal->metal->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-red-700">{{ number_format($withdrawal->quantity_kg, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $withdrawal->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
