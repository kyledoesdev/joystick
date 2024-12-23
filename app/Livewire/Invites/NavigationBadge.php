<?php

namespace App\Livewire\Invites;

use App\Livewire\Forms\InviteForm;
use App\Livewire\Traits\TableHelpers;
use App\Models\Invite;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class NavigationBadge extends Component
{
    public function render()
    {
        return view('livewire.invites.navigation-badge');
    }

    #[Computed]
    #[On('invitation-updated')]
    public function invitations()
    {
        return Invite::getPending();
    }
}
