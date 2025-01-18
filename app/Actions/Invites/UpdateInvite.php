<?php

namespace App\Actions\Invites;

use App\Actions\DiscordPing;
use App\Models\Invite;
use App\Models\InviteStatus;
use App\Models\UserGroupPreference;
use Illuminate\Support\Facades\DB;

final class UpdateInvite
{
    public function handle(int $inviteId, array $attributes): void
    {
        $invite = Invite::query()
            ->with('group', 'user')
            ->findOrFail($inviteId);

        DB::transaction(function() use ($invite, $attributes) {
            $invite->update([
                'status_id' => $attributes['status'],
                'responded_at' => $attributes['status'] == InviteStatus::ACCEPTED ? now() : $invite->responded_at,
            ]);

            $invite->group->userPreferences()->updateOrCreate([
                'user_id' => $invite->user_id
            ]);

            (new DiscordPing)->handle(
                $invite->group,
                $invite->user->name . ' has '. $this->getActionString($attributes['status']) . ' the group.',
                in_array($attributes['status'], [InviteStatus::OWNER_REMOVED, InviteStatus::USER_LEFT]) ? 'error' : 'info'
            );
        });

        /* delete feeds, suggestions & votes for the user if they were removed or left the group */
        DB::transaction(function() use ($invite, $attributes) {
            if (in_array($attributes['status'], [InviteStatus::OWNER_REMOVED, InviteStatus::USER_LEFT])) {
                $feeds = $invite->group->feeds()->where('user_id', $invite->user_id)->get();

                $feeds->each(function($feed) use ($invite) {
                    $feed->suggestions()
                        ->where('user_id', $invite->user_id)
                        ->each(function ($suggestion) use ($invite) {
                            $suggestion->votes()->where('user_id', $invite->user_id)->delete();
                            $suggestion->delete();
                        });
                    
                    $feed->delete();
                });
            }
        });
    }

    private function getActionString(int $status): string
    {
        $string = '';

        switch ($status) {
            case InviteStatus::ACCEPTED:
                $string = 'joined';
                break;
            case InviteStatus::USER_LEFT:
                $string = 'left';
                break;
            case InviteStatus::OWNER_REMOVED:
                $string = 'been removed from';
                break;
        }

        return $string;
    }
}