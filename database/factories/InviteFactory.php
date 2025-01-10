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
            'group_id' => Group::factory(),
            'user_id' => User::factory(),
            'status_id' => InviteStatus::ACCEPTED,
            'invited_at' => now()
        ];
    }
}
