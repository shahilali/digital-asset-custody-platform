<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Metal;
use App\Models\MetalPrice;

class MetalPriceHistory extends Component
{
    use WithPagination;

    public $metalId;
    public $metal;

    public function mount($id)
    {
        $this->metalId = $id;
        $this->metal = Metal::findOrFail($id);
    }

    public function render()
    {
        $priceHistory = MetalPrice::where('metal_id', $this->metalId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'latest' => MetalPrice::where('metal_id', $this->metalId)->latest()->first(),
            'highest' => MetalPrice::where('metal_id', $this->metalId)->max('price_per_kg'),
            'lowest' => MetalPrice::where('metal_id', $this->metalId)->min('price_per_kg'),
            'count' => MetalPrice::where('metal_id', $this->metalId)->count(),
        ];

        return view('livewire.metal-price-history', [
            'priceHistory' => $priceHistory,
            'stats' => $stats,
        ]);
    }
}
