<?php

namespace App\Actions\Invites;

use App\Models\Group;
use App\Models\InviteStatus;
use App\Models\User;
use App\Notifications\GroupInvitationNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

final class StoreInvite
{
    public function handle(User $user, Group $group): void
    {
        DB::transaction(function() use ($user, $group) {
            $invite = $group->invites()->updateOrCreate([
                'user_id' => $user->getKey(),
            ], [
                'status_id' => InviteStatus::PENDING,
                'invited_at' => now(),
            ]);

            if ($invite->wasRecentlyCreated) {
                Notification::send(User::find($userId), new GroupInvitationNotification($group));
            }
        });
    }
}