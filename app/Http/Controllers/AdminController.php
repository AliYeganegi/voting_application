<?php

namespace App\Http\Controllers;

use App\Models\VotingSession;
use Illuminate\Http\Request;
use App\Models\Vote;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VoteExport;
use App\Models\Ballot;
use App\Models\ImportFile;
use App\Models\ValidVoter;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $session          = VotingSession::latest()->first();
        $lastVoterFile    = ImportFile::where('type', 'voters')->latest()->first();
        $lastCandidateFile = ImportFile::where('type', 'candidates')->latest()->first();
        $previousSessions = VotingSession::where('is_active', false)->orderBy('start_at', 'desc')->get();

        return view('admin.dashboard', compact(
            'session',
            'lastVoterFile',
            'lastCandidateFile',
            'previousSessions'
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

        // Close any previous active session
        VotingSession::where('is_active', true)->update(['is_active' => false]);

        // Reset voters
        ValidVoter::query()->update(['has_voted' => false]);

        // Create new session
        $session = VotingSession::create([
            'start_at' => $start,
            'end_at' => $data['end_at'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'زمان رای گیری ثبت شد');
    }

    public function endVoting()
    {
        $session = VotingSession::where('is_active', true)->latest()->first();
        if (! $session) {
            return back()->withErrors(['error' => 'رای گیری فعالی یافت نشد']);
        }

        $session->update(['is_active' => false, 'end_at' => now()]);

        // Gather results for this session
        $results = User::where('is_candidate', true)
            ->withCount(['votes as votes_count' => function ($q) use ($session) {
                $q->where('voting_session_id', $session->id);
            }])->orderByDesc('votes_count')->get();

        // Generate and store PDF, now passing both $results and $session
        $pdf = Pdf::loadView('admin.results-pdf', compact('results', 'session'));

        $filePath = 'results/session_' . $session->id . '.pdf';
        Storage::put('public/' . $filePath, $pdf->output());
        $session->update(['result_file' => $filePath]);

        return back()->with('success', 'رای گیری پایان یافت و فایل نتایج ایجاد شد.');
    }


    public function exportResults()
    {
        $session = VotingSession::latest()->first();

        if ($session->is_active) {
            return back()->withErrors(['error' => 'رای گیری در حال اجراست گرفتن خروجی پس از اتمام رای گیری ممکن است']);
        }

        return Excel::download(new VoteExport, 'voting_results.xlsx');
    }

    public function results(VotingSession $session)
    {
        if ($session->is_active || ($session->end_at && now()->lt($session->end_at))) {
            return back()->withErrors(['error' => 'نتایج تنها پس از پایان جلسه قابل نمایش است.']);
        }

        $results = User::where('is_candidate', true)
            ->withCount(['votes as votes_count' => function ($q) use ($session) {
                $q->where('voting_session_id', $session->id);
            }])
            ->orderByDesc('votes_count')
            ->get();

        // pass both $results and $session
        return view('admin.results', compact('results', 'session'));
    }

    public function downloadResultPdf(VotingSession $session)
    {
        if (! $session->result_file) {
            abort(404);
        }
        return Storage::download('public/' . $session->result_file);
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

        return redirect()->back()->with('success', 'رای گیری به پایان رسید');
    }

    public function previousSessions()
    {
        $sessions = VotingSession::where('is_active', false)->orderBy('start_at', 'desc')->get();
        return view('admin.sessions', compact('sessions'));
    }

    public function viewBallots(VotingSession $session)
    {
        // only ended sessions:
        if ($session->is_active || ($session->end_at && now()->lt($session->end_at))) {
            return back()->withErrors(['error' => 'Ballots visible only after session ends.']);
        }

        // load ballots with candidates
        $ballots = Ballot::with('candidates')
            ->where('voting_session_id', $session->id)
            ->get();

        return view('admin.ballots', compact('session', 'ballots'));
    }
}
