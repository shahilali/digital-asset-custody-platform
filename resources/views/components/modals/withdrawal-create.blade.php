@props(['show' => false, 'metals' => [], 'account' => null, 'balanceKg' => 0, 'availableBars' => []])

@if($show)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" wire:click="closeWithdrawalModal"></div>

            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full z-50">
                <form wire:submit.prevent="saveWithdrawal">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-semibold leading-6 text-slate-900 mb-4" id="modal-title">
                                    Create New Withdrawal
                                </h3>
                                <div class="space-y-4">
                                    @if($account)
                                        <div class="rounded-lg {{ $account->account_type === 'institutional' ? 'bg-purple-50 border-purple-200' : 'bg-green-50 border-green-200' }} border p-3 mb-4">
                                            <p class="text-sm {{ $account->account_type === 'institutional' ? 'text-purple-800' : 'text-green-800' }}">
                                                <strong>{{ $account->customer_name }}</strong> ({{ $account->account_number }})
                                            </p>
                                            <p class="text-xs {{ $account->account_type === 'institutional' ? 'text-purple-600' : 'text-green-600' }} mt-1">
                                                {{ ucfirst($account->account_type) }} account
                                            </p>
                                        </div>
                                    @endif

                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Metal</label>
                                        <select wire:model.live="withdrawal_metal_id" class="w-full rounded-md border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:border-blue-500">
                                            <option value="">Select Metal</option>
                                            @foreach($metals as $metal)
                                                <option value="{{ $metal->id }}">{{ $metal->name }} ({{ $metal->symbol }})</option>
                                            @endforeach
                                        </select>
                                        @error('withdrawal_metal_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    @if($account && $account->account_type === 'retail')
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                                Available Balance: <span class="font-semibold text-green-600">{{ number_format($balanceKg, 3) }} kg</span>
                                            </label>
                                            <input type="number" step="0.001" wire:model="withdrawal_quantity_kg" placeholder="0.000" class="w-full rounded-md border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:border-blue-500">
                                            @error('withdrawal_quantity_kg') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                        </div>
                                    @endif

                                    @if($account && $account->account_type === 'institutional' && count($availableBars) > 0)
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                                Select Bars to Withdraw ({{ count($availableBars) }} available)
                                            </label>
                                            <div class="max-h-60 overflow-y-auto border border-slate-200 rounded-md p-3 space-y-2">
                                                @foreach($availableBars as $bar)
                                                    <label class="flex items-center p-2 hover:bg-slate-50 rounded cursor-pointer">
                                                        <input type="checkbox" wire:model.live="selectedBarIds" value="{{ $bar->id }}" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                        <span class="ml-3 text-sm text-slate-700">{{ $bar->serial_number }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            @error('selectedBarIds') <span class="text-xs text-red-600">{{ $message }}</span> @enderror

                                            @if(count($this->selectedBarIds) > 0)
                                                <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                                    <p class="text-sm text-blue-800">
                                                        <strong>Withdrawal Summary:</strong> {{ count($this->selectedBarIds) }} bar(s) selected = <strong>{{ count($this->selectedBarIds) }} kg</strong>
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($account && $account->account_type === 'institutional' && count($availableBars) === 0)
                                        <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-3">
                                            <p class="text-sm text-yellow-800">No allocated bars available for withdrawal.</p>
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
                        <button type="submit" class="w-full inline-flex justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 sm:w-auto">
                            Create Withdrawal
                        </button>
                        <button type="button" wire:click="closeWithdrawalModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 sm:mt-0 sm:w-auto">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
