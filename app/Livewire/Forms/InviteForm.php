<?php

namespace App\Livewire\Forms;

use App\Models\Invite;
use App\Models\InviteStatus;
use App\Models\User;
use App\Notifications\GroupInvitationNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Validate;
use Livewire\Form;

class InviteForm extends Form
{
    public array $invited_users = [];

    public function store($group, $userId)
    {
        $invite = $group->invites()->updateOrCreate([
            'user_id' => $userId,
        ], [
            'status_id' => InviteStatus::PENDING,
            'invited_at' => now(),
        ]);

        if ($invite->wasRecentlyCreated) {
            Notification::send(User::find($userId), new GroupInvitationNotification($group));
        }
    }

    public function edit($group)
    {
        $this->invited_users = $group->invites
            ->whereNotIn('status_id', [InviteStatus::OWNER_REMOVED, InviteStatus::USER_LEFT])
            ->pluck('user_id')
            ->toArray();
    }

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

    public function destroy($group, $userId)
    {
        Invite::query()
            ->where('group_id', $group->getKey())
            ->where('user_id', $userId)
            ->first()
            ->update(['status_id' => InviteStatus::OWNER_REMOVED]);
    }
}
