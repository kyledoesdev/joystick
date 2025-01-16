<?php

namespace App\Livewire\Forms;

use App\Actions\Invites\StoreInvite;
use App\Actions\Invites\UpdateInvite;
use App\Models\Invite;
use App\Models\InviteStatus;
use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Form;

class InviteForm extends Form
{
    public array $invited_users = [];

    public function store($group, $userId)
    {
        (new StoreInvite)->handle(User::find($userId), $group);
    }

    public function edit($group)
    {
        $this->invited_users = $group->invites
            ->whereNotIn('status_id', [InviteStatus::OWNER_REMOVED, InviteStatus::USER_LEFT])
            ->pluck('user_id')
            ->toArray();
    }

    public function update($invite, $status)
    {
        if (! in_array($status, InviteStatus::getStatuses())) {
            Flux::toast(variant: 'warning', text: "Not a valid action.", duration: 2000);
        }

        (new UpdateInvite)->handle($invite, [
            'status' => $status
        ]);
    }

    public function destroy($group, $userId)
    {
        Invite::query()
            ->where('group_id', $group->getKey())
            ->where('user_id', $userId)
            ->first()
            ->update(['status_id' => InviteStatus::OWNER_REMOVED]);
    }
}
