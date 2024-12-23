<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\InviteStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(50)->create();
        Group::factory()->create();

        InviteStatus::create(['name' => 'pending', 'display_name' => 'Pending', 'badge_color' => 'yellow']);
        InviteStatus::create(['name' => 'accepted', 'display_name' => 'Accepted', 'badge_color' => 'green']);
        InviteStatus::create(['name' => 'declined', 'display_name' => 'Declined', 'badge_color' => 'red']);
        InviteStatus::create(['name' => 'owner_removed', 'display_name' => 'Owner Removed', 'badge_color' => 'red']);
        InviteStatus::create(['name' => 'user_left', 'display_name' => 'User Left', 'badge_color' => 'red']);
    }
}
