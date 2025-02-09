<?php

namespace App\Models;

use App\Actions\DiscordPing;
use App\Models\GroupGame;
use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'name',
        'discord_webhook_url',
        'discord_updates',
        'owner_feeds_only'
    ];

    /* danger - handle with care */
    protected $with = [
        'settings',
    ];

    public static function boot()
    {
        parent::boot();

        static::updated(function($model) {
            if ($model->isDirty('discord_webhook_url')) {
                (new DiscordPing)->handle($model, 'Webhook updated successfully!');
            }
        });

        static::deleted(function($model) {
            (new DiscordPing)->handle($model, 'The group has been deleted.', 'error');
        });
    }

    public function casts(): array
    {
        return [
            'discord_updates' => 'boolean',
            'owner_feeds_only' => 'boolean'
        ];
    }

    public function owner(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }

    public function feeds(): HasMany
    {
        return $this->hasMany(Feed::class);
    }

    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class);
    }

    public function userPreferences(): HasMany
    {
        return $this->hasMany(UserGroupPreference::class);
    }

    public function suggestions(): HasManyThrough
    {
        return $this->hasManyThrough(Suggestion::class, Feed::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(GroupSetting::class);
    }
}
