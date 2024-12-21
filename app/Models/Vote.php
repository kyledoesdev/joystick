<?php

namespace App\Models;

use App\Models\Model;

class Vote extends Model
{
    const UP_VOTE = 1;
    const DOWN_VOTE = 2;
    const NEUTRAL = 3;

    protected $fillable = [
        'suggestion_id',
        'group_id',
        'user_id',
        'vote'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function suggestion()
    {
        return $this->belongsTo(Suggestion::class);
    }

    public static function getTypes(): array
    {
        return [
            self::UP_VOTE,
            self::DOWN_VOTE,
            self::NEUTRAL,
        ];
    }
}
