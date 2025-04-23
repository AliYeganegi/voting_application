<form method="POST" action="{{ route('vote.confirm') }}">
    @csrf
    <label>Voter ID:</label>
    <input type="text" name="voter_id" required>

    <label>Select Candidate:</label>
    <select name="candidate_id" required>
        @foreach($candidates as $candidate)
            <option value="{{ $candidate->id }}">{{ $candidate->name }}</option>
        @endforeach
    </select>

    <button type="submit">Continue</button>
</form>
