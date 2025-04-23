<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\VotingSession;

class CheckVotingSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $session = VotingSession::where('is_active', true)->latest()->first();

        if (!$session ||
            ($session->start_at && now()->lt($session->start_at)) ||
            ($session->end_at && now()->gt($session->end_at))) {
            return response('Voting is not active at the moment.', 403);
        }

        return $next($request);
    }
}
