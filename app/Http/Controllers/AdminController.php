<?php

namespace App\Http\Controllers;

use App\Models\VotingSession;
use Illuminate\Http\Request;
use App\Models\Vote;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VoteExport;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function results()
    {
        $session = VotingSession::latest()->first();

        // Block if still active
        if ($session->is_active || ($session->end_at && now()->lt($session->end_at))) {
            return back()->withErrors(['error' => 'Results are only available after voting ends.']);
        }

        // Aggregate vote counts
        $results = User::where('is_candidate', true)
            ->withCount(['votes as votes_count' => function ($q) {
                $q->select(DB::raw("count(*)"));
            }])
            ->orderByDesc('votes_count')
            ->get();

        return view('admin.results', compact('results'));
    }

    public function downloadPdf()
    {
        $session = VotingSession::latest()->first();

        if ($session->is_active || ($session->end_at && now()->lt($session->end_at))) {
            abort(403, 'Results aren’t available yet.');
        }

        $results = User::where('is_candidate', true)
            ->withCount(['votes as votes_count' => function ($q) {
                $q->select(DB::raw("count(*)"));
            }])
            ->orderByDesc('votes_count')
            ->get();

        $pdf = Pdf::loadView('admin.results-pdf', compact('results'));
        return $pdf->download('voting_results.pdf');
    }

    public function stopVoting()
    {
        $session = VotingSession::where('is_active', true)->latest()->first();

        if ($session) {
            $session->update([
                'is_active' => false,
                'end_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Voting ended.');
    }
}
