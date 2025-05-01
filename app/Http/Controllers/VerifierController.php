<?php
// app/Http/Controllers/VerifierController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VotingSession;
use App\Models\Verification;
use App\Models\ValidVoter;
use App\Models\Vote;

class VerifierController extends Controller
{
    public function index()
    {
        // 1. Find active session (or null)
        $session = VotingSession::where('is_active', true)
                    ->latest()
                    ->first();

        // 2. If there is one, expire old and load queue, otherwise empty collection
        if ($session) {
            Verification::where('voting_session_id', $session->id)
                ->where('status', 'pending')
                ->where('expires_at', '<', now())
                ->update(['status' => 'expired']);

            $queue = Verification::where('voting_session_id', $session->id)
                ->where('status', 'pending')
                ->get();
        } else {
            $queue = collect(); // empty collection
        }

        // 3. Always render with both variables
        return view('verify.index', compact('session', 'queue'));
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'voter_id' => 'required|string',
        ]);

        // 1) Ensure voter exists
        $voter = ValidVoter::where('voter_id', $data['voter_id'])->first();
        if (! $voter) {
            return back()->withErrors(['voter_id'=>'کد ملی نامعتبر است.']);
        }

        // 2) Get active session
        $session = VotingSession::where('is_active', true)
                    ->latest()
                    ->firstOrFail();

        // 3) Compute hash
        $voterHash = hash('sha256', $data['voter_id']);

        // 4) Prevent re-queue if already voted
        $hasVoted = Vote::where('voting_session_id', $session->id)
                        ->where('hashed_voter_id', $voterHash)
                        ->exists();

        if ($hasVoted) {
            return back()->withErrors([
                'voter_id' => 'این کد ملی قبلاً رأی خود را ثبت کرده است.'
            ]);
        }

        // 5) Expire old
        Verification::where('voting_session_id', $session->id)
            ->where('status','pending')
            ->where('expires_at','<',now())
            ->update(['status'=>'expired']);

        // 6) Count current pending
        $count = Verification::where('voting_session_id', $session->id)
            ->where('status','pending')
            ->count();

        if ($count >= 3) {
            return back()->withErrors(['voter_id'=>'صف تأیید پر است. لطفاً صبر کنید.']);
        }

        // 7) Add to queue
        Verification::create([
            'voting_session_id' => $session->id,
            'voter_id'          => $data['voter_id'],
            'voter_hash'        => $voterHash,
            'started_at'        => now(),
            'expires_at'        => now()->addMinutes(15),
            'status'            => 'pending',
        ]);

        return back()->with('success','کد ملی به صف تأیید اضافه شد.');
    }
}
