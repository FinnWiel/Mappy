<?php

namespace App\Livewire\Maps;

use App\Models\Map;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Editor extends Component
{
    public Map $map;

    public function mount(Map $map): void
    {
        Gate::authorize('view', $map);
        $this->map = $map;
    }

    public function render()
    {
        return view('livewire.maps.editor')->layout('layouts.editor');
    }
}
