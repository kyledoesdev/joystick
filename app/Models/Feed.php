<?php

namespace App\Models;

use App\Actions\DiscordPing;
use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feed extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'group_id',
        'user_id',
        'name',
        'start_time'
    ];

    public static function boot()
    {
        parent::boot();

        $user = auth()->check() ? auth()->user()->name : null;

        static::creating(function($model) use ($user) {
            $startTime = isset($model->attributes['start_time']) && $model->attributes['start_time'] != null 
                ? 'which has a start time of: ' . $model->start_time 
                : '.';

            (new DiscordPing)->handle($model->group, "{$user} created feed: {$model->name} {$startTime}");
        });
    }

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
        return $this->hasMany(Suggestion::class, 'feed_id', 'id');
    }

    public function votes(): HasManyThrough
    {
        return $this->hasManyThrough(Vote::class, Suggestion::class, 'feed_id', 'suggestion_id', 'id', 'id');
    }

    public function getStartTimeAttribute()
    {
        return $this->attributes['start_time'] != null
            ? Carbon::parse($this->attributes['start_time'])->inUserTimezone()->format('m/d/Y g:i A T')
            : null;
    }
}
