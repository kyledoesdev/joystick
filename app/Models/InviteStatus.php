<?php

namespace App\Models;

use App\Models\Model;
use ReflectionClass;

class InviteStatus extends Model
{
    protected $table = 'invite_statuses';

    const PENDING = 1;
    const ACCEPTED = 2;
    const DECLINED = 3;
    const OWNER_REMOVED = 4;
    const USER_LEFT = 5;

    protected $fillable = [
        'name',
        'display_name',
        'badge_color'
    ];

    public static function getStatuses(): array
    {
        $constants = (new ReflectionClass(static::class))->getConstants();

        return array_filter($constants, fn ($constant) => !in_array($constants, ['CREATED_AT', 'UPDATED_AT']), ARRAY_FILTER_USE_KEY);
    }
}
