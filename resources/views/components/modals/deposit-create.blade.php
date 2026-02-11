@props(['show' => false, 'metals' => [], 'storageType' => 'unallocated', 'existingStorageType' => null, 'account' => null])

@if($show)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="closeDepositModal"></div>

            <!-- Modal panel -->
            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full z-50">
                <form wire:submit.prevent="saveDeposit">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-semibold leading-6 text-slate-900 mb-4" id="modal-title">
                                    Create New Deposit
                                </h3>
                                <div class="space-y-4">
                                    @if($account)
                                        <div class="rounded-lg {{ $account->account_type === 'institutional' ? 'bg-purple-50 border-purple-200' : 'bg-green-50 border-green-200' }} border p-3 mb-4">
                                            <p class="text-sm {{ $account->account_type === 'institutional' ? 'text-purple-800' : 'text-green-800' }}">
                                                <strong>{{ ucfirst($account->account_type) }}</strong> account - Storage type is automatically set to <strong>{{ $storageType }}</strong>
                                            </p>
                                        </div>
                                    @endif

                                    <div x-data x-init="$wire.storage_type = '{{ $storageType }}'">
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Storage Type</label>
                                        <input type="text" value="{{ ucfirst($storageType) }}" readonly class="w-full rounded-md border-slate-300 bg-slate-100 px-3 py-2 text-sm shadow-sm text-slate-600 cursor-not-allowed">
                                        @error('storage_type') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Metal</label>
                                        <select wire:model.live="metal_id" class="w-full rounded-md border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:border-blue-500">
                                            <option value="">Select Metal</option>
                                            @foreach($metals as $metal)
                                                <option value="{{ $metal->id }}">{{ $metal->name }} ({{ $metal->symbol }})</option>
                                            @endforeach
                                        </select>
                                        @error('metal_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Quantity (kg)</label>
                                        <input type="number" step="0.001" wire:model="quantity_kg" placeholder="0.000" class="w-full rounded-md border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:border-blue-500">
                                        @error('quantity_kg') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    @if($storageType === 'allocated')
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Serial Numbers (one per line)</label>
                                            <textarea wire:model="serial_numbers" rows="4" placeholder="Enter serial numbers, one per line" class="w-full rounded-md border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:border-blue-500"></textarea>
                                            <p class="text-xs text-slate-500 mt-1">Required for allocated storage</p>
                                            @error('serial_numbers.*') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                        </div>
                                    @endif

                                    @if(session()->has('error'))
                                        <div class="rounded-lg bg-red-50 border border-red-200 p-3">
                                            <p class="text-sm text-red-800">{{ session('error') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 sm:w-auto">
                            Create Deposit
                        </button>
                        <button type="button" wire:click="closeDepositModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 sm:mt-0 sm:w-auto">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
