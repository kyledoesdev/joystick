<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GameFactory extends Factory
{
    public function definition(): array
    {
        return [
            'game_id' => Str::random(16),
            'name' => fake()->catchPhrase(),
            'cover' => 'https://google.com/images/fake_image.jpg'
        ];
    }
}
