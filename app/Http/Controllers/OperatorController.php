<?php

namespace App\Http\Controllers;

use App\Models\ImportFile;
use App\Models\VotingSession;
use App\Models\OperatorApproval;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OperatorController extends Controller
{
    public function panel()
    {
        $session = VotingSession::where(function ($q) {
            $q->where('is_active', true)
                ->orWhereNull('end_at');
        })->latest()->first();

        $startApps         = optional($session)->startApprovals ?? collect();
        $endApps           = optional($session)->endApprovals   ?? collect();
        $lastVoterFile     = ImportFile::where('type', 'voters')->latest()->first();
        $lastCandidateFile = ImportFile::where('type', 'candidates')->latest()->first();

        return view('operator.session', compact(
            'session',
            'startApps',
            'endApps',
            'lastVoterFile',
            'lastCandidateFile'
        ));
    }


    public function approveStart(VotingSession $session)
    {
        // cannot approve “start” if already active
        if ($session->is_active) {
            return back()->withErrors('جلسه قبلاً فعال شده.');
        }

        // prevent duplicate
        if ($session->startApprovals()->where('operator_id', Auth::id())->exists()) {
            return back()->with('success', 'شما قبلاً تأیید کرده‌اید.');
        }

        // record their approval
        OperatorApproval::create([
            'voting_session_id' => $session->id,
            'operator_id'       => Auth::id(),
            'action'              => 'start',
        ]);

        // if now ≥3 approvals, flip session active
        if ($session->startApprovals()->count() >= 3) {
            $session->update([
                'is_active' => true,
                'start_at'  => now(),
            ]);
        }

        return back();
    }

    public function approveEnd(VotingSession $session)
    {
        // cannot approve “end” unless active
        if (! $session->is_active) {
            return back()->withErrors('جلسه فعال نیست.');
        }

        if ($session->endApprovals()->where('operator_id', Auth::id())->exists()) {
            return back()->with('success', 'شما قبلاً تأیید کرده‌اید.');
        }

        OperatorApproval::create([
            'voting_session_id' => $session->id,
            'operator_id'       => Auth::id(),
            'action'              => 'end',
        ]);

        if ($session->endApprovals()->count() >= 3) {
            $session->update([
                'is_active' => false,
                'end_at'    => now(),
            ]);

            $results = User::where('is_candidate', true)
                ->withCount(['votes as votes_count' => function ($q) use ($session) {
                    $q->where('voting_session_id', $session->id);
                }])->orderByDesc('votes_count')->get();

            // Generate and store PDF, now passing both $results and $session
            $pdf = Pdf::loadView('admin.results-pdf', compact('results', 'session'));

            $filePath = 'results/session_' . $session->id . '.pdf';
            Storage::put('public/' . $filePath, $pdf->output());
            $session->update(['result_file' => $filePath]);
        }

        return back()->with('success', 'رای گیری پایان یافت و فایل نتایج ایجاد شد.');
    }

    public function createAndApproveStart(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // 1) create a stub session (inactive, no start_at yet)
        $session = VotingSession::create([
            'name'      => $data['name'],
            'start_at'  => null,
            'end_at'    => null,
            'is_active' => false,
        ]);

        // 2) record your approval
        $session->startApprovals()->create([
            'operator_id' => auth()->id(),
        ]);

        return redirect()
            ->route('operator.session')
            ->with('success', 'جلسه جدید ایجاد و شروع رأی‌گیری تأیید شد.');
    }

    public function history()
    {
        $sessions = VotingSession::with([
            'startApprovals.operator',
            'endApprovals.operator'
        ])->orderByDesc('id')->get();

        return view('operator.history', compact('sessions'));
    }

    public function cancelSession(VotingSession $session)
    {
        // delete any approvals
        OperatorApproval::where('voting_session_id', $session->id)->delete();
        // delete the session itself
        $session->delete();

        return redirect()
            ->route('operator.session')
            ->with('success', 'جلسه لغو شد.');
    }
}
