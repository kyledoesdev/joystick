<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invite extends Model
{
    const PENDING = 'pending';
    const ACCEPTED = 'accepted';
    const DECLINED = 'declined';
    const REMOVED = 'removed';

    protected $fillable = [
        'group_id',
        'user_id',
        'status',
        'invited_at',
        'responded_at',
    ];

    public function casts()
    {
        return [
            'invited_at' => 'timestamp',
            'responded_at' => 'timestamp'
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
}
