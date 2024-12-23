<?php

namespace App\Livewire\Actions\Group;

use App\Models\Group;
use App\Models\InviteStatus;
use App\Models\User;
use App\Notifications\GroupInvitationNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class UpdateInvitedUsers
{
    public function __construct(private Group $group, private Collection $invitedUsers, private Collection $groupInvites){}

    public function handle(): void
    {
        $this->invitedUsers->each(function ($userId) {
            $userInvite = $this->groupInvites->firstWhere('user_id', $userId);

            if (is_null($userInvite)) {
                $this->group->invites()->create([
                    'user_id' => $userId,
                    'status_id' => InviteStatus::PENDING,
                    'invited_at' => now(),
                ]);

                Notification::send(User::find($userId), new GroupInvitationNotification($this->group));
            } elseif ($userInvite->status_id === InviteStatus::OWNER_REMOVED) {
                $userInvite->update([
                    'status_id' => InviteStatus::PENDING,
                ]);
            }
        });
    }
}