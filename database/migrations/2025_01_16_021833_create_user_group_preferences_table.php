<?php

use App\Models\Group;
use App\Models\InviteStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('user_group_preferences')) {
            Schema::create('user_group_preferences', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('group_id')->constrained()->onDelete('cascade');
                $table->string('color')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        foreach(Group::all() as $group) {
            $invites = $group->invites()->where('status_id', InviteStatus::ACCEPTED)->get();

            foreach ($invites as $invite) {
                $group->userPreferences()->updateOrcreate([
                    'user_id' => $invite->user_id
                ], [
                    'color' => null
                ]);

                Log::channel('debug_discord')->info("created preference record for: {$invite->user->name} for {$group->name}.");

                sleep(5);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_group_preferences');
    }
};
