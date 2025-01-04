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
        $invite = Invite::query()
            ->where('user_id', auth()->id())
            ->with('group')
            ->findOrFail($inviteId);

        if (! in_array($status, InviteStatus::getStatuses())) {
            Flux::toast(variant: 'warning', text: "Not a valid action.", duration: 2000);
        }

        $invite->update(['status_id' => $status, 'responded_at' => now()]);
        $invite->group->writeToDiscord(auth()->user()->name . ' has joined the group: ' . $invite->group->name . '.');
    }
}
