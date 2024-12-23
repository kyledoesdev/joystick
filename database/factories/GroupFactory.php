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
            'name' => 'Royalty',
            'owner_feeds_only' => false,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Group $group) {
            Invite::factory()->create([
                'group_id' => $group->getKey(),
                'user_id' => $group->owner_id,
                'status_id' => InviteStatus::where('name', 'accepted')->first()->getKey(),
            ]);
            
            Feed::factory()->create([
                'group_id' => $group->getKey(),
                'user_id' => $group->owner_id,
                'name' => Str::possessive($group->name) . ' Backlog',
            ]);
        });
    }
}
