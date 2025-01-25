<?php

namespace App\Models;

use App\Models\Model;

class Game extends Model
{
    protected $fillable = [
        'game_id',
        'name',
        'cover',
        'is_custom'
    ];

    public function casts(): array
    {
        return [
            'is_custom' => 'boolean'
        ];
    }

    public static function getBlankCover(): string
    {
        return 'https://static-cdn.jtvnw.net/ttv-boxart/66082-285x380.jpg';
    }
}
