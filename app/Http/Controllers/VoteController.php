<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ValidVoter;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VoteController extends Controller
{
    public function index()
    {
        $candidates = User::where('is_candidate', true)->get();
        return view('vote.index', compact('candidates'));
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'voter_id' => 'required',
            'candidate_id' => 'required|exists:users,id',
        ]);

        $voter = ValidVoter::where('voter_id', $request->voter_id)->first();

        if (!$voter || $voter->has_voted) {
            return back()->withErrors(['voter_id' => 'Invalid or already voted']);
        }

        return view('vote.confirm', [
            'voter_id' => $request->voter_id,
            'first_name' => $voter->first_name,
            'last_name' => $voter->last_name,
            'candidate' => User::find($request->candidate_id),
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'voter_id' => 'required',
            'candidate_id' => 'required|exists:users,id',
        ]);

        $voter = ValidVoter::where('voter_id', $request->voter_id)->first();

        if (!$voter || $voter->has_voted) {
            return back()->withErrors(['voter_id' => 'Invalid or already voted']);
        }

        Vote::create([
            'hashed_voter_id' => Hash::make($request->voter_id),
            'candidate_id' => $request->candidate_id,
        ]);

        $voter->update(['has_voted' => true]);

        return redirect()->route('vote.index')->with('success', 'Vote submitted!');
    }
}
