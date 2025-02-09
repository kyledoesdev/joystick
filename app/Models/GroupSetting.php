<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupSetting extends Model
{
    protected $fillable = [
        'group_id',
        'send_suggestion_vote_alerts',
        'd_create_feed_alerts',
        'd_destroy_feed_alerts',
        'd_create_suggestion_alerts',
        'd_user_joined_alerts',
        'd_user_left_alerts',
    ];

    public function casts(): array
    {
        return [
            'send_suggestion_vote_alerts' => 'boolean',
            'd_create_feed_alerts' => 'boolean',
            'd_destroy_feed_alerts' => 'boolean',
            'd_create_suggestion_alerts' => 'boolean',
            'd_user_joined_alerts' => 'boolean',
            'd_user_left_alerts' => 'boolean',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public static function getDiscordPingSettings(): array
    {
        return [
            'd_create_feed_alerts' => 'Feed Created',
            'd_destroy_feed_alerts' => 'Feed Deleted',
            'd_create_suggestion_alerts' => 'Suggestion Created',
            'd_user_joined_alerts' => 'User Joined',
            'd_user_left_alerts' => 'User Left'
        ];
    }
}
