<?php

namespace App\Http\Middleware;

use App\Models\Invite;
use App\Models\InviteStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GroupMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $userInGroup = Invite::query()
            ->where('group_id', request()->route('group')->getKey())
            ->where('user_id', auth()->id())
            ->where('status_id', InviteStatus::ACCEPTED)
            ->exists();

        if (! $userInGroup) {
            abort(403);
        }

        return $next($request);
    }
}
