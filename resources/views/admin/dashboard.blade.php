@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Admin Panel - Voting Session</h1>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Session Status --}}
    @if($session && $session->is_active)
        <div class="alert alert-info">
            <strong>Voting is currently ACTIVE.</strong><br>
            Started at: {{ $session->start_at->format('Y-m-d H:i') }}<br>
            @if($session->end_at)
                Ends at: {{ $session->end_at->format('Y-m-d H:i') }}
            @else
                No end time set.
            @endif
        </div>

        {{-- Stop Voting Button --}}
        <form action="{{ route('admin.stop') }}" method="POST">
            @csrf
            <button class="btn btn-danger">Stop Voting</button>
        </form>

    @else
        <div class="alert alert-warning">
            <strong>Voting is NOT active.</strong>
        </div>

        {{-- Start Voting Form --}}
        <form action="{{ route('admin.start') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="end_at" class="form-label">Optional End Time:</label>
                <input type="datetime-local" name="end_at" class="form-control">
            </div>
            <button class="btn btn-success">Start Voting</button>
        </form>
    @endif
</div>
@endsection
