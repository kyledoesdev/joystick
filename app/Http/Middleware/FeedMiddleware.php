<?php

namespace App\Http\Middleware;

use App\Models\GroupList;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeedMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $list = GroupList::with(['group.members'])->findOrFail($request->id);

        if ($list->group->members->where('id', auth()->id())->isEmpty()) {
            abort(403);
        }

        return $next($request);
    }
}
