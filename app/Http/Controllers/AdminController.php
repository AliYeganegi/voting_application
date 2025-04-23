<?php

namespace App\Http\Controllers;

use App\Models\VotingSession;
use Illuminate\Http\Request;
use App\Models\Vote;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VoteExport;

class AdminController extends Controller
{
    public function dashboard()
    {
        $session = VotingSession::latest()->first();
        $candidates = User::where('is_candidate', true)->get();

        return view('admin.dashboard', compact('session', 'candidates'));
    }

    public function startVoting()
    {
        VotingSession::create([
            'start_at' => now(),
            'is_active' => true,
        ]);

        return back()->with('success', 'Voting started');
    }

    public function endVoting()
    {
        $session = VotingSession::latest()->first();
        $session->update([
            'end_at' => now(),
            'is_active' => false,
        ]);

        return back()->with('success', 'Voting ended');
    }

    public function exportResults()
    {
        $session = VotingSession::latest()->first();

        if ($session->is_active) {
            return back()->withErrors(['error' => 'Voting is still active. Export is allowed after ending.']);
        }

        return Excel::download(new VoteExport, 'voting_results.xlsx');
    }
}
