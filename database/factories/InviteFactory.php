<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\InviteStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InviteFactory extends Factory
{
    public function definition()
    {
        return [
            'group_id' => Group::factory(), // Creates a new Group
            'user_id' => User::factory(),  // Creates a new User
            'status_id' => InviteStatus::where('name', 'accepted')->first()->getKey(),
            'invited_at' => now()
        ];
    }
}
