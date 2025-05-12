<?php

namespace App\Http\Controllers;

use App\Models\Ballot;
use App\Models\User;
use App\Models\ValidVoter;
use App\Models\Verification;
use App\Models\Vote;
use App\Models\VotingSession;
use App\Notifications\VoteCastNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Storage;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class VoteController extends Controller
{
    public function index()
    {
        if (!auth()->user()->is_voter) {
            abort(403, 'Unauthorized');
        }

        $candidates = User::where('is_candidate', true)->get();
        $session    = VotingSession::where('is_active', true)
            ->latest()
            ->firstOrFail();

        return view('vote.index', compact('candidates', 'session'));
    }

    public function confirm(Request $request)
    {
        if (!auth()->user()->is_voter) {
            abort(403, 'Unauthorized');
        }

        if ($request->isMethod('get')) {
            return $this->index();
        }

        $data = $request->validate([
            'voter_id'        => 'required|string',
            'candidate_ids'   => 'nullable|array|max:5',
            'candidate_ids.*' => 'exists:users,id',
        ], [
            'candidate_ids.max'      => 'شما نمی‌توانید بیش از ۵ نامزد انتخاب کنید.',
            'candidate_ids.*.exists' => 'نامزد انتخاب‌شده نامعتبر است.',
        ]);

        $voter = ValidVoter::where('voter_id', $data['voter_id'])->first();
        if (! $voter) {
            return back()->withErrors(['voter_id' => 'کد ملی نامعتبر است.']);
        }

        $selectedIds = $data['candidate_ids'] ?? [];
        $candidates  = User::whereIn('id', $selectedIds)->get();

        return view('vote.confirm', [
            'voter_id'   => $data['voter_id'],
            'first_name' => $voter->first_name,
            'last_name'  => $voter->last_name,
            'candidates' => $candidates,
        ]);
    }

    public function submit(Request $request)
    {
        if (!auth()->user()->is_voter) {
            abort(403, 'Unauthorized');
        }

        $data = $request->validate([
            'voter_id'        => 'required|string',
            'candidate_ids'   => 'nullable|array|max:5',
            'candidate_ids.*' => 'exists:users,id',
        ], [
            'candidate_ids.max'      => 'شما نمی‌توانید بیش از ۵ نامزد انتخاب کنید.',
            'candidate_ids.*.exists' => 'یک یا چند نامزد انتخاب‌شده نامعتبرند.',
        ]);

        $voter   = ValidVoter::where('voter_id', $data['voter_id'])->firstOrFail();
        $session = VotingSession::where('is_active', true)->latest()->firstOrFail();
        $hash    = hash('sha256', $data['voter_id']);

        $already  = Vote::where('voting_session_id', $session->id)
            ->where('hashed_voter_id', $hash)
            ->count();
        $newVotes = max(1, count($data['candidate_ids'] ?? []));
        if ($already + $newVotes > 5) {
            return back()->withErrors([
                'candidate_ids' => "شما تا کنون {$already} رأی ثبت کرده‌اید؛ نمی‌توانید بیش از ۵ رأی داشته باشید."
            ]);
        }

        DB::transaction(function () use ($session, $hash, $data, $voter) {
            foreach ($data['candidate_ids'] ?? [] as $cid) {
                Vote::create([
                    'voting_session_id' => $session->id,
                    'hashed_voter_id'   => $hash,
                    'candidate_id'      => $cid,
                ]);
            }

            $ballot = Ballot::updateOrCreate(
                ['voting_session_id' => $session->id, 'voter_hash' => $hash],
                []
            );
            $ballot->candidates()->sync($data['candidate_ids'] ?? []);

            Verification::where([
                'voting_session_id' => $session->id,
                'voter_hash'        => $hash,
                'status'            => 'pending',
            ])->update(['status' => 'used']);

            // notify admins/operators/verifiers/voters
            $receivers = User::where(
                fn($q) => $q
                    ->where('is_admin', true)
                    ->orWhere('is_operator', true)
                    ->orWhere('is_verifier', true)
                    ->orWhere('is_voter', true)
            )->get();

            Notification::send(
                $receivers,
                new VoteCastNotification(
                    $data['voter_id'],
                    "{$voter->first_name} {$voter->last_name}",
                    $session->id
                )
            );
        });

        // reload the ballot
        $ballot = Ballot::with('candidates')
            ->where('voting_session_id', $session->id)
            ->where('voter_hash', $hash)
            ->firstOrFail();

        //
        // ─── MATCH endVoting() SETUP ────────────────────────────────────────
        //
        $configVars = (new ConfigVariables())->getDefaults();
        $fontDirs   = $configVars['fontDir'];
        $fontVars   = (new FontVariables())->getDefaults();
        $fontData   = $fontVars['fontdata'];

        $mpdf = new Mpdf([
            'mode'             => 'utf-8',
            'format'           => 'A4-P',
            'margin_left'      => 5,
            'margin_right'     => 5,
            'margin_top'       => 5,
            'margin_bottom'    => 5,
            'fontDir'          => array_merge($fontDirs, [storage_path('fonts')]),
            'fontdata'         => array_merge($fontData, [
                'vazirmatn' => [
                    'R'         => 'Vazirmatn-Regular.ttf',
                    'B'         => 'Vazirmatn-Bold.ttf',
                    'useOTL'    => 0xFF,
                    'useKashida' => 75,
                ],
            ]),
            'default_font'     => 'vazirmatn',
            'autoLangToFont'   => true,
            'autoScriptToLang' => true,
        ]);

        $mpdf->SetDirectionality('rtl');

        // load the full HTML (including <head> with your @font-face CSS)
        $fullHtml = view('admin.ballot-page', compact('session', 'ballot'))->render();

        // write HTML in BODY mode so head/styles are respected
        $mpdf->WriteHTML($fullHtml);

        $pdfContent = $mpdf->Output('', 'S');
        //
        // ────────────────────────────────────────────────────────────────────
        //

        if ($request->ajax()) {
            return response()->json([
                'print_url'    => route('vote.ballots.download', $ballot->id),
                'redirect_url' => route('votes.index'),
                'message'      => 'رأی‌های شما با موفقیت ثبت شدند.',
            ]);
        }

        return response($pdfContent, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=\"ballot_{$ballot->id}.pdf\"");
    }

    /**
     * Fallback download if JS/AJAX isn’t available.
     */
    public function download(Ballot $ballot)
    {
        // 1) Load session & candidates
        $session = VotingSession::findOrFail($ballot->voting_session_id);
        $ballot->load('candidates');

        // 2) mPDF font & RTL config (same as in endVoting)
        $configVars = (new ConfigVariables())->getDefaults();
        $fontDirs   = $configVars['fontDir'];
        $fontVars   = (new FontVariables())->getDefaults();
        $fontData   = $fontVars['fontdata'];

        $mpdf = new Mpdf([
            'mode'           => 'utf-8',
            'format'         => 'A4-P',
            'margin_left'    => 5,
            'margin_right'   => 5,
            'margin_top'     => 5,
            'margin_bottom'  => 5,
            'fontDir'        => array_merge($fontDirs, [storage_path('fonts')]),
            'fontdata'       => array_merge($fontData, [ /* … */]),
            'default_font'   => 'vazirmatn',
            'autoLangToFont' => true,
            'autoScriptToLang' => true,
        ]);
        $mpdf->SetDirectionality('rtl');

        // render the full template including its <head><style>…</head>
        $html = view('admin.ballot-page', compact('session', 'ballot'))->render();

        // write HTML so your in‐Blade CSS is applied
        $mpdf->WriteHTML($html);

        // stream it:
        return response(
            $mpdf->Output("ballot_{$ballot->id}.pdf", 'S'),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"ballot_{$ballot->id}.pdf\""
            ]
        );
    }
}
