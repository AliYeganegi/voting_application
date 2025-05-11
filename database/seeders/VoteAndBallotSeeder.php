<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ValidVoter;
use App\Models\Vote;
use App\Models\Ballot;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VoteAndBallotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sessionId = 1;

        // Get all candidate user IDs
        $candidateIds = User::where('is_candidate', true)
            ->pluck('id')
            ->toArray();

        // Iterate through each valid voter
        ValidVoter::all()->each(function (ValidVoter $voter) use ($sessionId, $candidateIds) {
            // Hash the voter ID for anonymity
            $hashedVoterId = hash('sha256', $voter->voter_id);

            // Create the ballot record linking voter to the session
            $ballot = Ballot::create([
                'voting_session_id' => $sessionId,
                'voter_hash'        => $hashedVoterId,
            ]);

            // Determine how many candidates this voter will vote for (0 to 5)
            $maxVotes = min(5, count($candidateIds));
            $voteCount = rand(0, $maxVotes);

            if ($voteCount > 0) {
                // Randomly select unique candidate IDs to vote for
                $chosenCandidates = collect($candidateIds)->random($voteCount);

                foreach ($chosenCandidates as $candidateId) {
                    // Create a vote record
                    Vote::create([
                        'hashed_voter_id'   => $hashedVoterId,
                        'candidate_id'      => $candidateId,
                        'voting_session_id' => $sessionId,
                    ]);

                    // Create a link in the pivot table ballot_candidate
                    DB::table('ballot_candidate')->insert([
                        'ballot_id'    => $ballot->id,
                        'candidate_id' => $candidateId,
                    ]);
                }
            }
        });
    }
}
