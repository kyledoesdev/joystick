<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->contstrained()->onDelete('cascade');
            $table->string('name');
            $table->string('discord_webhook_url')->nullable();
            $table->boolean('discord_updates')->default(false)->nullable();
            $table->boolean('owner_feeds_only')->default(false)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('feeds', function(Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamp('start_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
        Schema::dropIfExists('feeds');
    }
};
