<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'external_id',
        'name',
        'email',
        'avatar',
        'timezone',
        'ip_address',
        'user_agent',
        'user_platform',
        'external_token',
        'external_refresh_token',
    ];

    protected $hidden = [
        'external_token',
        'external_refresh_token',
        'ip_address'
    ];

    public function ownedGroups(): HasMany
    {
        return $this->hasMany(Group::class, 'owner_id', 'id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class, 'user_id', 'id');
    }

    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class);
    }

    public static function forGroupFormTable($groupId = null, string $search, string $sortBy, string $sortDirection)
    {
        return self::query()
            ->select([
                'users.id',
                'users.name',
                'users.avatar',
                'invite_statuses.display_name as status_name',
                'invite_statuses.badge_color as status_color'
            ])
            ->leftJoin('invites', function($join) use ($groupId) {
                $join->on('users.id', '=', 'invites.user_id')
                    ->where('invites.group_id', '=', $groupId);
            })
            ->leftJoin('invite_statuses', function($join) use ($groupId) {
                $join->on('invites.status_id', '=', 'invite_statuses.id');
            })
            ->when($search != '', fn($q) => $q->where('users.name', 'like', "%{$search}%"))
            ->tap(fn ($query) => $sortBy ? $query->orderBy($sortBy, $sortDirection) : $query)
            ->paginate(5);
    }
}
