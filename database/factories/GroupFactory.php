<?php

namespace Database\Factories;

use App\Models\Feed;
use App\Models\Group;
use App\Models\Invite;
use App\Models\InviteStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GroupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'owner_id' => User::factory()->create(),
            'name' => fake()->catchPhrase(),
            'discord_webhook_url' => null,
            'discord_updates' => false,
            'owner_feeds_only' => false,
        ];
    }

    public function withOwner(User $owner): self
    {
        return $this->state([
            'owner_id' => $owner->getKey(),
        ]);
    }

    public function ownerFeedsOnly(bool $ownerFeedsOnly): self
    {
        return $this->state([
            'owner_feeds_only' => $ownerFeedsOnly,
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function (Group $group) {
            Invite::factory()
                ->withStatus(InviteStatus::ACCEPTED)
                ->create([
                    'group_id' => $group->getKey(),
                    'user_id' => $group->owner_id,
                ]);
            
            Feed::factory()->create([
                'group_id' => $group->getKey(),
                'user_id' => $group->owner_id,
                'name' => Str::possessive($group->name) . ' Backlog',
                'start_time' => now(),
            ]);

            $group->settings()->create();
        });
    }
}
