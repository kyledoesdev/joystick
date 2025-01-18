<?php

namespace App\Livewire;

use App\Livewire\Forms\GroupForm;
use App\Livewire\Forms\InviteForm;
use App\Models\Group;
use App\Models\InviteStatus;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Dashboard extends Component
{
    public GroupForm $groupForm;
    public InviteForm $inviteForm;

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
            ->with('userPreferences', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->withCount(['invites' => function($query) {
                $query->where('status_id', InviteStatus::ACCEPTED);
            }])
            ->withCount(['feeds'])
            ->get();
    }

    public function store()
    {
        $this->groupForm->store();
    }

    public function confirmLeaveGroup($groupId)
    {
        $this->inviteForm->confirm($groupId);
    }

    public function leaveGroup($inviteId)
    {
        $this->inviteForm->update($inviteId, InviteStatus::USER_LEFT);

        Flux::modal('update-group-invite')->close();
    }
}
