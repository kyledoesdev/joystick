<?php

namespace App\Livewire;

use App\Livewire\Forms\GroupForm;
use App\Models\Group;
use App\Models\InviteStatus;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    public GroupForm $form;

    public function render()
    {
        return view('livewire.dashboard');
    }

    #[Computed]
    #[On('user-preferences-updated')]
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
            ->with('userPreferences', fn($q) => $q->where('user_id', auth()->id()))
            ->withCount(['invites' => function($q) {
                $q->where('status_id', InviteStatus::ACCEPTED);
            }])
            ->withCount(['feeds'])
            ->get();
    }

    public function store()
    {
        $this->form->store();
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
