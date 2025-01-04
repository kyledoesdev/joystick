<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suggestion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'feed_id',
        'game_id',
        'user_id',
        'game_mode',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function($model) {
            $user = auth()->check() ? auth()->user()->name : null;
            $model->load('feed', 'feed.group', 'game');

            $model->feed->group->writeToDiscord("{$user} added the game suggession: {$model->game->name} to the feed: {$model->feed->name}.");
        });
    }

    public function feed(): BelongsTo
    {
        return $this->belongsTo(Feed::class);
    }

    public function game(): HasOne
    {
        return $this->hasOne(Game::class, 'id', 'game_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}