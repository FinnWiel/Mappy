<?php

namespace App\Livewire\Maps;

use App\Models\Map;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public string $title = '';

    public function createMap(): void
    {
        $this->validate(['title' => ['required', 'string', 'max:100']]);
        $map = Map::create(['owner_id' => Auth::id(), 'title' => trim($this->title)]);
        $map->assignRoleTo(Auth::user(), Map::OWNER);
        $this->redirect(route('maps.show', $map), navigate: true);
    }

    public function render()
    {
        $user = Auth::user();
        $maps = Map::query()
            ->whereHas('owner', fn ($query) => $query->whereKey($user->id))
            ->orWhereHas('invitations', fn ($query) => $query->where('email', $user->email)->whereNotNull('accepted_at'))
            ->latest('updated_at')
            ->get()
            ->filter(fn (Map $map) => $map->roleFor($user) !== null);

        return view('livewire.maps.index', compact('maps'))->layout('layouts.app');
    }
}
