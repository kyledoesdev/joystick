<?php

namespace App\Actions\Groups;

use App\Models\Group;
use App\Models\GroupSetting;
use Illuminate\Support\Facades\DB;

final class UpdateGroup
{
    public function handle(Group $group, array $attributes)
    {
        DB::transaction(function() use ($group, $attributes) {
            /* update the group */
            $group->update([
                'name' => $attributes['name'],
                'owner_feeds_only' => $attributes['owner_feeds_only'],
                'discord_webhook_url' => $attributes['discord_webhook_url'],
                'discord_updates' => $attributes['discord_updates'],
            ]);

            /* update the group settings */
            $group->settings()->update(
                collect(GroupSetting::getDiscordPingSettings())
                    ->mapWithKeys(fn ($_, $key) => [
                        $key => in_array($key, $attributes['group_discord_alert_settongs'])
                    ])
                    ->toArray()
                );
        });
    }
}