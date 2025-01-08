<?php

namespace App\Livewire\Actions\Suggestions;

use App\Models\Game;
use App\Models\Suggestion;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Support\Facades\DB;

final class StoreSuggestion
{
    public function handle(User $user, array $attributes): void
    {
        DB::transaction(function() use ($user, $attributes) {
            $game = Game::updateOrCreate([
                'game_id' => $attributes['game']['id']
            ], [
                'name' => $attributes['game']['name'],
                'cover' => $attributes['game']['box_art_url'],
            ]);

            $suggestion = Suggestion::create([
                'feed_id' => $attributes['feed']->getKey(),
                'game_id' => $game->getKey(),
                'user_id' => $user->getKey(),
                'game_mode' => $attributes['game_mode'],
            ]);

            /* Auto create an Up vote for the user who suggested the game */
            $suggestion->votes()->create([
                'user_id' => $user->getKey(),
                'group_id' => $attributes['feed']->group_id,
                'vote' => Vote::UP_VOTE
            ]);
        });
    }
}