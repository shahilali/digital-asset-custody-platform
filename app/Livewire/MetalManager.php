<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Metal;
use App\Models\MetalPrice;

class MetalManager extends Component
{
    public $metals;
    public $name;
    public $symbol;
    public $editId = null;

    public $showPriceModal = false;
    public $selectedMetalId = null;
    public $selectedMetalName = '';
    public $price_per_kg;

    protected $rules = [
        'name' => 'required|string|max:255',
        'symbol' => 'required|string|max:10',
    ];

    protected $priceRules = [
        'price_per_kg' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->loadMetals();
    }

    public function loadMetals()
    {
        $this->metals = Metal::with(['metalPrices' => function($query) {
            $query->latest('created_at')->limit(1);
        }])->get();
    }

    public function save()
    {
        $this->validate();

        if ($this->editId) {
            $metal = Metal::find($this->editId);
            $metal->update([
                'name' => $this->name,
                'symbol' => $this->symbol,
            ]);
        } else {
            Metal::create([
                'name' => $this->name,
                'symbol' => $this->symbol,
            ]);
        }

        $this->reset(['name','symbol','editId']);
        $this->loadMetals();
    }

    public function edit($id)
    {
        $metal = Metal::findOrFail($id);
        $this->editId = $id;
        $this->name = $metal->name;
        $this->symbol = $metal->symbol;
    }

    public function openPriceModal($metalId)
    {
        $metal = Metal::findOrFail($metalId);
        $this->selectedMetalId = $metalId;
        $this->selectedMetalName = $metal->name;
        $this->price_per_kg = '';
        $this->showPriceModal = true;
    }

    public function closePriceModal()
    {
        $this->showPriceModal = false;
        $this->reset(['selectedMetalId', 'selectedMetalName', 'price_per_kg']);
    }

    public function savePrice()
    {
        $this->validate($this->priceRules);

        MetalPrice::create([
            'metal_id' => $this->selectedMetalId,
            'price_per_kg' => $this->price_per_kg,
        ]);

        session()->flash('message', 'Metal price updated successfully.');
        $this->closePriceModal();
        $this->loadMetals();
    }

    public function render()
    {
        return view('livewire.metal-manager');
    }
}
