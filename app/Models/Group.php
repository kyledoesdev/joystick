<?php

namespace App\Models;

use App\Models\GroupGame;
use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Group extends Model
{
    protected $fillable = [
        'owner_id',
        'name'
    ];

    public function owner(): HasOne
    {
        $this->hasOne(User::class, 'owner_id', 'id');
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
}
