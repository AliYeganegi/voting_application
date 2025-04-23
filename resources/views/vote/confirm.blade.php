<h3>Confirm Vote</h3>
<p>Voter ID: {{ $voter_id }}</p>
<p>Candidate: {{ $candidate->name }}</p>

<form method="POST" action="{{ route('vote.submit') }}">
    @csrf
    <input type="hidden" name="voter_id" value="{{ $voter_id }}">
    <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
    <button type="submit">Confirm & Submit Vote</button>
</form>

<a href="{{ route('vote.index') }}">Cancel</a>
