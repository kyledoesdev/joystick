<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->name();

        return [
            'external_id' => Str::random(16),
            'name' => $name,
            'email' => fake()->unique()->safeEmail(),
            'avatar' => "https://api.dicebear.com/7.x/initials/svg?seed=" . $name,
            'external_token' => Str::random(16),
            'external_refresh_token' => Str::random(32),
            'timezone' => 'America/New_York'
        ];
    }
}
