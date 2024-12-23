<?php

namespace App\Livewire;

use App\Livewire\Forms\GroupForm;
use App\Models\Group;
use App\Models\InviteStatus;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    public GroupForm $form;

    public function render()
    {
        return view('livewire.dashboard');
    }

    #[Computed]
    public function groups()
    {
        /* todo fix & eager load votes hasManyDeep */
        return Group::query()
            ->whereHas('invites', function($query) {
                $query->newQuery()
                    ->where('status_id', InviteStatus::ACCEPTED)
                    ->where('user_id', auth()->id());
            })
            ->with('feeds.suggestions.votes')
            ->withCount(['invites', 'feeds'])
            ->get();
    }

    public function confirm($groupId)
    {
        $this->form->confirm($groupId);
    }

    public function destroy()
    {
        $this->form->destroy();
    }
}
