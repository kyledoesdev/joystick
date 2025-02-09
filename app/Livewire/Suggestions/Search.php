<?php

namespace App\Livewire\Suggestions;

use Livewire\Component;

class Search extends Component
{
    public string $search = '';

    public function render()
    {
        return view('livewire.feed.search');
    }

    public function updatedSearch($value)
    {
        $this->dispatch('search-updated', $value);
    }
}