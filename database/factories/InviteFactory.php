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

    public function forUser(User $user): self
    {
        return $this->state([
            'user_id' => $user->getKey(),
        ]);
    }

    public function forGroup(Group $group): self
    {
        return $this->state([
            'group_id' => $group->getKey(),
        ]);
    }

    public function withStatus(int $inviteStatus): self
    {
        return $this->state([
            'status_id' => $inviteStatus,
        ])->afterCreating(function ($invite) use ($inviteStatus) {
            if ($inviteStatus === InviteStatus::ACCEPTED) {
                $invite->group->userPreferences()->create([
                    'user_id' => $invite->user_id,
                    'color' => '#FFFFFF'
                ]);
            }
        });;
    }
}
