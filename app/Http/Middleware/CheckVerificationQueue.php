<?php
// app/Http/Middleware/CheckVerificationQueue.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\VotingSession;
use App\Models\Verification;

class CheckVerificationQueue
{
    public function handle(Request $request, Closure $next)
    {
        // 1) Find active session
        $session = VotingSession::where('is_active', true)
                    ->latest()
                    ->first();

        if (! $session) {
            abort(403, 'Voting is not active.');
        }

        // 2) Expire old pending verifications
        Verification::where('voting_session_id', $session->id)
            ->where('status', 'pending')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);

        // 3) Get voter_id from input
        $voterId = $request->input('voter_id');

        // 4) Compute hash
        $voterHash = hash('sha256', $voterId);

        // 5) Check for a pending verification
        $exists = Verification::where('voting_session_id', $session->id)
            ->where('voter_hash', $voterHash)
            ->where('status', 'pending')
            ->exists();

        if (! $exists) {
            return back()->withErrors([
                'voter_id' => 'شما هنوز در صف تأیید نیستید؛ لطفاً از صفحه تأییدکننده اقدام کنید.'
            ]);
        }

        return $next($request);
    }
}
