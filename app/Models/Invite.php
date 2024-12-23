<?php

namespace App\Models;

use App\Models\InviteStatus;
use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invite extends Model
{
    protected $fillable = [
        'group_id',
        'user_id',
        'status_id',
        'invited_at',
        'responded_at',
    ];

    protected $with = [
        'status'
    ];

    public function casts()
    {
        return [
            'invited_at' => 'datetime',
            'responded_at' => 'datetime'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function status(): HasOne
    {
        return $this->hasOne(InviteStatus::class, 'id', 'status_id');
    }

    public function getInvitedAtAttribute(): string
    {
        return Carbon::parse($this->attributes['invited_at'])->inUserTimezone()->format('m/d/Y g:i A T');
    }

    public function getRespondedAtAttribute(): string
    {
        return Carbon::parse($this->attributes['responded_at'])->inUserTimezone()->format('m/d/Y g:i A T');
    }

    public static function getPending(): int
    {
        return self::query()->where('user_id', auth()->id())->where('status_id', InviteStatus::PENDING)->count();
    }
}
