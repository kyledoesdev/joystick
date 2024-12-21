<?php

namespace App\Livewire\Feed;

use App\Models\Group;
use App\Models\Suggestion;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class VotersTable extends Component
{
    use WithPagination;

    public Suggestion $suggestion;

    public function render()
    {
        return view('livewire.feed.voters-table');
    }

    #[Computed]
    public function votes()
    {
        return $this->suggestion->votes()->with('user')->paginate(10);
    }
}
