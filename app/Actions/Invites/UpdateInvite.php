<?php

namespace App\Actions\Invites;

use App\Actions\DiscordPing;
use App\Models\Invite;
use App\Models\UserGroupPreference;
use Illuminate\Support\Facades\DB;

final class UpdateInvite
{
    public function handle(int $inviteId, array $attributes): void
    {
        DB::transaction(function() use ($inviteId, $attributes) {
            $invite = Invite::query()
                ->with('group', 'user')
                ->findOrFail($inviteId);
                
            $invite->update([
                'status_id' => $attributes['status'],
                'responded_at' => now()
            ]);

            $invite->group->userPreferences()->updateOrCreate([
                'user_id' => $invite->user_id
            ]);

            (new DiscordPing)->handle($invite->group, $invite->user->name . ' has joined the group: ' . $invite->group->name . '.');
        });
    }
}