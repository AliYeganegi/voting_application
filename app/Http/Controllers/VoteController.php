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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class VoteController extends Controller
{
    public function index()
    {
        $candidates = User::where('is_candidate', true)->get();
        return view('vote.index', compact('candidates'));
    }

    public function confirm(Request $request)
    {
        // If they arrived via GET (after a validation redirect), just show the form
        if ($request->isMethod('get')) {
            return $this->index();
        }

        // Otherwise it’s a POST—continue with your validation & confirm logic…
        $data = $request->validate([
            'voter_id'        => 'required|string',
            'candidate_ids'   => 'nullable|array|max:5',
            'candidate_ids.*' => 'exists:users,id',
        ], [
            'candidate_ids.max'      => 'شما نمی‌توانید بیش از ۵ نامزد انتخاب کنید.',
            'candidate_ids.*.exists' => 'نامزد انتخاب‌شده نامعتبر است.',
        ]);

        // Lookup voter
        $voter = ValidVoter::where('voter_id', $data['voter_id'])->first();
        if (! $voter) {
            return back()->withErrors(['voter_id' => 'کد ملی نامعتبر است.']);
        }

        // Load selected candidates
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
        // 1) Validate input
        $data = $request->validate([
            'voter_id'        => 'required|string',
            'candidate_ids'   => 'nullable|array|max:5',
            'candidate_ids.*' => 'exists:users,id',
        ], [
            'candidate_ids.max'      => 'شما نمی‌توانید بیش از ۵ نامزد انتخاب کنید.',
            'candidate_ids.*.exists' => 'یک یا چند نامزد انتخاب‌شده نامعتبرند.',
        ]);

        // 2) Lookup voter
        $voter = ValidVoter::where('voter_id', $data['voter_id'])->first();
        if (! $voter) {
            return back()->withErrors(['voter_id' => 'کد ملی نامعتبر است.']);
        }

        // 3) Get active session
        $session = VotingSession::where('is_active', true)
            ->latest()
            ->firstOrFail();

        // 4) Compute stable hash
        $voterHash = hash('sha256', $data['voter_id']);

        // 5) Count existing votes this hash has in this session
        $alreadyCast = Vote::where('voting_session_id', $session->id)
            ->where('hashed_voter_id', $voterHash)
            ->count();

        // 6) Determine new vote usage (blank counts as 1)
        $countSelected = count($data['candidate_ids'] ?? []);
        $newVotes      = $countSelected > 0 ? $countSelected : 1;

        if ($alreadyCast + $newVotes > 5) {
            return back()->withErrors([
                'candidate_ids' => "شما تا کنون {$alreadyCast} رأی ثبت کرده‌اید؛ نمی‌توانید بیش از ۵ رأی داشته باشید."
            ]);
        }

        // 7) Record within a transaction
        DB::transaction(function () use ($session, $voterHash, $data, $voter) {
            // a) Create individual Vote rows
            foreach ($data['candidate_ids'] ?? [] as $candidateId) {
                Vote::create([
                    'hashed_voter_id'   => $voterHash,
                    'candidate_id'      => $candidateId,
                    'voting_session_id' => $session->id,
                ]);
            }

            // b) Create or update the Ballot and sync candidates
            $ballot = Ballot::updateOrCreate(
                [
                    'voting_session_id' => $session->id,
                    'voter_hash'        => $voterHash,
                ],
                []
            );
            $ballot->candidates()->sync($data['candidate_ids'] ?? []);

            // c) Mark this verification as used
            Verification::where('voting_session_id', $session->id)
                ->where('voter_hash', $voterHash)
                ->where('status', 'pending')
                ->update(['status' => 'used']);

            // 3d) Fire notifications
            $receivers = User::where(
                fn($q) => $q
                    ->where('is_admin', true)
                    ->orWhere('is_operator', true)
                    ->orWhere('is_verifier', true)
            )->get();

            Notification::send(
                $receivers,
                new VoteCastNotification(
                    $data['voter_id'],
                    "{$voter->first_name} {$voter->last_name}",
                    $session
                )
            );
        });

        // 8) Redirect with success
        return redirect()->route('vote.index')
            ->with('success', 'رأی‌های شما با موفقیت ثبت شدند.');
    }
}
