<?php

namespace App\Exports;

use App\Models\Vote;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class VoteExport implements FromCollection
{
    public function collection(): Collection
    {
        return User::where('is_candidate', true)
            ->get()
            ->map(function ($candidate) {
                return [
                    'Candidate' => $candidate->name,
                    'Votes' => Vote::where('candidate_id', $candidate->id)->count()
                ];
            });
    }
}

