<?php

namespace App\Http\Controllers;

use App\Models\VotingSession;
use Illuminate\Http\Request;
use App\Models\Vote;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VoteExport;
use App\Models\ImportFile;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function dashboard()
    {
        $session          = VotingSession::latest()->first();
        $lastVoterFile    = ImportFile::where('type', 'voters')->latest()->first();
        $lastCandidateFile = ImportFile::where('type', 'candidates')->latest()->first();

        return view('admin.dashboard', compact(
            'session',
            'lastVoterFile',
            'lastCandidateFile'
        ));
    }

    public function startVoting(Request $request)
    {
        $data = $request->validate([
            'start_at' => 'nullable|date',
            'end_at'   => 'nullable|date|after:start_at',
        ]);

        // Decide the real start time:
        $start = $data['start_at']
            ? \Carbon\Carbon::parse($data['start_at'])
            : now();

        // Update the existing session if present, or create new:
        $session = VotingSession::latest()->first();
        if ($session) {
            $session->update([
                'start_at'  => $start,
                'end_at'    => $data['end_at'] ?? null,
                'is_active' => true,  // will be checked with times below
            ]);
        } else {
            VotingSession::create([
                'start_at'  => $start,
                'end_at'    => $data['end_at'] ?? null,
                'is_active' => true,
            ]);
        }

        return back()->with('success', 'Voting session scheduled.');
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

        // 0) If no session exists, tell the admin
        if (! $session) {
            return back()->withErrors([
                'error' => 'هنوز جلسه رأی‌گیری ایجاد نشده است.'
            ]);
        }

        // 1) Block if still active or not yet ended
        if (
            $session->is_active
            || ($session->end_at && now()->lt($session->end_at))
        ) {
            return back()->withErrors([
                'error' => 'نتایج تنها پس از پایان رأی‌گیری قابل مشاهده هستند.'
            ]);
        }

        // 2) Aggregate vote counts
        $results = User::where('is_candidate', true)
            ->withCount(['votes as votes_count' => function ($q) {
                $q->select(DB::raw("count(*)"));
            }])
            ->orderByDesc('votes_count')
            ->get();

        // 3) Show the results
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
