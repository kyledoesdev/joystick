<?php

namespace App\Models;

use App\Models\GroupGame;
use App\Models\Model;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use MarvinLabs\DiscordLogger\Discord\Exceptions\MessageCouldNotBeSent;
use MarvinLabs\DiscordLogger\Logger;

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

    public static function boot()
    {
        parent::boot();

        static::created(function($model) {
            if ($model->discord_updates === true) {
                $model->writeToDiscord('Webhook connected successfully!');
            }
        });

        static::updated(function($model) {
            if ($model->discord_updates === true && $model->isDirty('discord_webhook_url')) {
                $model->writeToDiscord('Webhook updated successfully!');
            }
        });

        static::deleted(function($model) {
            if ($model->discord_updates === true) {
                $model->writeToDiscord('The group has been deleted.', 'error');
            }
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

    public function suggestions(): HasManyThrough
    {
        return $this->hasManyThrough(Suggestion::class, Feed::class);
    }

    public function writeToDiscord(string $message, string $level = 'info'): void
    {
        if (!is_null($this->discord_webhook_url)) {
            try {
                config(['logging.channels.discord' => [
                    'driver' => 'custom',
                    'via' => Logger::class,
                    'level' => 'info',
                    'url' => $this->discord_webhook_url,
                ]]);

                Log::channel('discord')->{$level}($message);
            } catch (MessageCouldNotBeSent $e) {
                Log::warning('Invalid Discord webhook URL provided.');
            } catch (Exception $e) {
                Log::warning('An error occurred while logging to Discord.');
            }
        } else {
            Log::info($message);
        }
    }
}
