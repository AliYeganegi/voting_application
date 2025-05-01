<?php
// app/Http/Middleware/VerifierMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifierMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check() || ! auth()->user()->is_verifier) {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
}
