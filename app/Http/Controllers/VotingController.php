<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use Illuminate\Http\Request;
use App\Models\User;

class VotingController extends Controller
{
    public function showForm()
    {
        return view('vote.form');
    }

    public function submitVote(Request $request)
    {
        $request->validate([
            'voter_id' => 'required|string',
            'candidate_id' => 'required|exists:users,id',
        ]);

        $hashedId = hash('sha256', $request->voter_id);

        // Prevent double voting
        if (Vote::where('hashed_voter_id', $hashedId)->exists()) {
            return redirect()->back()->withErrors(['voter_id' => 'رای با این شماره ملی ثبت شده است.']);
        }

        Vote::create([
            'hashed_voter_id' => $hashedId,
            'candidate_id' => $request->candidate_id,
        ]);

        return redirect()->route('vote.confirm');
    }
}
