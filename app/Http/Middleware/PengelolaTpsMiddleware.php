<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PengelolaTpsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isPengelolaTps()) {
            abort(403, 'Halaman ini khusus untuk Pengelola TPS.');
        }

        return $next($request);
    }
}

