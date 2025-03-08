<?php

namespace App\Actions\Feeds;

use App\Actions\DiscordPing;
use App\Actions\Suggestions\DestroySuggestion;
use App\Models\Feed;
use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class DestroyFeed
{
    public function handle(User $user, Feed $feed)
    {
        DB::transaction(function() use ($user, $feed) {
            /* first delete each suggestion in the feed*/
            $feed->suggestions->each(function(Suggestion $suggestion) {
                (new DestroySuggestion)->handle($suggestion); 
            });

            $feed->delete();

            if ($feed->group->discord_updates && $feed->group->settings->d_destroy_feed_alerts) {
                (new DiscordPing)->handle($feed->group, "{$user->name} deleted feed: {$feed->name}.", 'error');
            }
        });
    }
}