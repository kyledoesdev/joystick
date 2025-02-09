<?php

use App\Models\Group;
use App\Models\GroupSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id');
            $table->boolean('send_suggestion_vote_alerts')->default(false);
            $table->boolean('d_create_feed_alerts')->default(true);
            $table->boolean('d_destroy_feed_alerts')->default(false);
            $table->boolean('d_create_suggestion_alerts')->default(true);
            $table->boolean('d_user_joined_alerts')->default(false);
            $table->boolean('d_user_left_alerts')->default(false);
            $table->timestamps();
        });

        Group::all()->each(function(Group $group) {
            GroupSetting::create(['group_id' => $group->getKey()]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_settings');
    }
};
