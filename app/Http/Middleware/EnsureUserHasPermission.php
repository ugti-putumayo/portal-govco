<?php

namespace App\Http\Middleware;
use Closure;

class EnsureUserHasPermission
{
    public function handle($request, Closure $next, string $permission)
    {
        $user = $request->user();
        abort_unless($user, 401);
        abort_unless($user->canPerm($permission), 403);
        return $next($request);
    }
}