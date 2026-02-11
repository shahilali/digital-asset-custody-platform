<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Metal;

class MetalManager extends Component
{
    public $metals;
    public $name;
    public $symbol;
    public $editId = null;

    protected $rules = [
        'name' => 'required|string|max:255',
        'symbol' => 'required|string|max:10',
    ];

    public function mount()
    {
        $this->loadMetals();
    }

    public function loadMetals()
    {
        $this->metals = Metal::all();
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

    public function render()
    {
        return view('livewire.metal-manager');
    }
}
