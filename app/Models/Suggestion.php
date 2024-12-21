<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Suggestion extends Model
{
    protected $fillable = [
        'feed_id',
        'game_id',
        'user_id',
        'game_mode',
    ];

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