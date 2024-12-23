<?php

namespace App\Models;

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
        'owner_feeds_only'
    ];

    public function casts(): array
    {
        return [
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
}
