<?php

namespace App\Livewire\Actions\Group;

use App\Models\Group;
use App\Models\InviteStatus;
use App\Models\User;
use App\Notifications\GroupInvitationNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class UnInviteUsers
{
    public function __construct(private Group $group, private Collection $invitedUsers, private Collection $groupInvites){}

    public function handle(): void
    {
        $this->groupInvites->each(function ($invite) {
            if (! $this->invitedUsers->contains($invite->user_id)) {
                $invite->update(['status_id' => InviteStatus::OWNER_REMOVED]);
            }
        });
    }
}