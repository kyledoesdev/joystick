<?php

namespace Database\Factories;

use App\Models\Feed;
use App\Models\Game;
use App\Models\Suggestion;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SuggestionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'feed_id' => Feed::factory(),
            'game_id' => Game::factory(),
            'user_id' => User::factory(),
            'game_mode' => $this->faker->randomElement(['Competitive', 'Casual', 'Custom', 'Ranked']),
        ];
    }

    public function forFeed(Feed $feed): self
    {
        return $this->state([
            'feed_id' => $feed->getKey(),
        ]);
    }

    public function forUser(User $user): self
    {
        return $this->state([
            'user_id' => $user->getKey(),
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function (Suggestion $suggestion) {
            $suggestion->votes()->create([
                'user_id' => $suggestion->user_id,
                'group_id' => $suggestion->feed->group->getKey(),
                'vote' => Vote::UP_VOTE
            ]);
        });
    }
}
