<?php

namespace App\Livewire\Forms;

use App\Models\Invite;
use App\Models\InviteStatus;
use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Form;

class InviteForm extends Form
{
    public function update($inviteId, $status)
    {
        $invite = Invite::where('user_id', auth()->id())->findOrFail($inviteId);

        if (! in_array($status, InviteStatus::getStatuses())) {
            Flux::toast(variant: 'warning', text: "Not a valid action.", duration: 2000);
        }

        $invite->update(['status_id' => $status, 'responded_at' => now()]);
    }
}
