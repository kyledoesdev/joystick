<?php

namespace App\Http\Middleware;

use App\Models\Invite;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GroupMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        //dd(request()->groupId);

        $userInGroup = Invite::query()
            ->where('group_id', request()->groupId)
            ->where('user_id', auth()->id())
            ->where('status', 'accepted')
            ->exists();

        if (! $userInGroup) {
            abort(403);
        }

        return $next($request);
    }
}
