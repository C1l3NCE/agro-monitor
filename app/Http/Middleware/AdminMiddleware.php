<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Если пользователь не авторизован
        if (!auth()->check()) {
            abort(403, 'Доступ запрещён');
        }

        // Если пользователь не админ
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Доступ только для администратора');
        }

        return $next($request);
    }
}
