<?php

namespace App\Livewire\Actions\Groups;

use App\Models\Group;
use App\Models\InviteStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class StoreGroup
{
    public function handle(User $user, array $attributes): void
    {
        DB::transaction(function() use ($user, $attributes) {
            $group = Group::create([
                'name' => $attributes['name'],
                'owner_id' => $user->getKey(),
                'discord_webhook_url' => $attributes['discord_webhook_url'],
                'discord_updates' => $attributes['discord_updates'],
                'owner_feeds_only' => $attributes['owner_feeds_only']
            ]);
    
            /* Auto create a game backlog feed */
            $group->feeds()->create([
                'user_id' => $user->getKey(),
                'name' => Str::possessive($group->name) . ' Game Backlog'
            ]);
    
            /* auto create the accepted invite for the group owner */
            $group->invites()->create([
                'user_id' => $user->getKey(),
                'invited_at' => now(),
                'responded_at' => now(),
                'status_id' => InviteStatus::ACCEPTED 
            ]);
        });
    }
}