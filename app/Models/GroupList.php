<?php

namespace App\Models;

use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GroupList extends Model
{
    protected $table = 'lists';

    protected $fillable = [
        'group_id',
        'user_id',
        'name',
        'start_time'
    ];

    public function casts(): array
    {
        return [
            'start_time' => 'datetime'
        ];
    }

    public function creator(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(Suggestion::class, 'list_id', 'id');
    }

    public function votes(): HasManyThrough
    {
        return $this->hasManyThrough(Vote::class, Suggestion::class, 'list_id', 'suggestion_id', 'id', 'id');
    }

    public function getStartTimeAttribute()
    {
        return $this->attributes['start_time'] != null
            ? Carbon::parse($this->attributes['start_time'])->inUserTimezone()->format('m/d/Y g:i A T')
            : null;
    }
}