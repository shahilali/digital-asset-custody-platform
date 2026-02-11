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
                        <th class="px-3 py-2 text-right font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($metals as $metal)
                        <tr class="hover:bg-slate-50">
                            <td class="px-3 py-2 text-slate-500">{{ $metal->id }}</td>
                            <td class="px-3 py-2 font-medium text-slate-900">{{ $metal->name }}</td>
                            <td class="px-3 py-2">{{ $metal->symbol }}</td>
                            <td class="px-3 py-2 text-right space-x-2">
                                <button wire:click="edit({{ $metal->id }})" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-2 py-1 text-xs font-medium text-slate-700 shadow-sm hover:bg-slate-50">Edit</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
