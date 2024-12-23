<?php

namespace App\Models;

use App\Models\Model;

class Game extends Model
{
    protected $fillable = [
        'game_id',
        'name',
        'cover'
    ];
}
