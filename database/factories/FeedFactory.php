<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeedFactory extends Factory
{
    public function definition()
    {
        return [
            'group_id' => Group::factory(),
            'user_id' => User::factory(),
            'name' => fake()->catchPhrase(),
        ];
    }

    public function forOwner(User $user)
    {
        return $this->state([
            'user_id' => $user->getKey()
        ]);
    }

    public function withGroupId(int $groupId)
    {
        return $this->state([
            'group_id' => $groupId,
        ]);
    }
}
