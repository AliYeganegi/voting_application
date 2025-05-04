<?php

namespace App\Exports;

use App\Models\Ballot;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\User;
use App\Models\Vote;
use App\Models\VotingSession;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VotingResultsExcelExport implements FromView
{
    protected $sessionId;

    public function __construct($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    public function view(): View
    {
        $results = User::where('is_candidate', true)
            ->withCount(['votes' => function ($query) {
                $query->where('voting_session_id', $this->sessionId);
            }])
            ->orderByDesc('votes_count')
            ->get();

            $session = VotingSession::find($this->sessionId);

        return view('admin.results-excel', [
            'results' => $results,
            'session' => $session
        ]);
    }
}
