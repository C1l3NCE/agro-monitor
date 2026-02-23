<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            abort(403);
        }

        if (!auth()->user()->hasRole($roles)) {
            abort(403, 'Недостаточно прав');
        }

        return $next($request);
    }
}
