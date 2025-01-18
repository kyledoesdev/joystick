<?php

namespace App\Actions\Feeds;

use App\Actions\DiscordPing;
use App\Models\Feed;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class DestroyFeed
{
    public function handle(User $user, Feed $feed)
    {
        DB::transaction(function() use ($user, $feed) {
            $feed->delete();

            (new DiscordPing)->handle($feed->group, "{$user->name} deleted feed: {$feed->name}.", 'error');
        });
    }
}