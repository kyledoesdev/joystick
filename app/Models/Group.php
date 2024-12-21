<?php

namespace App\Models;

use App\Models\GroupGame;
use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot(['status'])
            ->withTimestamps();
    }

    public function lists(): HasMany
    {
        return $this->hasMany(GroupList::class);
    }

    public function suggestions()
    {
        return $this->hasManyThrough(Suggestion::class, GroupList::class);
    }
}
