<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Price History - {{ $metal->name }}</h1>
            <p class="text-sm text-slate-500 mt-1">Historical price data for {{ $metal->symbol }}</p>
        </div>
        <a href="{{ route('metals') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-md shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Metals
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-xs text-slate-500 mb-1">Latest Price</div>
            @if($stats['latest'])
                <div class="font-mono font-semibold text-xl text-slate-900">${{ number_format($stats['latest']->price_per_kg, 2) }}</div>
                <div class="text-xs text-slate-500 mt-1">{{ $stats['latest']->created_at->diffForHumans() }}</div>
            @else
                <div class="text-slate-400 text-sm">No data</div>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-xs text-slate-500 mb-1">Highest Price</div>
            @if($stats['highest'])
                <div class="font-mono font-semibold text-xl text-green-700">${{ number_format($stats['highest'], 2) }}</div>
            @else
                <div class="text-slate-400 text-sm">No data</div>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-xs text-slate-500 mb-1">Lowest Price</div>
            @if($stats['lowest'])
                <div class="font-mono font-semibold text-xl text-red-700">${{ number_format($stats['lowest'], 2) }}</div>
            @else
                <div class="text-slate-400 text-sm">No data</div>
            @endif
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="text-xs text-slate-500 mb-1">Total Records</div>
            <div class="font-semibold text-xl text-slate-900">{{ number_format($stats['count']) }}</div>
        </div>
    </div>

    <!-- Price History Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6">
            @if($priceHistory->isEmpty())
                <div class="text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <p class="text-slate-500 text-lg">No price history available</p>
                    <p class="text-slate-400 text-sm mt-1">Price records will appear here once added</p>
                </div>
            @else
                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">Date & Time</th>
                                <th class="px-4 py-3 text-right font-semibold">Price per KG</th>
                                <th class="px-4 py-3 text-right font-semibold">Change</th>
                                <th class="px-4 py-3 text-right font-semibold">Time Ago</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($priceHistory as $index => $price)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 text-slate-900">
                                        <div class="font-medium">{{ $price->created_at->format('M d, Y') }}</div>
                                        <div class="text-xs text-slate-500">{{ $price->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="font-mono text-slate-900 font-medium text-base">${{ number_format($price->price_per_kg, 2) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        @php
                                            // Get the previous price from the full collection (not just current page)
                                            $previousPrice = \App\Models\MetalPrice::where('metal_id', $metalId)
                                                ->where('created_at', '<', $price->created_at)
                                                ->orderBy('created_at', 'desc')
                                                ->first();
                                        @endphp

                                        @if($previousPrice)
                                            @php
                                                $change = $price->price_per_kg - $previousPrice->price_per_kg;
                                                $changePercent = $previousPrice->price_per_kg > 0 ? (($change / $previousPrice->price_per_kg) * 100) : 0;
                                            @endphp

                                            @if($change > 0)
                                                <div class="inline-flex items-center text-green-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                    </svg>
                                                    <span class="font-medium">+{{ number_format($changePercent, 2) }}%</span>
                                                </div>
                                            @elseif($change < 0)
                                                <div class="inline-flex items-center text-red-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                    </svg>
                                                    <span class="font-medium">{{ number_format($changePercent, 2) }}%</span>
                                                </div>
                                            @else
                                                <span class="text-slate-500 text-sm">No change</span>
                                            @endif
                                        @else
                                            <span class="text-slate-400 text-sm">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right text-slate-500">
                                        {{ $price->created_at->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $priceHistory->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
