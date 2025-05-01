<?php
// app/Http/Controllers/VerifierController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VotingSession;
use App\Models\Verification;
use App\Models\ValidVoter;

class VerifierController extends Controller
{
    public function index()
    {
        // Find active session
        $session = VotingSession::where('is_active', true)
                    ->latest()
                    ->first();

        if (! $session) {
            return view('verify.index')->withErrors([
                'info' => 'هیچ جلسهٔ رأی‌گیری فعالی وجود ندارد.'
            ]);
        }

        // Expire old
        Verification::where('voting_session_id', $session->id)
            ->where('status', 'pending')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);

        // Current queue
        $queue = Verification::where('voting_session_id', $session->id)
            ->where('status', 'pending')
            ->get();

        return view('verify.index', compact('session','queue'));
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'voter_id' => 'required|string',
        ]);

        // Ensure voter exists
        $voter = ValidVoter::where('voter_id', $data['voter_id'])->first();
        if (! $voter) {
            return back()->withErrors(['voter_id'=>'کد ملی نامعتبر است.']);
        }

        // Active session
        $session = VotingSession::where('is_active', true)
                    ->latest()
                    ->firstOrFail();

        // Expire old
        Verification::where('voting_session_id', $session->id)
            ->where('status', 'pending')
            ->where('expires_at', '<', now())
            ->update(['status'=>'expired']);

        // Count pending
        $count = Verification::where('voting_session_id', $session->id)
            ->where('status', 'pending')
            ->count();

        if ($count >= 3) {
            return back()->withErrors(['voter_id'=>'صف تأیید پر است. لطفا صبر کنید.']);
        }

        // Prepare times
        $now = now();
        $exp = $now->clone()->addMinutes(15);

        // Insert
        Verification::create([
            'voting_session_id' => $session->id,
            'voter_id'          => $data['voter_id'],
            'voter_hash'        => hash('sha256', $data['voter_id']),
            'started_at'        => $now,
            'expires_at'        => $exp,
            'status'            => 'pending',
        ]);

        return back()->with('success','کد ملی به صف تأیید اضافه شد.');
    }
}
