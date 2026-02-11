<div class="space-y-6">
    @if (session()->has('message'))
        <div class="rounded-lg bg-green-50 border border-green-200 p-4">
            <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
        <div class="p-6 space-y-6">
            <div>
                <h1 class="text-lg font-semibold text-slate-900">Metals</h1>
                <p class="text-sm text-slate-500">Create and manage precious metals.</p>
            </div>

            <form wire:submit.prevent="save" class="space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Metal Name</label>
                        <input type="text" wire:model="name" placeholder="Metal Name" class="w-full rounded-md border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Symbol</label>
                        <input type="text" wire:model="symbol" placeholder="Symbol (e.g., XAU)" class="w-full rounded-md border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:border-blue-500">
                    </div>
                </div>
                <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                    {{ $editId ? 'Update' : 'Create' }}
                </button>
            </form>

            <div class="overflow-x-auto rounded-lg border border-slate-200">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold">ID</th>
                            <th class="px-3 py-2 text-left font-semibold">Name</th>
                            <th class="px-3 py-2 text-left font-semibold">Symbol</th>
                            <th class="px-3 py-2 text-right font-semibold">Current Price</th>
                            <th class="px-3 py-2 text-right font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($metals as $metal)
                            <tr class="hover:bg-slate-50">
                                <td class="px-3 py-2 text-slate-500">{{ $metal->id }}</td>
                                <td class="px-3 py-2 font-medium text-slate-900">{{ $metal->name }}</td>
                                <td class="px-3 py-2">{{ $metal->symbol }}</td>
                                <td class="px-3 py-2 text-right">
                                    @if($metal->metalPrices->isNotEmpty())
                                        <span class="font-mono text-slate-900">${{ number_format($metal->metalPrices->first()->price_per_kg, 2) }}</span>
                                        <span class="text-xs text-slate-500 ml-1">/kg</span>
                                    @else
                                        <span class="text-slate-400 text-xs">No price set</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-right space-x-2">
                                    <button wire:click="edit({{ $metal->id }})" class="text-slate-700 hover:text-slate-900" title="Edit Metal">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="openPriceModal({{ $metal->id }})" class="text-blue-700 hover:text-blue-900" title="Set Price">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Price Modal -->
    @if($showPriceModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="closePriceModal"></div>

                <!-- Modal panel -->
                <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-50">
                    <form wire:submit.prevent="savePrice">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg font-semibold leading-6 text-slate-900 mb-4" id="modal-title">
                                        Set Price for {{ $selectedMetalName }}
                                    </h3>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Price per KG</label>
                                            <input type="number" step="0.01" wire:model="price_per_kg" placeholder="0.00" class="w-full rounded-md border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:border-blue-500">
                                            @error('price_per_kg') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 sm:w-auto">
                                Save Price
                            </button>
                            <button type="button" wire:click="closePriceModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 sm:mt-0 sm:w-auto">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
