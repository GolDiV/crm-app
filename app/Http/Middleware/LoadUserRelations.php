<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoadUserRelations
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            auth()->user()->load('role');

            Log::info('✅ Middleware LoadUserRelations отработал');
        }

        return $next($request);
    }
}
