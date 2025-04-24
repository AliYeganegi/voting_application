<?php

namespace App\Http\Middleware;

use App\Models\VotingSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVotingIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $session = VotingSession::where('is_active', true)->first();

        if (!$session || now()->lt($session->start_at) || now()->gt($session->end_at)) {
            return redirect()->route('vote.closed');
        }

        return $next($request);
    }
}
